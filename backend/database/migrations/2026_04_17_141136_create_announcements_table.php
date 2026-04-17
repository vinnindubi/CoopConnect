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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('severity', ['info', 'warning', 'error'])->default('info');
            $table->text('message');
            $table->string('action_text')->nullable();
            $table->string('action_link')->nullable();
            $table->boolean('is_active')->default(true); // So we can turn it off without deleting it
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
