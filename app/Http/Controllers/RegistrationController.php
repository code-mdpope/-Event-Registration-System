<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    /**
     * Store a newly created registration
     */
    public function store(Request $request, Event $event)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to register for events.');
        }

        // Check if event is available for registration
        if (!$event->isAvailableForRegistration()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'This event is not available for registration.');
        }

        // Check for duplicate registration
        $existingRegistration = Registration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRegistration) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You have already registered for this event.');
        }

        // Check capacity
        if ($event->approved_registrations_count >= $event->capacity) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Event capacity has been reached.');
        }

        // Create registration
        Registration::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'registration_date' => now(),
            'status' => 'pending',
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', 'Registration successful! Please wait for admin approval.');
    }

    /**
     * Cancel a registration
     */
    public function cancel(Registration $registration)
    {
        // Ensure user owns this registration
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if registration can be cancelled
        if (!$registration->canBeCancelled()) {
            return redirect()->route('dashboard')
                ->with('error', 'This registration cannot be cancelled.');
        }

        $registration->status = 'cancelled';
        $registration->save();

        return redirect()->route('dashboard')
            ->with('success', 'Registration cancelled successfully.');
    }
}
