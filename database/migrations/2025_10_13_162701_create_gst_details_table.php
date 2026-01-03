<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGstDetailsTable extends Migration
{
    public function up(): void
    {
        Schema::create('gst_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('gst_no', 15); // GSTIN is 15 characters
            $table->string('company_name');
            $table->text('address')->nullable();
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
        Schema::dropIfExists('gst_details');
    }
}
