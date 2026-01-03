<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingRoomsTable extends Migration
{
    public function up(): void
    {
        Schema::create('booking_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('room_type_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price_per_room', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            // Foreign keys
            $table->foreign('booking_id')->references('id')->on('booking')->onDelete('cascade');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_rooms');
    }
}

