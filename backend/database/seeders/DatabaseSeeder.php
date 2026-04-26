<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'admission' => 'ADM-001', // Add this line!
        ]);
        $this->call(MarketplaceSeeder::class);
        $this->call(GroupSeeder::class); // Make sure groups seed first!
        $this->call(GroupEventSeeder::class); // Then events seed using those group IDs
        $this->call(ForumPostSeeder::class);
    }
}
