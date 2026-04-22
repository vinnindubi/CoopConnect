<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;
use App\Models\User;
use Carbon\Carbon;
use Str;
class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // 1. Hardcode the categories (Add 'All' for the Vue frontend tab)
        $categories = ['All', 'Student Life', 'Housing', 'Academics', 'Experiences', 'Tips & Tricks'];

        // 2. Fetch articles and eager-load the author only
        $query = Article::with('author')->latest();

        // Filter by Category ENUM
        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        // Filter by Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // 3. Map to the exact format Vue expects
        $articles = $query->get()->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'excerpt' => $article->excerpt,
                'category' => $article->category, // Directly accesses the ENUM string
                
                'authorName' => $article->author->fullname ?? 'Unknown Student',
                'authorRole' => $article->author->role ?? 'Student',
                'authorAvatar' => $article->author->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($article->author->fullname ?? 'Student'),
                
                'date' => Carbon::parse($article->created_at)->format('M d'),
                'readTime' => $article->read_time,
                'image' => $article->image ?? 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600',
            ];
        });

        // 4. Return both the tabs list and the articles!
        return response()->json([
            'categories' => $categories,
            'articles' => $articles
        ]);
    }
    public function storeArticle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image'=>'nullable|string',
            'category' => 'required|in:Student Life,Housing,Academics,Experiences,Tips & Tricks',
        ]);

        $validated['user_id'] = auth()->id(); 
        
        $wordCount = str_word_count(strip_tags($validated['content']));
        $minutes = ceil($wordCount / 200);
        $validated['read_time'] = $minutes . ' min read';

        $article = Article::create($validated);

        return response()->json([
            'message' => 'Article published!',
            'data'=> $article
            ]);
    }

/**
     * Fetch a single article's full details
     */
    public function show($id)
    {
        // Find the article or automatically return a 404 Not Found error
        $article = Article::with('author')->findOrFail($id);

        // Format it consistently with our index method
        $formattedArticle = [
            'id' => $article->id,
            'title' => $article->title,
            'excerpt' => $article->excerpt,
            'content' => $article->content, // The full HTML/text body!
            'category' => $article->category,
            
            'authorName' => $article->author->name ?? 'Unknown Student',
            'authorRole' => $article->author->role ?? 'Student',
            'authorAvatar' => $article->author->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($article->author->name ?? 'Student'),
            
            'date' => Carbon::parse($article->created_at)->format('M d, Y'), // Added the Year for the full page view
            'readTime' => $article->read_time,
            'image' => $article->image ?? 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1200',
        ];

        return response()->json($formattedArticle);
    }
    /**
     * Update an existing article
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'excerpt' => 'sometimes|required|string|max:500',
            'content' => 'sometimes|required|string',
            'category' => 'sometimes|required|in:Student Life,Housing,Academics,Experiences,Tips & Tricks',
        ]);

        // If the admin updated the content, we need to recalculate the read time!
        if (isset($validated['content'])) {
            $wordCount = str_word_count(strip_tags($validated['content']));
            $minutes = ceil($wordCount / 200);
            $validated['read_time'] = $minutes . ' min read';
        }

        $article->update($validated);

        return response()->json([
            'message' => 'Article updated successfully!',
            'article' => $article // Optional: send back the updated data
        ]);
    }

    /**
     * Delete an article
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        
        // Optional: If you are actually uploading images to an S3 bucket or local storage, 
        // you would delete the image file here before deleting the database row!
        // Storage::disk('public')->delete($article->image_path);

        $article->delete();

        return response()->json([
            'message' => 'Article deleted successfully!'
        ]);
    }
    public function userArticles($id)
    {
        // Find all articles where the user_id matches the seller's ID
        $articles = Article::where('user_id', $id)
            ->latest()
            ->get()
            ->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'excerpt' => Str::limit($article->content, 100), // Cuts a long article down to a snippet
                    'date' => $article->created_at->format('M d, Y'), // e.g., "Oct 12, 2025"
                ];
            });

        return response()->json(['articles' => $articles]);
    }
}
