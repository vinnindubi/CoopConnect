<?php

namespace Database\Seeders;

use App\Models\MarketplaceAdvert;
use App\Models\MarketplaceProduct;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------
        // 1. HELPER: Create Dummy Users 
        // ---------------------------------------------------
        $getOrCreateUser = function ($fullname, $isVerified = true) {
            return User::firstOrCreate(
                ['email' => Str::slug($fullname) . '@student.cuk.ac.ke'],
                [
                    'fullname' => $fullname,
                    'admission' => 'CT201/' . rand(1000, 9999) . '/21',
                    'password' => bcrypt('password'),
                    'verification_status' => $isVerified ? 'approved' : 'pending',
                ]
            );
        };

        // ---------------------------------------------------
        // 2. SEED PRODUCTS
        // Allowed Categories: 'Electronics', 'Fashion', 'Academic', 'Services'
        // Allowed Conditions: 'new','good', 'fair', 'poor'
        // ---------------------------------------------------
        $products = [
            [
                'title' => 'HP EliteBook 840 G5 (Core i5)',
                'price' => 25000,
                'category' => 'Electronics',
                'seller' => 'TechHub Campus',
                'image' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&q=80&w=400',
                'condition' => 'good',
                'is_negotiable' => true,
                'quantity' => 2
            ],
            [
                'title' => 'Knotless Braids & Dreadlocks',
                'price' => 800,
                'category' => 'Services',
                'seller' => 'Glamour Cuts',
                'image' => 'https://images.unsplash.com/photo-1605497788044-5a32c7078486?auto=format&fit=crop&q=80&w=400',
                'condition' => 'new',
                'is_negotiable' => false,
                'quantity' => 3
            ],
            [
                'title' => 'Casio fx-991EX Scientific Calculator',
                'price' => 1500,
                'category' => 'Academic',
                'seller' => 'Engineering Seniors',
                'image' => 'https://images.unsplash.com/photo-1632571401005-458e9d244591?w=500&auto=format&fit=crop&q=60',
                'condition' => 'new',
                'is_negotiable' => true,
            ],
            [
                'title' => 'Vintage Thrifted Varsity Jackets',
                'price' => 1200,
                'category' => 'Fashion',
                'seller' => 'Drip Thrift Store',
                'image' => 'https://images.unsplash.com/photo-1584966393803-c8c7257794cd?w=500&auto=format&fit=crop&q=60',
                'condition' => 'good',
                'is_negotiable' => false,
                'quantity' => 2
            ],
            [
                'title' => 'Sony Noise Cancelling Headphones',
                'price' => 3500,
                'category' => 'Electronics',
                'seller' => 'Gadget Gadgets', // Unverified for testing
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&q=80&w=400',
                'condition' => 'new',
                'is_negotiable' => true,
            ],
            [
                'title' => 'Laptop Repair & Windows Installation',
                'price' => 500,
                'category' => 'Services',
                'seller' => 'Campus IT Guru',
                'image' => 'https://images.unsplash.com/photo-1709102884400-b50ca1a12bc3?w=500&auto=format&fit=crop&q=60',
                'condition' => 'new',
                'is_negotiable' => false,
            ],
            [
                'title' => 'Nike Air Force 1 (Size 40-44)',
                'price' => 2500,
                'category' => 'Fashion',
                'seller' => 'Kicks Ke',
                'image' => 'https://images.unsplash.com/photo-1712167631738-4dab9e53c853?w=500&auto=format&fit=crop&q=60',
                'condition' => 'new',
                'is_negotiable' => true,
            ],
            [
                'title' => 'Business Law & Economics Textbooks',
                'price' => 1200,
                'category' => 'Academic',
                'seller' => 'Jennifer Wambo',
                'image' => 'https://images.unsplash.com/photo-1588580000645-4562a6d2c839?auto=format&fit=crop&q=80&w=400',
                'condition' => 'fair',
                'is_negotiable' => true,
            ],
            [
                'title' => 'Original iPhone 20W Fast Charger',
                'price' => 1800,
                'category' => 'Electronics',
                'seller' => 'TechHub Campus',
                'image' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&q=80&w=400',
                'condition' => 'new',
                'is_negotiable' => false,
            ],
            [
                'title' => 'Graduation & Birthday Photoshoots',
                'price' => 3000,
                'category' => 'Services',
                'seller' => 'Lens Crafters',
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&q=80&w=400',
                'condition' => 'new',
                'is_negotiable' => true,
            ]
        ];

        foreach ($products as $item) {
            $isVerified = ($item['seller'] !== 'Gadget Gadgets'); 
            $user = $getOrCreateUser($item['seller'], $isVerified);

            MarketplaceProduct::create([
                'user_id' => $user->id,
                'title' => $item['title'],
                'price' => $item['price'],
                'category' => $item['category'],
                'image' => $item['image'],
                'description' => 'Available now on campus. Contact me for more details.',
                'condition' => $item['condition'],
                'is_negotiable' => $item['is_negotiable'],
                'status' => 'active'
            ]);
        }

        // ---------------------------------------------------
        // 3. SEED ADVERTS
        // Allowed Categories: 'Housing', 'Collaborations', 'Jobs', 'General'
        // ---------------------------------------------------
        $adverts = [
            [
                'title' => 'Looking for a Female Roommate',
                'category' => 'Housing',
                'description' => 'Seeking a neat roommate to share a 1-bedroom apartment in Hardy Karen. Rent is 6k per month. WiFi included.',
                'author' => 'Sarah M.',
            ],
            [
                'title' => 'Frontend Developer Needed for Startup',
                'category' => 'Collaborations',
                'description' => 'We are building a fintech app for students and need a Vue.js developer to join our team. Equity compensation.',
                'author' => 'CoopInnovate Team',
            ],
            [
                'title' => 'Part-time Library Assistant',
                'category' => 'Jobs',
                'description' => 'The main library is looking for 3 students to work evening shifts organizing shelves. Apply at the admin block.',
                'author' => 'Library Admin',
            ]
        ];

        foreach ($adverts as $item) {
            $user = $getOrCreateUser($item['author'], false);

            MarketplaceAdvert::create([
                'user_id' => $user->id,
                'title' => $item['title'],
                'category' => $item['category'],
                'description' => $item['description'],
                'status' => 'active'
            ]);
        }
    }
}