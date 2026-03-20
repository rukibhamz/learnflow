<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'amount' => 49.00,
            'currency' => 'USD',
            'stripe_payment_intent_id' => null,
            'stripe_session_id' => null,
            'status' => OrderStatus::Pending,
            'metadata' => [],
        ];
    }
}

