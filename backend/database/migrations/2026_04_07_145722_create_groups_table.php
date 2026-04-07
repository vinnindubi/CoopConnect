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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
        
        // Identity
        $table->enum('type', ['club', 'society']);
        $table->string('category'); // e.g., Tech, Media, Christian
        $table->string('name')->unique();
        $table->string('slug')->unique(); // For clean URLs like /clubs/tech-innovators
        
        // Content
        $table->text('description');
        $table->text('mission')->nullable();
        $table->string('cover_image')->nullable();
        
        // Logistics
        $table->string('meeting_time');
        $table->string('meeting_venue')->nullable();
        $table->string('contact_email')->nullable();
        
        // Official Registration Details
        $table->string('patron_name');
        $table->string('proposal_document_path'); // Path to the uploaded PDF
        
        // Super Admin Control
        $table->boolean('is_approved')->default(false); // Stays hidden until Dean approves
        
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
