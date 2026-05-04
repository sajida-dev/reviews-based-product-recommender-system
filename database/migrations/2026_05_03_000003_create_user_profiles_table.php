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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // User's interest vector (embedding based on their reviews)
            $table->longText('interests_vector')->nullable();

            // Preferred product categories
            $table->json('preferred_categories')->nullable();

            // When this profile was last updated
            $table->timestamp('last_interest_update')->nullable();

            // Model version used for generating the vector
            $table->string('model_version')->nullable();

            // Overall preference strength (0.0 - 1.0)
            $table->float('preference_score')->default(0.5);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
