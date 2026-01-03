<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_cancellations', function (Blueprint $table) {
            $table->id('cancel_id');
            $table->unsignedBigInteger('booking_id');
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->decimal('deduction percentage', 10, 2)->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->useCurrent();
            $table->unsignedBigInteger('cancelled_by')->nullable()->comment('User ID or admin who cancelled');
            $table->string('status')->default('cancelled'); // for tracking
            $table->timestamps();

            // Foreign key relation to bookings
            $table->foreign('booking_id')->references('id')->on('booking')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_cancellations');
    }
};
