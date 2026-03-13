    public function getStatsProperty()
    {
        return [
            'total_users' => \App\Models\User::count(),
            'total_courses' => \App\Models\Course::count(),
            'total_enrollments' => \App\Models\Enrollment::count(),
            'total_revenue' => \App\Models\Enrollment::count() * 49.00, // Mock
            'pending_reviews' => \App\Models\Course::where('status', 'review')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
