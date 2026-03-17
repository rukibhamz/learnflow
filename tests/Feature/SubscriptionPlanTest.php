<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_can_be_created_with_auto_slug()
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Pro Monthly',
            'price_monthly' => 1999,
            'features' => ['Unlimited courses', 'Priority support'],
        ]);

        $this->assertEquals('pro-monthly', $plan->slug);
        $this->assertDatabaseHas('subscription_plans', ['slug' => 'pro-monthly']);
    }

    public function test_active_scope_returns_only_active_plans()
    {
        SubscriptionPlan::create(['name' => 'Active', 'slug' => 'active', 'price_monthly' => 999, 'is_active' => true]);
        SubscriptionPlan::create(['name' => 'Inactive', 'slug' => 'inactive', 'price_monthly' => 499, 'is_active' => false]);

        $this->assertCount(1, SubscriptionPlan::active()->get());
    }

    public function test_formatted_prices()
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Test',
            'slug' => 'test',
            'price_monthly' => 2999,
            'price_yearly' => 29990,
        ]);

        $this->assertEquals('$29.99', $plan->formattedMonthlyPrice());
        $this->assertEquals('$299.90', $plan->formattedYearlyPrice());
    }

    public function test_yearly_savings_calculation()
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Savings',
            'slug' => 'savings',
            'price_monthly' => 1000,
            'price_yearly' => 8400,
        ]);

        $this->assertEquals(30, $plan->yearlyMonthlySavings());
    }

    public function test_features_cast_to_array()
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Features',
            'slug' => 'features',
            'price_monthly' => 500,
            'features' => ['Feature A', 'Feature B', 'Feature C'],
        ]);

        $plan->refresh();
        $this->assertIsArray($plan->features);
        $this->assertCount(3, $plan->features);
    }

    public function test_pricing_page_loads()
    {
        SubscriptionPlan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'price_monthly' => 999,
            'is_active' => true,
        ]);

        $response = $this->get(route('pricing'));
        $response->assertOk();
        $response->assertSee('Basic');
    }
}
