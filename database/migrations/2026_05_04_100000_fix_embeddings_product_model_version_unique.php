<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('embeddings', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('embeddings', function (Blueprint $table) {
            $table->dropUnique(['product_id']);
        });

        Schema::table('embeddings', function (Blueprint $table) {
            $table->unique(['product_id', 'model_version']);
        });

        Schema::table('embeddings', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('embeddings', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('embeddings', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'model_version']);
        });

        Schema::table('embeddings', function (Blueprint $table) {
            $table->unique('product_id');
        });

        Schema::table('embeddings', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }
};
