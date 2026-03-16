<?php

namespace Database\Factories;

use App\Enums\DiscountType;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'name' => fake()->words(3, true),
            'discount_type' => DiscountType::Percentage,
            'amount' => fake()->randomFloat(2, 5, 50),
            'max_uses' => null,
            'used_count' => 0,
            'minimum_amount' => null,
            'expires_at' => null,
            'is_active' => true,
        ];
    }

    public function fixed(): static
    {
        return $this->state(['discount_type' => DiscountType::Fixed]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }
}
