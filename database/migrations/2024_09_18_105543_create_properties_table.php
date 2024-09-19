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

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('township_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->json('title');
            $table->json('description');
            $table->string('type', 30);
            $table->string('slug')->unique();
            $table->string('status', 30);
            $table->json('address');
            $table->string('cover_image');
            $table->float('latitude');
            $table->float('longitude');
            $table->dateTime('posted_at')->nullable();
            $table->dateTime('sold_at')->nullable();
            $table->dateTime('rent_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
