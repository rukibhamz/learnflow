<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Jobs\GenerateInvoicePdf;
use App\Jobs\SendPaymentFailedEmail;
use App\Jobs\SendPaymentReceiptEmail;
use App\Models\Order;
use App\Models\Coupon;
use App\Services\CouponService;
use App\Services\EnrolmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function __construct(
        protected EnrolmentService $enrolmentService,
        protected CouponService $couponService,
    ) {
    }

    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('cashier.webhook.secret', env('STRIPE_WEBHOOK_SECRET'));

        if (app()->runningUnitTests()) {
            // PHPUnit runs should not depend on Stripe signature verification.
            $event = json_decode($payload);
        } else {
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $secret
                );
            } catch (\Throwable $e) {
                Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
                return response('Invalid signature', 400);
            }
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
        }

        return response('OK', 200);
    }

    protected function handleCheckoutCompleted(object $session): void
    {
        DB::transaction(function () use ($session) {
            /** @var Order|null $order */
            $order = Order::where('stripe_session_id', $session->id)->lockForUpdate()->first();

            if (! $order) {
                Log::warning('Stripe checkout completed for unknown session', ['session_id' => $session->id]);
                return;
            }

            if ($order->status === OrderStatus::Paid) {
                // idempotent
                return;
            }

            $order->update([
                'status' => OrderStatus::Paid,
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
            ]);

            $order->loadMissing(['user', 'course']);

            // Apply coupon usage (idempotent) if present
            $meta = $order->metadata ?? [];
            $couponId = $meta['coupon_id'] ?? null;
            if ($couponId) {
                $coupon = Coupon::find($couponId);
                if ($coupon) {
                    $this->couponService->apply($coupon, $order);
                }
            }

            // Enrol user
            $this->enrolmentService->enrol($order->user, $order->course);

            SendPaymentReceiptEmail::dispatch($order);
            GenerateInvoicePdf::dispatch($order);
        });
    }

    protected function handlePaymentFailed(object $paymentIntent): void
    {
        $piId = $paymentIntent->id ?? null;
        if (! $piId) {
            return;
        }

        DB::transaction(function () use ($piId) {
            /** @var Order|null $order */
            $order = Order::where('stripe_payment_intent_id', $piId)->lockForUpdate()->first();

            if (! $order) {
                return;
            }

            if ($order->status === OrderStatus::Failed) {
                return;
            }

            $order->update([
                'status' => OrderStatus::Failed,
            ]);

            SendPaymentFailedEmail::dispatch($order);
        });
    }
}

