<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Order;
use App\Models\Payout;
use App\Models\User;
use App\Services\PayoutService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_payout_can_be_created()
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $payout = Payout::create([
            'instructor_id' => $instructor->id,
            'amount' => 5000,
            'platform_fee' => 2143,
            'status' => 'pending',
            'method' => 'manual',
            'period_start' => '2026-03-01',
            'period_end' => '2026-03-31',
        ]);

        $this->assertDatabaseHas('payouts', ['id' => $payout->id]);
        $this->assertEquals('$50.00', $payout->formattedAmount());
        $this->assertEquals('$21.43', $payout->formattedFee());
    }

    public function test_payout_can_be_marked_paid()
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $payout = Payout::create([
            'instructor_id' => $instructor->id,
            'amount' => 3000,
            'platform_fee' => 1000,
            'status' => 'pending',
            'period_start' => '2026-03-01',
            'period_end' => '2026-03-31',
        ]);

        $payout->markPaid('tr_123');

        $payout->refresh();
        $this->assertEquals('paid', $payout->status);
        $this->assertNotNull($payout->paid_at);
        $this->assertEquals('tr_123', $payout->stripe_transfer_id);
    }

    public function test_payout_belongs_to_instructor()
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $payout = Payout::create([
            'instructor_id' => $instructor->id,
            'amount' => 1000,
            'platform_fee' => 500,
            'status' => 'pending',
            'period_start' => '2026-03-01',
            'period_end' => '2026-03-31',
        ]);

        $this->assertEquals($instructor->id, $payout->instructor->id);
    }

    public function test_payout_service_calculates_earnings()
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $instructor->forceFill(['revenue_share_percent' => 70])->save();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        Order::create([
            'user_id' => User::factory()->create()->id,
            'course_id' => $course->id,
            'amount' => 10000,
            'status' => 'paid',
        ]);

        $service = new PayoutService();
        $earnings = $service->calculateEarnings($instructor, now()->subMonth()->toDateString(), now()->toDateString());

        $this->assertEquals(10000, $earnings['gross_revenue']);
        $this->assertEquals(7000, $earnings['instructor_earnings']);
        $this->assertEquals(3000, $earnings['platform_fee']);
        $this->assertEquals(70, $earnings['share_percent']);
    }

    public function test_payout_service_creates_payout()
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $instructor->forceFill(['revenue_share_percent' => 80])->save();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        Order::create([
            'user_id' => User::factory()->create()->id,
            'course_id' => $course->id,
            'amount' => 5000,
            'status' => 'paid',
        ]);

        $service = new PayoutService();
        $payout = $service->createPayout($instructor, now()->subMonth()->toDateString(), now()->toDateString());

        $this->assertNotNull($payout);
        $this->assertEquals(4000, $payout->amount);
        $this->assertEquals(1000, $payout->platform_fee);
    }

    public function test_payout_service_returns_null_for_no_earnings()
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');

        $service = new PayoutService();
        $payout = $service->createPayout($instructor, now()->subMonth()->toDateString(), now()->toDateString());

        $this->assertNull($payout);
    }
}
