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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');  // FK to locations
            $table->unsignedBigInteger('locality_id')->nullable(); // FK to localities, optional

            $table->string('name');
            $table->text('description')->nullable();
            $table->float('rating_avg')->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('count')->nullable()->default(0);
            $table->boolean('is_featured')->default(false);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('property_rules');

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();

            // Foreign Keys
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
