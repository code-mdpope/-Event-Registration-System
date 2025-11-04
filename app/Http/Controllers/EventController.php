<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of events
     */
    public function index(Request $request)
    {
        $query = Event::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by status (default to upcoming)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->upcoming();
        }

        $events = $query->latest()->paginate(12);

        return view('events.index', compact('events'));
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        $event->load(['registrations.user']);
        
        // Check if user is registered
        $isRegistered = false;
        $userRegistration = null;
        
        if (auth()->check()) {
            $userRegistration = $event->registrations()
                ->where('user_id', auth()->id())
                ->first();
            $isRegistered = $userRegistration !== null;
        }

        return view('events.show', compact('event', 'isRegistered', 'userRegistration'));
    }
}
