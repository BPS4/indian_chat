<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('media')->nullable();
            $table->string('youtube_link')->nullable();
            $table->text('description');
            $table->string('calling_number');
            $table->string('website_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('telegram_link')->nullable();
            $table->string('country')->default('India');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->boolean('auto_send')->default(false);
            $table->integer('total_users')->nullable();
            $table->integer('created_by')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
