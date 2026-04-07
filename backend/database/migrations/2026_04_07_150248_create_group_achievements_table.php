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
        Schema::create('group_achievements', function (Blueprint $table) {
           $table->id();
        $table->foreignId('group_id')->constrained()->cascadeOnDelete();
        
        $table->string('title');
        $table->text('description');
        $table->string('date_achieved'); // e.g., "August 2025"
        $table->string('image_path')->nullable();
        
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_achievements');
    }
};
