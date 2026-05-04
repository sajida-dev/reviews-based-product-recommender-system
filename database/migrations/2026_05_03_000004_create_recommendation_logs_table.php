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
        Schema::create('recommendation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recommended_product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Type of recommendation algorithm used
            $table->enum('recommendation_type', [
                'similarity',           // Product similarity
                'collaborative',        // Collaborative filtering
                'sentiment_based',      // Based on aspect sentiments
                'category_based'        // Category preference
            ]);

            // Similarity or recommendation score
            $table->float('score');

            // Position in the recommendation list
            $table->integer('rank');

            // Tracking whether user clicked
            $table->boolean('was_clicked')->default(false);

            // Tracking whether user purchased
            $table->boolean('was_purchased')->default(false);

            $table->timestamps();

            // Indexes for analytics queries
            $table->index(['user_id', 'created_at']);
            $table->index(['product_id', 'recommendation_type']);
            $table->index('recommendation_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendation_logs');
    }
};
