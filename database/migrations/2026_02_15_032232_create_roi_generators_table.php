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
        Schema::create('roi_generators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('investment_amount', 15, 2);
            $table->decimal('roi_percentage', 8, 2);
            $table->decimal('roi_amount', 15, 2);
            $table->date('roi_date');
            $table->timestamps();

            $table->unique(['user_id', 'roi_date']); // Prevent duplicate ROI per day
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roi_generators');
    }
};
