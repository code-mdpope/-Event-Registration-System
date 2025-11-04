<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Registration;
use App\Models\Attendance;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        $registrations = Registration::where('user_id', $user->id)
            ->with('event')
            ->latest()
            ->paginate(10);

        $attendances = Attendance::where('user_id', $user->id)
            ->with('event')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_registrations' => Registration::where('user_id', $user->id)->count(),
            'approved_registrations' => Registration::where('user_id', $user->id)
                ->where('status', 'approved')->count(),
            'pending_registrations' => Registration::where('user_id', $user->id)
                ->where('status', 'pending')->count(),
            'total_attendances' => Attendance::where('user_id', $user->id)->count(),
        ];

        return view('user.dashboard', compact('registrations', 'attendances', 'stats'));
    }
}
