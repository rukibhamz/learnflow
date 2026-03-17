<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class RefundService
{
    /**
     * Process a full refund for an order.
     */
    public function refund(Order $order, ?string $reason = null): Order
    {
        if ($order->status === OrderStatus::Refunded) {
            throw new \Exception('This order has already been refunded.');
        }

        if ($order->status !== OrderStatus::Paid) {
            throw new \Exception('Only paid orders can be refunded.');
        }

        return DB::transaction(function () use ($order, $reason) {
            if ($order->stripe_payment_intent_id) {
                $stripe = new StripeClient(config('cashier.secret'));
                $stripe->refunds->create([
                    'payment_intent' => $order->stripe_payment_intent_id,
                    'reason' => 'requested_by_customer',
                ]);
            }

            $order->update([
                'status' => OrderStatus::Refunded,
                'metadata' => array_merge($order->metadata ?? [], [
                    'refund_reason' => $reason,
                    'refunded_at' => now()->toISOString(),
                ]),
            ]);

            Enrollment::where('user_id', $order->user_id)
                ->where('course_id', $order->course_id)
                ->delete();

            return $order->fresh();
        });
    }

    public function canRefund(Order $order): bool
    {
        return $order->status === OrderStatus::Paid;
    }
}
