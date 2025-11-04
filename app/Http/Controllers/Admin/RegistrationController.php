<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationApproved;
use App\Mail\RegistrationDeclined;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Registration::with(['user', 'event']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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

        $registrations = $query->latest()->paginate(15);
        $events = Event::pluck('title', 'id');

        return view('admin.registrations.index', compact('registrations', 'events'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Registration $registration)
    {
        $registration->load(['user', 'event']);
        return view('admin.registrations.show', compact('registration'));
    }

    /**
     * Approve a registration
     */
    public function approve(Registration $registration)
    {
        if ($registration->status !== 'pending') {
            return redirect()->route('admin.registrations.index')
                ->with('error', 'Only pending registrations can be approved.');
        }

        // Check event capacity
        $event = $registration->event;
        if ($event->approved_registrations_count >= $event->capacity) {
            return redirect()->route('admin.registrations.index')
                ->with('error', 'Event capacity reached. Cannot approve more registrations.');
        }

        $registration->status = 'approved';
        $registration->save();

        $this->logActivity('Registration Approved', "Approved registration for {$registration->user->name} to {$event->title}");

        // Send email notification
        try {
            Mail::to($registration->user->email)->send(new RegistrationApproved($registration));
        } catch (\Exception $e) {
            // Log error but don't fail the operation
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Registration approved successfully.');
    }

    /**
     * Decline a registration
     */
    public function decline(Registration $registration)
    {
        if ($registration->status !== 'pending') {
            return redirect()->route('admin.registrations.index')
                ->with('error', 'Only pending registrations can be declined.');
        }

        $registration->status = 'declined';
        $registration->save();

        $this->logActivity('Registration Declined', "Declined registration for {$registration->user->name} to {$registration->event->title}");

        // Send email notification
        try {
            Mail::to($registration->user->email)->send(new RegistrationDeclined($registration));
        } catch (\Exception $e) {
            // Log error but don't fail the operation
            \Log::error('Failed to send decline email: ' . $e->getMessage());
        }

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Registration declined successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Registration $registration)
    {
        $userName = $registration->user->name;
        $eventTitle = $registration->event->title;
        $registration->delete();

        $this->logActivity('Registration Deleted', "Deleted registration for {$userName} to {$eventTitle}");

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Registration deleted successfully.');
    }
}
