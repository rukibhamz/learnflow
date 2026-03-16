<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use App\ValueObjects\CouponValidationResult;
use Illuminate\Support\Facades\DB;

class CouponService
{
    public function validate(string $code, User $user, float $orderAmount): CouponValidationResult
    {
        $code = trim($code);

        if ($code === '') {
            return new CouponValidationResult(false, 0.0, 'Please enter a coupon code.');
        }

        /** @var Coupon|null $coupon */
        $coupon = Coupon::whereRaw('LOWER(code) = ?', [mb_strtolower($code)])->first();

        if (! $coupon) {
            return new CouponValidationResult(false, 0.0, 'Invalid coupon code.');
        }

        if (! $coupon->is_active) {
            return new CouponValidationResult(false, 0.0, 'This coupon is not active.', $coupon->id, $coupon->code);
        }

        if ($coupon->expires_at !== null && $coupon->expires_at->isPast()) {
            return new CouponValidationResult(false, 0.0, 'This coupon has expired.', $coupon->id, $coupon->code);
        }

        if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
            return new CouponValidationResult(false, 0.0, 'This coupon has reached its usage limit.', $coupon->id, $coupon->code);
        }

        if ($coupon->minimum_amount !== null && $orderAmount < (float) $coupon->minimum_amount) {
            return new CouponValidationResult(
                false,
                0.0,
                'Minimum order amount for this coupon is $' . number_format((float) $coupon->minimum_amount, 2) . '.',
                $coupon->id,
                $coupon->code
            );
        }

        $alreadyUsed = CouponUsage::where('coupon_id', $coupon->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyUsed) {
            return new CouponValidationResult(false, 0.0, 'You have already used this coupon.', $coupon->id, $coupon->code);
        }

        $discountAmount = $this->discountAmount($coupon, $orderAmount);

        if ($discountAmount <= 0) {
            return new CouponValidationResult(false, 0.0, 'This coupon does not apply to this order.', $coupon->id, $coupon->code);
        }

        return new CouponValidationResult(true, $discountAmount, 'Coupon applied.', $coupon->id, $coupon->code);
    }

    public function discountAmount(Coupon $coupon, float $amount): float
    {
        if ($coupon->discount_type->value === 'fixed') {
            return round(min($amount, (float) $coupon->amount), 2);
        }

        // percentage
        return round(max(0, $amount * ((float) $coupon->amount / 100)), 2);
    }

    /**
     * Apply a coupon to an order (idempotent).
     */
    public function apply(Coupon $coupon, Order $order): void
    {
        DB::transaction(function () use ($coupon, $order) {
            // If already applied to this order, do nothing (idempotent)
            $exists = CouponUsage::where('order_id', $order->id)
                ->where('coupon_id', $coupon->id)
                ->exists();

            if ($exists) {
                return;
            }

            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'used_at' => now(),
            ]);

            // Avoid race conditions on used_count
            Coupon::where('id', $coupon->id)->update([
                'used_count' => DB::raw('used_count + 1'),
            ]);
        });
    }
}

