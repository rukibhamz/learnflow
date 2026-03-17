<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function getStatsProperty(): array
    {
        return [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'total_revenue' => Order::paid()->sum('amount'),
            'pending_reviews' => Course::where('status', 'review')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
