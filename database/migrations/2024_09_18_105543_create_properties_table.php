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
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('township_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
            $table->boolean('is_rentable')->default(false);
            $table->string('rent_price_type', 30)->nullable();
            $table->integer('rent_price_from')->default(0);
            $table->integer('rent_price_to')->default(0);
            $table->boolean('rent_negotiable')->default(false);
            $table->decimal('landlord_commission', 5, 2)->default(0);
            $table->decimal('renter_commission', 5, 2)->default(0);
            $table->integer('rented_price')->nullable();
            $table->integer('rented_commission')->nullable();

            // sale price detail
            $table->boolean('is_saleable')->default(false);
            $table->string('sale_price_type', 30)->nullable();
            $table->integer('sale_price_from')->default(0);
            $table->integer('sale_price_to')->default(0);
            $table->boolean('sale_negotiable')->default(false);
            $table->decimal('seller_commission', 5, 2)->default(0);
            $table->integer('sold_price')->nullable();
            $table->integer('sold_commission')->nullable();

            $table->dateTime('posted_at')->nullable();
            $table->dateTime('sold_at')->nullable();
            $table->dateTime('rented_at')->nullable();
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
