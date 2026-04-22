<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $groups = [
            // ==========================================
            // RELIGIOUS SOCIETIES
            // ==========================================
            [
                'type' => 'society',
                'category' => 'Christian',
                'name' => 'Christian Union (CU)',
                'slug' => Str::slug('Christian Union CU'),
                'description' => 'A fellowship of Christian students dedicated to spiritual growth, evangelism, and community service.',
                'mission' => 'To know Christ and make Him known across the university campus.',
                'cover_image' => 'https://images.unsplash.com/photo-1438232992991-995b7058bbb3?auto=format&fit=crop&q=80&w=800',
                'meeting_time' => 'Wednesday 5:00 PM - 7:00 PM, Sunday 8:00 AM - 12:30 PM',
                'meeting_venue' => 'Main University Auditorium',
                'contact_email' => 'cu@university.edu',
                'patron_name' => 'Rev. Dr. John Kamau',
                'proposal_document_path' => 'proposals/cu_registration_document.pdf',
                'is_approved' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'society',
                'category' => 'Catholic',
                'name' => 'Catholic Students Association (CSA)',
                'slug' => Str::slug('Catholic Students Association CSA'),
                'description' => 'A community of Catholic students fostering spiritual nourishment, fellowship, and active participation in the Holy Mass.',
                'mission' => 'To live and spread the Gospel values in our academic journey.',
                'cover_image' => 'https://images.unsplash.com/photo-1436564989038-18b9958df72b?auto=format&fit=crop&q=80&w=800',
                'meeting_time' => 'Thursday 5:30 PM - 7:00 PM, Sunday 9:00 AM - 11:00 AM',
                'meeting_venue' => 'Chapel / Block A Room 101',
                'contact_email' => 'csa@university.edu',
                'patron_name' => 'Father Peter Ochieng',
                'proposal_document_path' => 'proposals/csa_registration_document.pdf',
                'is_approved' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'society',
                'category' => 'Islamic',
                'name' => 'Muslim Students Association (MSA)',
                'slug' => Str::slug('Muslim Students Association MSA'),
                'description' => 'A society catering to the spiritual, social, and academic needs of Muslim students on campus.',
                'mission' => 'To promote the understanding of Islam and foster a strong brotherhood/sisterhood among students.',
                'cover_image' => 'https://images.unsplash.com/photo-1564121211835-e88c852648ab?auto=format&fit=crop&q=80&w=800',
                'meeting_time' => 'Friday 1:00 PM - 2:00 PM (Jumu\'ah)',
                'meeting_venue' => 'University Mosque / Block C Hall',
                'contact_email' => 'msa@university.edu',
                'patron_name' => 'Dr. Amina Hassan',
                'proposal_document_path' => 'proposals/msa_registration_document.pdf',
                'is_approved' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ==========================================
            // CLUBS
            // ==========================================
            [
                'type' => 'club',
                'category' => 'Technology',
                'name' => 'Tech Innovators Club',
                'slug' => Str::slug('Tech Innovators Club'),
                'description' => 'A community for coding enthusiasts, aspiring developers, and tech innovators to collaborate on projects and learn new stacks.',
                'mission' => 'To bridge the gap between academic theory and practical software engineering.',
                'cover_image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=800',
                'meeting_time' => 'Tuesday 4:00 PM - 6:00 PM',
                'meeting_venue' => 'Computer Lab 3, ICT Center',
                'contact_email' => 'techclub@university.edu',
                'patron_name' => 'Mr. David Kipkorir',
                'proposal_document_path' => 'proposals/tech_club_registration.pdf',
                'is_approved' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'club',
                'category' => 'Environment',
                'name' => 'Environmental & Wildlife Club',
                'slug' => Str::slug('Environmental and Wildlife Club'),
                'description' => 'Advocates for environmental conservation, climate awareness, and green initiatives within and beyond the university.',
                'mission' => 'To promote sustainable living and protect our natural heritage through tree planting and clean-up drives.',
                'cover_image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=800',
                'meeting_time' => 'Friday 3:00 PM - 5:00 PM',
                'meeting_venue' => 'Science Block, Room 4B',
                'contact_email' => 'ecoclub@university.edu',
                'patron_name' => 'Prof. Jane Wanjiku',
                'proposal_document_path' => 'proposals/eco_club_registration.pdf',
                'is_approved' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'club',
                'category' => 'Arts & Culture',
                'name' => 'Creative Arts & Drama Society',
                'slug' => Str::slug('Creative Arts and Drama Society'),
                'description' => 'The premier hub for actors, poets, spoken word artists, and dancers to express themselves and prepare for regional drama festivals.',
                'mission' => 'To nurture creative talent and provide a platform for artistic expression.',
                'cover_image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&q=80&w=800',
                'meeting_time' => 'Monday & Thursday 5:00 PM - 7:30 PM',
                'meeting_venue' => 'Student Center Ampitheatre',
                'contact_email' => 'drama@university.edu',
                'patron_name' => 'Mrs. Grace Mutuku',
                'proposal_document_path' => 'proposals/drama_club_registration.pdf',
                'is_approved' => true, // Setting this to false if you want to test the approval workflow!
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        DB::table('groups')->insert($groups);
    }
}