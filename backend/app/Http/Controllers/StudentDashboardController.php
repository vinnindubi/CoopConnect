<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeedPost;
use Carbon\Carbon;
class StudentDashboardController extends Controller
{
// 1. The Main Feed
    public function getFeed()
    {
        // 1. Eager Load 'feedable' AND the nested 'group' relationship
        // This ensures Laravel fetches everything in just 2-3 fast queries total.
        $feed = FeedPost::with(['feedable', 'feedable.group'])->latest()->take(50)->get();

        $formattedFeed = $feed->map(function ($post) {
            $item = $post->feedable;

            // Failsafe: Skip if the original event was deleted
            if (!$item) return null;

            if ($post->feedable_type === 'App\Models\GroupEvent') {
                
                // --- SCENARIO A: Admin Post (group_id is null) ---
                if (is_null($item->group_id)) {
                    return [
                        'id' => $post->id,
                        'isImagePost' => !is_null($item->image), // Will be true if an image exists
                        'image' => $item->image, // Passes the image URL to Vue
                        'category' => 'Official Announcement',
                        'categoryColor' => 'error', // Red styling
                        'icon' => 'mdi-shield-crown-outline', // Admin-like icon
                        'iconBg' => 'red-lighten-5',
                        'timestamp' => $post->created_at->diffForHumans(),
                        'title' => $item->title,
                        'content' => $item->description,
                    ];
                } 
                
                // --- SCENARIO B: Student/Club Event (has a group_id) ---
                else {
                    // Grab the group name. Fallback to 'Campus' if the group was deleted.
                    $groupName = $item->group ? $item->group->name : 'Campus';

                    return [
                        'id' => $post->id,
                        'isImagePost' => !is_null($item->image),
                        'image' => $item->image,
                        'category' => $groupName, // e.g., "Tech Club Event"
                        'categoryColor' => 'primary', // Blue styling
                        'icon' => 'mdi-calendar-star',
                        'iconBg' => 'blue-lighten-5',
                        'timestamp' => $post->created_at->diffForHumans(),
                        'title' => $item->title,
                        'content' => $item->description . ' — 📍 Location: ' . ($item->location ?? 'TBA') . ' | 📅 Date: ' . Carbon::parse($item->event_date)->format('M d, Y'),
                    ];
                }
            }

            return null;
        });

        // 3. Filter out nulls and return clean JSON
        return response()->json($formattedFeed->filter()->values());
    }
    
    // 2. Upcoming Events
    public function getUpcomingEvents()
    {
        // Returning an empty array for now
        return response()->json([]);
    }
    public function getMarketplacePreview()
    {
        // Returning an empty array for now
        return response()->json([]);
    }







//     public function getFeed()
// {
//     // Fetch the latest 20 items from the ledger, AND eager-load the actual content
//     $feed = FeedPost::with('feedable')->latest()->paginate(20);

//     // Map the raw data into a clean, uniform format for your Vue frontend
//     $formattedFeed = $feed->map(function ($post) {
//         $item = $post->feedable; // This will automatically be an Event, Article, or AdminPost object!

//         // If it's an EVENT
//         if ($post->feedable_type === 'App\Models\Event') {
//             return [
//                 'id' => $post->id,
//                 'type' => 'Event',
//                 'categoryColor' => 'primary',
//                 'title' => 'New Event: ' . $item->title,
//                 'content' => 'Join us at ' . $item->location . ' on ' . $item->date,
//                 'timestamp' => $post->created_at->diffForHumans(),
//             ];
//         }

//         // If it's an ADMIN POST
//         if ($post->feedable_type === 'App\Models\AdminPost') {
//             return [
//                 'id' => $post->id,
//                 'type' => 'Official Update',
//                 'categoryColor' => 'error',
//                 'title' => $item->headline,
//                 'content' => $item->body,
//                 'timestamp' => $post->created_at->diffForHumans(),
//             ];
//         }
//     });

//     return response()->json($formattedFeed);
// }
}
