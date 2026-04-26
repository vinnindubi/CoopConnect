<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GroupEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Grab some random group IDs from the groups you already seeded
        // This prevents foreign key crashes!
        $groups = DB::table('groups')->pluck('id')->toArray();
        
        // Failsafe: If no groups exist, we just use null for everything (Admin posts)
        $randomGroupId1 = !empty($groups) ? $groups[array_rand($groups)] : null;
        $randomGroupId2 = !empty($groups) ? $groups[array_rand($groups)] : null;
        $randomGroupId3 = !empty($groups) ? $groups[array_rand($groups)] : null;

        $events = [
            // ==========================================
            // 1. ADMIN EVENTS (group_id is null)
            // ==========================================
            [
                'group_id' => null,
                'title' => 'Vice Chancellor\'s State of the Campus Address',
                'event_type' => 'Official Admin',
                'description' => 'All students are invited to the main auditorium for the semester briefing. We will discuss new campus developments, the upcoming Innovation Week, and updates to the student welfare policies.',
                'event_date' => $now->copy()->addDays(2)->setTime(14, 0), // 2 days from now at 2 PM
                'location' => 'Main University Auditorium',
                'price' => 0.00,
                'image' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&q=80&w=800',
                'organizer' => 'Office of the Vice Chancellor',
                'is_featured' => true, // Highlighted in the UI
                'status' => 'upcoming',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'group_id' => null,
                'title' => 'Campus Innovation Week 2026',
                'event_type' => 'Academic & Tech',
                'description' => 'The biggest event of the year! Come see the amazing projects our students have been building. Pitching sessions, tech demonstrations, and networking with industry leaders.',
                'event_date' => $now->copy()->addDays(5)->setTime(9, 0),
                'location' => 'Innovation Hub & Library Grounds',
                'price' => 0.00,
                'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&q=80&w=800',
                'organizer' => 'University Administration',
                'is_featured' => true,
                'status' => 'upcoming',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ==========================================
            // 2. GROUP EVENTS (Assigned to existing groups)
            // ==========================================
            [
                'group_id' => $randomGroupId1,
                'title' => 'Introduction to Laravel & Vue.js Workshop',
                'event_type' => 'Academic & Tech',
                'description' => 'Want to learn how to build full-stack applications? Join us for this hands-on workshop where we will build a mini-project from scratch. Bring your laptops!',
                'event_date' => $now->copy()->addDays(1)->setTime(16, 0),
                'location' => 'Computer Lab 3, ICT Center',
                'price' => 0.00,
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=800',
                'organizer' => 'Tech Committee',
                'is_featured' => true,
                'status' => 'upcoming',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'group_id' => $randomGroupId2,
                'title' => 'Weekend Nature Hike & Clean-up',
                'event_type' => 'Social & Culture',
                'description' => 'Take a break from your screens! We are heading out for a nature walk and will be doing a light trash clean-up along the trail. Transport will be provided from the main gate.',
                'event_date' => $now->copy()->addDays(4)->setTime(7, 30),
                'location' => 'Meet at Main Gate',
                'price' => 200.00, // Small fee for transport
                'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=800',
                'organizer' => 'Events Organizing Team',
                'is_featured' => false,
                'status' => 'upcoming',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'group_id' => $randomGroupId3,
                'title' => 'Inter-University Poetry Slam',
                'event_type' => 'Arts & Entertainment',
                'description' => 'An evening of spoken word, poetry, and live acoustic music. Come support our campus representatives as they battle it out with visiting universities.',
                'event_date' => $now->copy()->addDays(7)->setTime(18, 0),
                'location' => 'Student Centre Amphitheatre',
                'price' => 100.00,
                'image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&q=80&w=800',
                'organizer' => 'Creative Arts Exec Board',
                'is_featured' => false,
                'status' => 'upcoming',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insert into database
        DB::table('group_events')->insert($events);
    }
}