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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            // We use checkout_request_id to match the M-Pesa callback to this specific record
            $table->string('checkout_request_id')->unique(); 
            $table->string('phone_number');
            $table->decimal('amount', 8, 2);
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->string('mpesa_receipt_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
