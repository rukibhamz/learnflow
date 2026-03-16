<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponModelTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('student');
    }

    public function test_active_coupon_is_valid(): void
    {
        $coupon = Coupon::factory()->create(['is_active' => true, 'minimum_amount' => null]);
        $this->assertTrue($coupon->isValidFor($this->user, 100.0));
    }

    public function test_inactive_coupon_is_invalid(): void
    {
        $coupon = Coupon::factory()->inactive()->create();
        $this->assertFalse($coupon->isValidFor($this->user, 100.0));
    }

    public function test_expired_coupon_is_invalid(): void
    {
        $coupon = Coupon::factory()->expired()->create();
        $this->assertFalse($coupon->isValidFor($this->user, 100.0));
    }

    public function test_coupon_with_max_uses_reached_is_invalid(): void
    {
        $coupon = Coupon::factory()->create(['max_uses' => 5, 'used_count' => 5]);
        $this->assertFalse($coupon->isValidFor($this->user, 100.0));
    }

    public function test_coupon_with_uses_remaining_is_valid(): void
    {
        $coupon = Coupon::factory()->create(['max_uses' => 5, 'used_count' => 4]);
        $this->assertTrue($coupon->isValidFor($this->user, 100.0));
    }

    public function test_coupon_fails_minimum_amount_check(): void
    {
        $coupon = Coupon::factory()->create(['minimum_amount' => 50.00]);
        $this->assertFalse($coupon->isValidFor($this->user, 30.0));
    }

    public function test_coupon_passes_minimum_amount_check(): void
    {
        $coupon = Coupon::factory()->create(['minimum_amount' => 50.00]);
        $this->assertTrue($coupon->isValidFor($this->user, 50.0));
    }

    public function test_coupon_with_no_expiry_is_valid(): void
    {
        $coupon = Coupon::factory()->create(['expires_at' => null]);
        $this->assertTrue($coupon->isValidFor($this->user, 100.0));
    }

    public function test_coupon_with_future_expiry_is_valid(): void
    {
        $coupon = Coupon::factory()->create(['expires_at' => now()->addDays(7)]);
        $this->assertTrue($coupon->isValidFor($this->user, 100.0));
    }
}
