<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('feedback', function (Blueprint $table) {
        $table->id();
        
        // Make sure this line exists and is saved!
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); 
        
        $table->string('category');
        $table->integer('rating')->nullable();
        $table->text('details');
        $table->string('attachment_path')->nullable();
        $table->boolean('is_anonymous')->default(false);
        $table->boolean('allow_contact')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
