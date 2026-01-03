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
        Schema::create('facility_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->nullable()->constrained('facility_groups')->onDelete('cascade');
            $table->string('facility_name');
            $table->string('icon')->nullable();
            $table->boolean('facility_for')->default('Hotel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_masters');
    }
};
