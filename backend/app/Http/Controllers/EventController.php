<?php

namespace App\Http\Controllers;

use App\Models\GroupEvent; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index()
    {
        // Get all events from today onwards, ordered by date.
        // We use "with('group')" so we know which club is hosting it!
        $events = GroupEvent::with('group')
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date', 'asc')
            ->get();

        return response()->json($events);
    }
    public function storeGlobalEvent(Request $request)
    {
        // Optional: Security check to ensure the user is a system admin
        // if ($request->user()->role !== 'admin') {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        $allowedCategories = [
            'Academic', 'Social', 'Sports', 'Career' // Add your constants here
        ];

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_type' => ['required', 'string', Rule::in($allowedCategories)],
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'organizer' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'description' => 'required|string',
        ]);

        // Create the event WITHOUT a group_id (Making it a Global Event)
        $event = GroupEvent::create([
            'group_id' => null, // The magic happens here!
            'title' => $validated['title'],
            'event_type' => $validated['event_type'],
            'event_date' => $validated['event_date'],
            'location' => $validated['location'] ?? 'Campus (TBA)',
            'price' => $validated['price'] ?? 0,
            'organizer' => $validated['organizer'] ?? 'Campus Connect',
            'image' => $validated['image'],
            'description' => $validated['description'],
            'is_featured' => true, // Let's make all global admin events featured by default!
        ]);

        return response()->json([
            'message' => 'Global event published successfully!',
            'event' => $event
        ], 201);
    }
    /**
     * Delete an Event (System Admin Only)
     */
    public function destroyGlobalEvent($id)
    {
        // Optional: Security check for admin role
        
        $event = GroupEvent::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully.']);
    }
    /**
     * Toggle the 'is_featured' status of an event
     */
    public function toggleFeatureStatus($id)
    {
        // Find the event
        $event = GroupEvent::findOrFail($id);
        
        // Flip the boolean (if true, make false. If false, make true)
        $event->is_featured = !$event->is_featured;
        $event->save();

        $status = $event->is_featured ? 'promoted to featured' : 'removed from featured';

        return response()->json([
            'message' => "Event successfully $status!",
            'is_featured' => $event->is_featured
        ]);
    }

}
