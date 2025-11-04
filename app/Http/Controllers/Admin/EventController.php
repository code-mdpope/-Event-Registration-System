<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $events = $query->latest()->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'capacity' => 'required|integer|min:1',
            'status' => ['required', Rule::in(['upcoming', 'ongoing', 'completed', 'cancelled'])],
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('banner_image')) {
            $imagePath = $request->file('banner_image')->store('events', 'public');
            $validated['banner_image'] = basename($imagePath);
        }

        $event = Event::create($validated);

        $this->logActivity('Event Created', "Created event: {$event->title}");

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load(['registrations.user', 'attendances.user']);
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'capacity' => 'required|integer|min:1',
            'status' => ['required', Rule::in(['upcoming', 'ongoing', 'completed', 'cancelled'])],
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('banner_image')) {
            // Delete old image
            if ($event->banner_image) {
                Storage::disk('public')->delete('events/' . $event->banner_image);
            }
            $imagePath = $request->file('banner_image')->store('events', 'public');
            $validated['banner_image'] = basename($imagePath);
        } else {
            unset($validated['banner_image']);
        }

        $event->update($validated);

        $this->logActivity('Event Updated', "Updated event: {$event->title}");

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Delete banner image if exists
        if ($event->banner_image) {
            Storage::disk('public')->delete('events/' . $event->banner_image);
        }

        $eventTitle = $event->title;
        $event->delete();

        $this->logActivity('Event Deleted', "Deleted event: {$eventTitle}");

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
