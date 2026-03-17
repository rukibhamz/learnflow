<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionController extends Controller
{
    public function pricing()
    {
        $plans = SubscriptionPlan::active()->get();
        $user = auth()->user();
        $currentPlan = null;

        if ($user && $user->subscribed('default')) {
            $stripePriceId = $user->subscription('default')->stripe_price;
            $currentPlan = SubscriptionPlan::where('stripe_monthly_price_id', $stripePriceId)
                ->orWhere('stripe_yearly_price_id', $stripePriceId)
                ->first();
        }

        return view('pricing', [
            'plans' => $plans,
            'currentPlan' => $currentPlan,
        ]);
    }

    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $user = $request->user();
        $priceId = $request->billing_cycle === 'yearly'
            ? $plan->stripe_yearly_price_id
            : $plan->stripe_monthly_price_id;

        if (!$priceId) {
            return back()->with('error', 'This billing cycle is not available for the selected plan.');
        }

        try {
            $checkout = $user->newSubscription('default', $priceId)
                ->checkout([
                    'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('pricing'),
                ]);

            return redirect($checkout->url);
        } catch (IncompletePayment $e) {
            return redirect()->route('cashier.payment', [
                $e->payment->id,
                'redirect' => route('pricing'),
            ]);
        }
    }

    public function success(Request $request)
    {
        return view('subscription.success');
    }

    public function portal(Request $request)
    {
        return $request->user()->redirectToBillingPortal(route('dashboard'));
    }

    public function cancel(Request $request)
    {
        $request->user()->subscription('default')?->cancel();

        return back()->with('success', 'Your subscription has been cancelled. You\'ll retain access until the end of your billing period.');
    }

    public function resume(Request $request)
    {
        $subscription = $request->user()->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume();
            return back()->with('success', 'Your subscription has been resumed.');
        }

        return back()->with('error', 'Unable to resume subscription.');
    }
}
