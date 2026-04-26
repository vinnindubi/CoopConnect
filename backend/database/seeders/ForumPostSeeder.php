<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumPost;
use App\Models\User;
use Carbon\Carbon;

class ForumPostSeeder extends Seeder
{
    public function run(): void
    {
        // Grab some existing users, or fail gracefully
        $users = User::inRandomOrder()->take(3)->get();
        
        $user1 = $users->get(0)->id ?? null;
        $user2 = $users->get(1)->id ?? null;
        $user3 = $users->get(2)->id ?? null;

        $now = Carbon::now();

        $posts = [
            [
                'user_id' => $user1,
                'title' => 'Is the new 24/7 library schedule actually helping anyone?',
                'excerpt' => 'I\'ve been going to the library at 2 AM and it\'s mostly empty, but the guards seem exhausted. Are we actually using this or should the school revert the budget to something else? #CampusLife',
                'category' => 'Campus Life',
                'upvotes' => 142,
                'is_hot' => true,
                'tags' => json_encode(['CampusLife']),
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(2),
            ],
            [
                'user_id' => $user2,
                'title' => 'Looking for a roommate (Male) - Ruiru Area',
                'excerpt' => 'Hey guys, my current roommate is graduating. I have a 2-bedroom place near the main gate. Rent is KES 8,000 per person. Looking for someone clean and preferably in 3rd/4th year. #Housing #Ruiru',
                'category' => 'Housing',
                'upvotes' => 12,
                'is_hot' => false,
                'tags' => json_encode(['Housing', 'Ruiru']),
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now->copy()->subHours(5),
            ],
            [
                'user_id' => $user3,
                'title' => 'Best tech companies for Software Engineering attachments?',
                'excerpt' => 'I\'m starting my attachment search for next semester. Has anyone here interned at Safaricom or Microsoft ADC? What was the interview process like? #Attachment #Tech',
                'category' => 'Attachment & Careers',
                'upvotes' => 89,
                'is_hot' => true,
                'tags' => json_encode(['Attachment', 'Tech']),
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ],
            [
                'user_id' => null, // Anonymous Post
                'title' => 'The WiFi in Block C has been down for 3 days 😡',
                'excerpt' => 'How are we supposed to submit assignments when the school WiFi keeps dropping? I\'ve used up all my mobile data. Anyone else facing this issue? #HELF_Loans #Frustrated',
                'category' => 'Rants',
                'upvotes' => 215,
                'is_hot' => false,
                'tags' => json_encode(['HELF_Loans', 'Frustrated']),
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ]
        ];

        ForumPost::insert($posts);
    }
}