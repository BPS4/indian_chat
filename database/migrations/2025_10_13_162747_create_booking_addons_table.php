<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingAddonsTable extends Migration
{
    public function up(): void
    {
        Schema::create('booking_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('addon_name');
            $table->decimal('addon_price', 10, 2)->default(0.00);
            $table->integer('quantity')->default(1);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('booking_id')
                ->references('id')
                ->on('booking')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_addons');
    }
}
