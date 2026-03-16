<?php

namespace App\ValueObjects;

class CouponValidationResult
{
    public function __construct(
        public bool $valid,
        public float $discount_amount = 0.0,
        public string $message = '',
        public ?int $coupon_id = null,
        public ?string $code = null,
    ) {
    }
}

