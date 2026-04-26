<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ForumPost;
use Carbon\Carbon;

class CalculateHotPosts extends Command
{
    protected $signature = 'forum:calculate-hot';
    protected $description = 'Calculates the hotness score for recent posts and updates the is_hot flag';

    public function handle()
    {
        // 1. Reset everything to false first
        ForumPost::where('is_hot', true)->update(['is_hot' => false]);

        // 2. Grab posts from the last 48 hours (no need to calculate ancient posts)
        $recentPosts = ForumPost::withCount('comments')
            ->where('created_at', '>=', Carbon::now()->subHours(48))
            ->get();

        $scoredPosts = $recentPosts->map(function ($post) {
            $ageInHours = $post->created_at->diffInHours(Carbon::now());
            
            // The Algorithm: (Upvotes + (Comments * 2)) / (Age + 2)^1.5
            $engagement = $post->upvotes + ($post->comments_count * 2);
            $score = $engagement / pow(($ageInHours + 2), 1.5);

            $post->hot_score = $score;
            return $post;
        });

        // 3. Grab the top 5 highest scores
        $topPosts = $scoredPosts->sortByDesc('hot_score')->take(5);

        // 4. Mark those top 5 as "Hot"!
        foreach ($topPosts as $post) {
            $post->update(['is_hot' => true]);
        }

        $this->info('Hot posts successfully calculated!');
    }
}