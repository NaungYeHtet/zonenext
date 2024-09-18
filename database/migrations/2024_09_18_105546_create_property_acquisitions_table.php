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

        Schema::create('property_acquisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('type', 30);
            $table->string('price_type', 30);
            $table->integer('price_from');
            $table->integer('price_to')->nullable();
            $table->boolean('negotiable')->default(false);
            $table->decimal('owner_commission', 3, 2)->default(0);
            $table->decimal('customer_commission', 3, 2)->default(0);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_acquisitions');
    }
};
