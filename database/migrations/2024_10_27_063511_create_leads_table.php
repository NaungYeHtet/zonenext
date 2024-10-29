<?php

use App\Enums\LeadStatus;
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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('township_id')->nullable()->constrained();
            $table->string('property_type');
            $table->string('interest');
            $table->string('first_name');
            $table->string('last_name');
            $table->boolean('is_owner')->default(true);
            $table->string('address')->nullable();
            $table->string('status')->default(LeadStatus::New);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->string('preferred_contact_time')->nullable();
            $table->boolean('send_updates')->default(false);
            $table->integer('max_price')->nullable();
            $table->mediumInteger('square_feet')->nullable();
            $table->tinyInteger('bedrooms')->nullable();
            $table->tinyInteger('bathrooms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
