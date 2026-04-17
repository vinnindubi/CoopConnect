<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
class AnnouncementController extends Controller
{
    
// Fetch only active announcements
    public function index()
    {
     
        $announcements = Announcement::where('is_active', true)->latest()->get();
        return response()->json($announcements);
    }

    // Create a new broadcast
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'severity' => 'required|string',
            'message' => 'required|string',
            'action_text' => 'nullable|string',
            'action_link' => 'nullable|string',
        ]);

        // Optional: Automatically deactivate older alerts so only 1 is active at a time
        Announcement::where('is_active', true)->update(['is_active' => false]);

        $announcement = Announcement::create($validated);

        return response()->json(['message' => 'Broadcast sent!', 'data' => $announcement]);
    }

    // Deactivate an alert
    public function deactivate($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update(['is_active' => false]);
        return response()->json(['message' => 'Broadcast deactivated.']);
    }
}