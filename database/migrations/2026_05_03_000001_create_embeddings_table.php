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
        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();

            // Store embedding as JSON array
            $table->longText('embedding');

            // Model version for tracking updates
            $table->string('model_version')->default('sentence-transformers/all-MiniLM-L6-v2');

            // Dimension of the embedding (usually 384 for all-MiniLM)
            $table->integer('dimension')->nullable();

            // Reference to Qdrant vector ID (if using external VDB)
            $table->bigInteger('qdrant_id')->nullable();

            // Additional metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes for querying
            $table->index('model_version');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('embeddings');
    }
};
