<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Http\Resources\ForumPostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    public function index(Request $request)
    {
       $query = ForumPost::with('author')->withCount('replies')->latest();

        // Handle Category Filtering from Vue
        if ($request->has('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        // Handle Search Query from Vue
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('excerpt', 'like', $searchTerm);
            });
        }

        return ForumPostResource::collection($query->get());
    }

    public function store(Request $request)
    {
        // 1. Validate the new is_anonymous boolean
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'excerpt' => 'required|string|min:10',
            'is_anonymous' => 'boolean' // <--- Add this!
        ]);

        // Automatically extract hashtags from the excerpt using Regex
        preg_match_all('/#(\w+)/', $validated['excerpt'], $matches);
        $tags = !empty($matches[1]) ? $matches[1] : null;

        // 2. Determine the user_id based on the toggle
        $userId = $request->boolean('is_anonymous') ? null : auth()->id();

        $post = ForumPost::create([
            'user_id' => $userId, // <--- Assign it here!
            'title' => $validated['title'],
            'category' => $validated['category'],
            'excerpt' => $validated['excerpt'],
            'tags' => $tags,
        ]);

        return response()->json([
            'message' => 'Topic posted successfully',
            'post' => new ForumPostResource($post->load('author')->loadCount('replies'))
        ], 201);
    }

    public function upvote(ForumPost $post)
    {
        $post->increment('upvotes');
        return response()->json(['success' => true, 'upvotes' => $post->upvotes]);
    }

    public function trending()
    {
        // By using the ForumPost model instead of DB::table, 
        // Laravel automatically decodes the JSON strings into real arrays!
        $trending = ForumPost::whereNotNull('tags')
            ->pluck('tags')
            ->flatten()      // Flattens [['CampusLife'], ['Housing', 'Ruiru']] into ['CampusLife', 'Housing', 'Ruiru']
            ->countBy()      // Counts how many times each tag appears
            ->sortDesc()     // Sorts from most popular to least popular
            ->take(5)        // Grabs the top 5
            ->map(function ($count, $tag) {
                // Formats it cleanly for your Vue frontend
                return [
                    'tag' => '#' . $tag, 
                    'count' => $count . ' post' . ($count > 1 ? 's' : '') // Handles singular/plural nicely!
                ];
            })
            ->values(); // Resets the array keys so Vue's v-for loop is happy

        return response()->json($trending);
    }
    // UPDATE
    public function update(Request $request, ForumPost $post)
    {
        // Ensure only the author can edit (Optional, but good practice)
        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|min:10',
        ]);

        $post->update($validated);

        return response()->json(['message' => 'Post updated', 'post' => new ForumPostResource($post)]);
    }

    // DELETE
    public function destroy(ForumPost $post)
    {
        // Ensure only the author (or an admin) can delete
        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}