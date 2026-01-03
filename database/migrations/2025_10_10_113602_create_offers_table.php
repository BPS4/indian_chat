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
        Schema::create('offers', function (Blueprint $table) {
             $table->id('offer_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['flat', 'percent']);
            $table->decimal('discount_value', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'inactive'])->default('active');

            // NEW FIELDS
            $table->unsignedBigInteger('hotel_id')->nullable(); // If null, applies to all hotels
            $table->decimal('min_booking_amount', 10, 2)->nullable(); // Optional minimum threshold
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Optional cap for % discount

            $table->timestamps();

            // Foreign Key Constraint (if hotels table exists)
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
