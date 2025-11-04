<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['user', 'event']);

        // Filter by event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Search by user name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $attendances = $query->latest()->paginate(15);
        $events = Event::pluck('title', 'id');

        return view('admin.attendances.index', compact('attendances', 'events'));
    }

    /**
     * Show attendance management for a specific event
     */
    public function manage(Event $event)
    {
        $event->load(['registrations.user', 'attendances.user']);
        
        // Get approved registrations for this event
        $approvedRegistrations = $event->registrations()
            ->where('status', 'approved')
            ->with('user')
            ->get();

        // Get already marked attendances
        $markedAttendances = $event->attendances()->pluck('user_id')->toArray();

        return view('admin.attendances.manage', compact('event', 'approvedRegistrations', 'markedAttendances'));
    }

    /**
     * Mark attendance for a user in an event
     */
    public function mark(Request $request, Event $event)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if user has approved registration
        $registration = Registration::where('event_id', $event->id)
            ->where('user_id', $request->user_id)
            ->where('status', 'approved')
            ->first();

        if (!$registration) {
            return redirect()->route('admin.attendances.manage', $event)
                ->with('error', 'User does not have an approved registration for this event.');
        }

        // Check if already marked
        $existing = Attendance::where('event_id', $event->id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existing) {
            return redirect()->route('admin.attendances.manage', $event)
                ->with('error', 'Attendance already marked for this user.');
        }

        Attendance::create([
            'event_id' => $event->id,
            'user_id' => $request->user_id,
            'checked_in_at' => now(),
        ]);

        $this->logActivity('Attendance Marked', "Marked attendance for user ID {$request->user_id} in event: {$event->title}");

        return redirect()->route('admin.attendances.manage', $event)
            ->with('success', 'Attendance marked successfully.');
    }

    /**
     * Unmark attendance for a user in an event
     */
    public function unmark(Event $event, $userId)
    {
        $attendance = Attendance::where('event_id', $event->id)
            ->where('user_id', $userId)
            ->first();

        if ($attendance) {
            $attendance->delete();
            $this->logActivity('Attendance Unmarked', "Unmarked attendance for user ID {$userId} in event: {$event->title}");
            
            return redirect()->route('admin.attendances.manage', $event)
                ->with('success', 'Attendance unmarked successfully.');
        }

        return redirect()->route('admin.attendances.manage', $event)
            ->with('error', 'Attendance not found.');
    }

    /**
     * Display attendance statistics for an event
     */
    public function statistics(Event $event)
    {
        $event->load(['registrations', 'attendances']);
        
        $stats = [
            'total_registrations' => $event->registrations->count(),
            'approved_registrations' => $event->registrations->where('status', 'approved')->count(),
            'pending_registrations' => $event->registrations->where('status', 'pending')->count(),
            'declined_registrations' => $event->registrations->where('status', 'declined')->count(),
            'attendees_count' => $event->attendances->count(),
            'attendance_rate' => $event->attendance_rate,
        ];

        return view('admin.attendances.statistics', compact('event', 'stats'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $eventTitle = $attendance->event->title;
        $attendance->delete();

        $this->logActivity('Attendance Deleted', "Deleted attendance for event: {$eventTitle}");

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance deleted successfully.');
    }
}
