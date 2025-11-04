<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Registration;
use App\Models\Attendance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_events' => Event::count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_registrations' => Registration::count(),
            'total_attendances' => Attendance::count(),
            'attendance_rate' => $this->calculateOverallAttendanceRate(),
            'upcoming_events' => Event::upcoming()->count(),
            'recent_registrations' => Registration::with(['user', 'event'])->latest()->take(5)->get(),
            'recent_events' => Event::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    private function calculateOverallAttendanceRate(): float
    {
        $approvedRegistrations = Registration::where('status', 'approved')->count();
        if ($approvedRegistrations == 0) {
            return 0;
        }
        
        $attendances = Attendance::count();
        return round(($attendances / $approvedRegistrations) * 100, 2);
    }
}
