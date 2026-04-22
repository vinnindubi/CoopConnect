<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->string('title');
            $table->enum('category', [
                'Electronics', 
                'Fashion', 
                'Academic', 
                'Services'
            ]);
            $table->text('description')->nullable();
            
            //  Make quantity nullable (so Services can leave it empty)
            $table->integer('quantity')->nullable()->default(1);
            
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
        
            $table->enum('condition', [
                'new',
                'good',
                'fair',
                'poor'
            ])->nullable(); 
            
            $table->boolean('is_negotiable')->default(false);
            $table->enum('status', ['active', 'sold', 'hidden'])->default('active');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_products');
    }
};