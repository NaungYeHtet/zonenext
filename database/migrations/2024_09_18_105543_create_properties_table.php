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
            $table->foreignId('township_id')->nullable()->constrained('townships');
            $table->foreignId('owner_id')->nullable()->constrained('leads');
            $table->foreignId('customer_id')->nullable()->constrained('leads');
            $table->json('title');
            $table->json('description');
            $table->string('type', 30);
            $table->string('slug')->unique();
            $table->string('status', 30);
            $table->json('address');
            $table->string('cover_image');
            $table->json('images')->nullable();
            $table->float('latitude');
            $table->float('longitude');
            $table->unsignedInteger('views_count')->default(0);

            // area detail
            $table->string('area_type');
            $table->string('area_unit')->nullable();
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('length')->nullable();
            $table->unsignedMediumInteger('area')->nullable();
            $table->unsignedMediumInteger('square_feet')->nullable();
            $table->unsignedSmallInteger('bathrooms_count')->nullable();

            // rent price detail
            $table->string('acquisition_type', 5)->nullable();
            $table->string('price_type', 30)->nullable();
            $table->integer('price_from')->default(0);
            $table->integer('price_to')->default(0);
            $table->boolean('negotiable')->default(false);
            $table->decimal('owner_commission', 5, 2)->default(0);
            $table->decimal('customer_commission', 5, 2)->default(0);
            $table->integer('purchased_price')->nullable();
            $table->integer('purchased_commission')->nullable();

            $table->dateTime('posted_at')->nullable();
            $table->dateTime('purchased_at')->nullable();
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
