<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingPaymentTable extends Migration
{
    public function up(): void
    {
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id');
            $table->string('payment_method'); // e.g., card, UPI, net banking
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('INR');
            $table->enum('payment_status', ['0', '1', '2'])->default('0'); // 0: Pending, 1: Completed, 2: Failed

            $table->string('transaction_id')->nullabel();
            $table->text('transaction_details')->nullable();
            $table->text('razorpay_order_id')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('booking_id')->references('booking_id')->on('booking')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_payment');
    }
}
