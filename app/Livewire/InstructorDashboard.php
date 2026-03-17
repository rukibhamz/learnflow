<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Review;
use Livewire\Component;

class InstructorDashboard extends Component
{
    public function getMetricsProperty(): array
    {
        $instructor = auth()->user();
        $courseIds = Course::where('instructor_id', $instructor->id)->pluck('id');

        return [
            'total_students' => Enrollment::whereIn('course_id', $courseIds)->count(),
            'total_courses' => $courseIds->count(),
            'total_revenue' => Order::whereIn('course_id', $courseIds)->paid()->sum('amount'),
            'avg_rating' => Review::whereIn('course_id', $courseIds)
                ->whereNotNull('approved_at')->avg('rating') ?? 0,
        ];
    }

    public function getRecentEnrollmentsProperty()
    {
        $courseIds = Course::where('instructor_id', auth()->id())->pluck('id');

        return Enrollment::whereIn('course_id', $courseIds)
            ->with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.instructor-dashboard');
    }
}
