<?php

namespace App\Rules;

use App\Models\Coupon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueCouponCode implements ValidationRule
{
    public function __construct(
        protected ?int $ignoreId = null
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $code = trim((string) $value);
        if ($code === '') {
            return;
        }

        $query = Coupon::whereRaw('LOWER(code) = ?', [mb_strtolower($code)]);
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('This coupon code is already in use.');
        }
    }
}

