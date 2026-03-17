<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use App\Services\RefundService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefundTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_refund_service_updates_order_status(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => 49.99,
            'currency' => 'usd',
            'status' => OrderStatus::Paid,
        ]);

        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $service = new RefundService();
        $refundedOrder = $service->refund($order, 'Student requested');

        $this->assertEquals(OrderStatus::Refunded, $refundedOrder->status);
        $this->assertEquals('Student requested', $refundedOrder->metadata['refund_reason']);
    }

    public function test_refund_removes_enrollment(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->published()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => 49.99,
            'currency' => 'usd',
            'status' => OrderStatus::Paid,
        ]);

        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $service = new RefundService();
        $service->refund($order);

        $this->assertSoftDeleted('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_cannot_refund_already_refunded_order(): void
    {
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'course_id' => Course::factory()->published()->create()->id,
            'amount' => 49.99,
            'currency' => 'usd',
            'status' => OrderStatus::Refunded,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('already been refunded');

        (new RefundService())->refund($order);
    }

    public function test_cannot_refund_pending_order(): void
    {
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'course_id' => Course::factory()->published()->create()->id,
            'amount' => 49.99,
            'currency' => 'usd',
            'status' => OrderStatus::Pending,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Only paid orders');

        (new RefundService())->refund($order);
    }

    public function test_can_refund_returns_correct_boolean(): void
    {
        $paid = Order::create([
            'user_id' => User::factory()->create()->id,
            'course_id' => Course::factory()->published()->create()->id,
            'amount' => 49.99,
            'currency' => 'usd',
            'status' => OrderStatus::Paid,
        ]);

        $pending = Order::create([
            'user_id' => User::factory()->create()->id,
            'course_id' => Course::factory()->published()->create()->id,
            'amount' => 49.99,
            'currency' => 'usd',
            'status' => OrderStatus::Pending,
        ]);

        $service = new RefundService();
        $this->assertTrue($service->canRefund($paid));
        $this->assertFalse($service->canRefund($pending));
    }
}
