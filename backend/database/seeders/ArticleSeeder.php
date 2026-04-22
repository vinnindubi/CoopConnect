<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // We need a user to assign as the author. Let's find the first user, or create a dummy one.
        $user = User::first() ?? User::factory()->create([
            'name' => 'Campus Admin',
            'email' => 'admin@campus.edu',
            'password' => bcrypt('password'), // added a safe default password just in case
        ]);

        $articles = [
            [
                'title' => 'Surviving Your First Year: A Senior’s Ultimate Cheat Sheet',
                'excerpt' => 'Forget what the brochure told you. Here is the actual truth about making friends, finding the best food, and surviving finals week without losing your mind.',
                'content' => 'Here is the full article content. This is where the long-form text goes explaining all the tips and tricks for first-year survival.',
                'category' => 'Tips & Tricks',
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=1200',
                'user_id' => $user->id,
            ],
            [
                'title' => 'The Best Off-Campus Housing Spots You Didn’t Know About',
                'excerpt' => 'Tired of dorm life? We scoured the areas around campus to find the hidden gems that won’t break your HELB loan.',
                'content' => 'Finding affordable housing is tough. Here is a breakdown of the best estates, average rent prices, and how to find good roommates...',
                'category' => 'Housing',
                'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=600',
                'user_id' => $user->id,
            ],
            [
                'title' => 'My 48 Hours Awake at the CUK Tech Hackathon',
                'excerpt' => 'Lots of coffee, zero sleep, and one broken keyboard later. Here is what it’s actually like to compete in the annual tech showdown.',
                'content' => 'Day 1 started with high hopes. By Day 2, we were debugging code through sheer willpower. Here is the story of our hackathon project...',
                'category' => 'Experiences',
                'image' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=600',
                'user_id' => $user->id,
            ],
            [
                'title' => 'Mastering the Art of Meal Prepping on a Student Budget',
                'excerpt' => 'Tired of eating noodles every night? Here is a realistic, step-by-step guide to cooking an entire week of healthy meals for under KES 1,500.',
                'content' => 'Eating healthy on a budget is entirely possible. It all comes down to buying in bulk at the local market and dedicating Sunday evening to cooking...',
                'category' => 'Student Life',
                'image' => 'https://plus.unsplash.com/premium_photo-1680291971376-ccc54aacb22b?w=500&auto=format&fit=crop&q=60',
                'user_id' => $user->id,
            ]
        ];

        // Loop through the array and insert them into the database
        foreach ($articles as $articleData) {
            Article::create($articleData);
        }
    }
}