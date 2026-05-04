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
        Schema::create('aspect_sentiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // The aspect being discussed (e.g., "battery", "camera", "design")
            $table->string('aspect');

            // Sentiment towards that aspect
            $table->enum('sentiment', ['positive', 'negative', 'neutral']);

            // Confidence score (0.0 - 1.0)
            $table->float('confidence');

            // The actual text mentioning this aspect
            $table->text('mention_text')->nullable();

            // Whether this aspect was emphasized (multiple mentions)
            $table->boolean('is_emphasized')->default(false);

            $table->timestamps();

            // Indexes for common queries
            $table->index(['product_id', 'aspect']);
            $table->index(['product_id', 'sentiment']);
            $table->index('review_id');
            $table->index(['aspect', 'sentiment']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspect_sentiments');
    }
};
