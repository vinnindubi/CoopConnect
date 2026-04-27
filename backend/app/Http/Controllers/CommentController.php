<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // ... your index and store methods go here
    public function store(Request $request, $articleId)
    {
        $request->validate(['content' => 'required|string']);

        $comment = Comment::create([
            'article_id' => $articleId,
            'user_id' => auth()->id() ?? 1, // Using 1 temporarily to test!
            'content' => $request->content
        ]);

        $comment->load('user');

        return response()->json([
            'status' => 'success',
            'data' => $comment
        ], 201);
    }

    public function index($articleId)
    {
        $comments = Comment::with('user')->where('article_id', $articleId)->latest()->get();
        return response()->json(['status' => 'success', 'data' => $comments]);
    }
}