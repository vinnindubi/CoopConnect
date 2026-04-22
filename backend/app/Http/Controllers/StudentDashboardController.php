<?php

namespace App\Http\Controllers;

use App\Models\GroupEvent;
use Illuminate\Http\Request;
use App\Models\FeedPost;
use Carbon\Carbon;
use App\Models\MarketplaceProduct;
class StudentDashboardController extends Controller
{
// 1. The Main Feed
 public function getFeed()
    {
       //  Removed 'feedable.group'. We will just load 'feedable' safely!
        $feed = FeedPost::with('feedable')->latest()->take(50)->get();

        $formattedFeed = $feed->map(function ($post) {
            $item = $post->feedable;

            // Failsafe: Skip if the original event or article was deleted
            if (!$item) return null;

            // ==========================================
            // 1. HANDLE ARTICLES
            // ==========================================
            if ($post->feedable_type === 'App\Models\Article') {
                return [
                    'id' => 'feed-' . $post->id,
                    'isArticle' => true,                 // Tells Vue to handle this as an article
                    'isImagePost' => !is_null($item->image),
                    'image' => $item->image,
                    'category' => $item->category,
                    'categoryColor' => 'indigo',         // Distinct purple/indigo styling for articles
                    'icon' => 'mdi-newspaper-variant-outline',
                    'iconBg' => 'indigo-lighten-5',
                    'timestamp' => $post->created_at->diffForHumans(),
                    'title' => $item->title,
                    'content' => $item->excerpt,         // Short excerpt for the feed card
                    'fullContent' => $item->content,     // Full story for when they click to read it
                ];
            }

            // ==========================================
            // 2. HANDLE EVENTS
            // ==========================================
            elseif ($post->feedable_type === 'App\Models\GroupEvent') {
                
                // --- SCENARIO A: Admin Post (group_id is null) ---
                if (is_null($item->group_id)) {
                    return [
                        'id' => 'feed-' . $post->id,
                        'isImagePost' => !is_null($item->image),
                        'image' => $item->image,
                        'category' => 'Official Announcement',
                        'categoryColor' => 'error', // Red styling
                        'icon' => 'mdi-shield-crown-outline', 
                        'iconBg' => 'red-lighten-5',
                        'timestamp' => $post->created_at->diffForHumans(),
                        'title' => $item->title,
                        'content' => $item->description,
                    ];
                } 
                
                // --- SCENARIO B: Student/Club Event (has a group_id) ---
                else {
                    $groupName = $item->group ? $item->group->name : 'Campus';

                    return [
                        'id' => 'feed-' . $post->id,
                        'isImagePost' => !is_null($item->image),
                        'image' => $item->image,
                        'category' => $groupName, 
                        'categoryColor' => 'primary', // Blue styling
                        'icon' => 'mdi-calendar-star',
                        'iconBg' => 'blue-lighten-5',
                        'timestamp' => $post->created_at->diffForHumans(),
                        'title' => $item->title,
                        'content' => $item->description . ' — 📍 Location: ' . ($item->location ?? 'TBA') . ' | 📅 Date: ' . \Carbon\Carbon::parse($item->event_date)->format('M d, Y'),
                    ];
                }
            }

            // Fallback for anything else
            return null;
        });

        // 3. Filter out nulls and return clean JSON
        return response()->json($formattedFeed->filter()->values());
    }
    public function getHomeData()
    {
        // 1. Get 4 random products for the "Interesting" section
        $featuredProducts = MarketplaceProduct::inRandomOrder()
            ->limit(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'price' => 'KES ' . number_format($product->price, 0),
                    'image' => $product->image,
                    'seller_id' => $product->user_id,
                ];
            });

        // 2. Get the 3 closest upcoming events (where date is today or in the future)
        $upcomingActivities = GroupEvent::where('event_date', '>=', Carbon::now())
            ->orderBy('event_date', 'asc')
            ->limit(3)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'club' => $activity->club_name,
                    // Formats date to something like "Oct 24, 2:00 PM"
                    'date' => Carbon::parse($activity->event_date)->format('M d, g:i A'), 
                    'location' => $activity->location,
                ];
            });

        // Return them both in one payload!
        return response()->json([
            'featured_products' => $featuredProducts,
            'upcoming_activities' => $upcomingActivities
        ]);
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
