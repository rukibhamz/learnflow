    public function getMetricsProperty()
    {
        $instructor = auth()->user();
        $courseIds = $instructor->courses()->pluck('id');
        
        return [
            'total_students' => \App\Models\Enrollment::whereIn('course_id', $courseIds)->count(),
            'total_courses' => $courseIds->count(),
            'total_revenue' => \App\Models\Enrollment::whereIn('course_id', $courseIds)->count() * 49.00, // Mock price
            'avg_rating' => 4.8,
        ];
    }

    public function getRecentEnrollmentsProperty()
    {
        $courseIds = auth()->user()->courses()->pluck('id');
        return \App\Models\Enrollment::whereIn('course_id', $courseIds)
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
