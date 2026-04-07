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
        Schema::create('group_users', function (Blueprint $table) {
            $table->id();
        
        // Foreign Keys
        $table->foreignId('group_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        
        // System Access Level ('admin' sees the dashboard, 'member' does not)
        $table->enum('role', ['member', 'admin'])->default('member');
        
        // Public Display Title (e.g., 'Chairperson', 'Secretary', 'Treasurer')
        $table->string('title')->nullable(); 
        
        $table->timestamps(); // Serves as the "Joined At" date
        
        // Ensure a user can only join a specific group once
        $table->unique(['group_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_users');
    }
};
