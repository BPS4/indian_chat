<?php

// database/migrations/xxxx_xx_xx_create_booking_discounts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('booking_id')->references('id')->on('booking')->onDelete('cascade');
            $table->foreign('coupon_id')->references('coupon_id')->on('coupons')->onDelete('set null');
            $table->foreign('offer_id')->references('offer_id')->on('offers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_discounts');
    }
};
