<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsExportController extends Controller
{
    public function export(Request $request, string $type): StreamedResponse
    {
        return match ($type) {
            'revenue' => $this->exportRevenue($request),
            'enrollments' => $this->exportEnrollments($request),
            default => abort(404),
        };
    }

    private function exportRevenue(Request $request): StreamedResponse
    {
        $days = (int) ($request->query('days', 90));

        return new StreamedResponse(function () use ($days) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Order ID', 'Student', 'Course', 'Amount', 'Status', 'Payment Method']);

            Order::where('created_at', '>=', now()->subDays($days))
                ->with(['user:id,name,email', 'course:id,title'])
                ->orderByDesc('created_at')
                ->chunk(200, function ($orders) use ($handle) {
                    foreach ($orders as $order) {
                        fputcsv($handle, [
                            $order->created_at->toDateString(),
                            $order->id,
                            $order->user?->name ?? 'Deleted',
                            $order->course?->title ?? 'Deleted',
                            number_format($order->amount / 100, 2),
                            $order->status->value ?? $order->status,
                            'Stripe',
                        ]);
                    }
                });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="revenue-export-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    private function exportEnrollments(Request $request): StreamedResponse
    {
        $days = (int) ($request->query('days', 90));

        return new StreamedResponse(function () use ($days) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Student', 'Email', 'Course', 'Progress %', 'Completed At', 'Expires At']);

            Enrollment::where('created_at', '>=', now()->subDays($days))
                ->with(['user:id,name,email', 'course:id,title'])
                ->orderByDesc('created_at')
                ->chunk(200, function ($enrollments) use ($handle) {
                    foreach ($enrollments as $enrollment) {
                        fputcsv($handle, [
                            $enrollment->created_at->toDateString(),
                            $enrollment->user?->name ?? 'Deleted',
                            $enrollment->user?->email ?? '',
                            $enrollment->course?->title ?? 'Deleted',
                            $enrollment->progress_percentage ?? 0,
                            $enrollment->completed_at?->toDateString() ?? '',
                            $enrollment->expires_at?->toDateString() ?? 'Never',
                        ]);
                    }
                });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="enrollments-export-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
