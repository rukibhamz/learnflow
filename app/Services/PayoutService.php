<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    public function calculateEarnings(User $instructor, string $from, string $to): array
    {
        $grossRevenue = Order::paid()
            ->whereHas('course', fn ($q) => $q->where('instructor_id', $instructor->id))
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        $sharePercent = $instructor->revenue_share_percent ?? 70;
        $instructorEarnings = (int) round($grossRevenue * ($sharePercent / 100));
        $platformFee = $grossRevenue - $instructorEarnings;

        return [
            'gross_revenue' => $grossRevenue,
            'instructor_earnings' => $instructorEarnings,
            'platform_fee' => $platformFee,
            'share_percent' => $sharePercent,
        ];
    }

    public function createPayout(User $instructor, string $periodStart, string $periodEnd, ?string $notes = null): ?Payout
    {
        $earnings = $this->calculateEarnings($instructor, $periodStart, $periodEnd);

        if ($earnings['instructor_earnings'] <= 0) {
            return null;
        }

        return Payout::create([
            'instructor_id' => $instructor->id,
            'amount' => $earnings['instructor_earnings'],
            'platform_fee' => $earnings['platform_fee'],
            'status' => 'pending',
            'method' => $instructor->stripe_connect_id ? 'stripe' : 'manual',
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'notes' => $notes,
        ]);
    }

    public function getInstructorsPendingPayouts(): Collection
    {
        return User::role('instructor')
            ->withSum(['payouts as total_paid' => fn ($q) => $q->paid()], 'amount')
            ->get()
            ->map(function (User $instructor) {
                $totalEarned = Order::paid()
                    ->whereHas('course', fn ($q) => $q->where('instructor_id', $instructor->id))
                    ->sum('amount');

                $sharePercent = $instructor->revenue_share_percent ?? 70;
                $totalDue = (int) round($totalEarned * ($sharePercent / 100));
                $totalPaid = (int) ($instructor->total_paid ?? 0);
                $balance = $totalDue - $totalPaid;

                return (object) [
                    'instructor' => $instructor,
                    'total_earned' => $totalEarned,
                    'total_due' => $totalDue,
                    'total_paid' => $totalPaid,
                    'balance' => $balance,
                    'share_percent' => $sharePercent,
                ];
            })
            ->filter(fn ($row) => $row->total_earned > 0)
            ->sortByDesc('balance');
    }
}
