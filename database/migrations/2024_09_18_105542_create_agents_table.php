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
        Schema::disableForeignKeyConstraints();

        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('phone');
            $table->dateTime('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('language');

            $table->json('preferred_notification_channels')->nullable();
            $table->json('preferred_lead_interests')->nullable();
            $table->json('preferred_property_types')->nullable();
            $table->json('preferred_townships')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
