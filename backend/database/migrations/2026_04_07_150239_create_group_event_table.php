<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_events', function (Blueprint $table) {
            $table->id();
            
            // Relationship
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            
            // 1. Core Event Details
            $table->string('title');
            $table->string('event_type'); // Your standardized constants ('Academic & Tech', etc.)
            $table->text('description');
            $table->dateTime('event_date');
            
            // 2. Location & Logistics
            $table->string('location')->nullable(); // e.g., "Student Centre", "Google Meet"
            $table->decimal('price', 8, 2)->default(0); // e.g., 500.00. Default 0 means "Free"
            
            // 3. Media & Customization
            $table->string('image')->nullable(); // Custom banner for this specific event
            $table->string('organizer')->nullable(); // In case a sub-committee is hosting (e.g., "Tech Dept")
            
            // 4. State Management (Crucial for production apps)
            $table->boolean('is_featured')->default(false); // Easily grab the top 3 for your UI Carousel!
            $table->string('status')->default('upcoming'); // 'upcoming', 'ongoing', 'completed', 'cancelled'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_events');
    }
};
