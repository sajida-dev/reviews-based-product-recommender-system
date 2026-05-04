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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->tinyInteger('rating')->unsigned()->comment('1-5');
            $table->text('review')->nullable();

            $table->boolean('verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);

            $table->timestamps();

            $table->unique(['product_id', 'user_id']); // one review per user per product
            $table->index(['product_id', 'rating']);
            $table->softDeletes();

            $table->index(['user_id', 'created_at'], 'reviews_user_created_index');
            $table->index(['product_id', 'is_approved', 'created_at'], 'reviews_product_approved_index');
     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
