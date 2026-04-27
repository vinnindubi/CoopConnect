<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    // Fetch replies for a specific post
    public function index($postId)
    {
        $replies = Reply::with('user')->where('post_id', $postId)->oldest()->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $replies
        ]);
    }

    // Save a new reply
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $reply = Reply::create([
            'post_id' => $postId,
            'user_id' => auth()->id(), 
            'content' => $request->content
        ]);

        // Load the user so the frontend instantly gets the avatar and name
        $reply->load('user');

        return response()->json([
            'status' => 'success',
            'data' => $reply
        ], 201);
    }
}