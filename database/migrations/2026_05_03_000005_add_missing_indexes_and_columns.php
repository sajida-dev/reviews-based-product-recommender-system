<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add missing columns and indexes to existing tables
     */
    public function up(): void
    {
        // Add views column safely
        if (!Schema::hasColumn('products', 'views')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('views')->default(0)->after('attributes');
            });
        }

        // Reviews indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'reviews_user_created_index');
            $table->index(['product_id', 'is_approved', 'created_at'], 'reviews_product_approved_index');
        });

        // Orders
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'created_at'], 'orders_user_status_created_index');
        });

        // Carts
        Schema::table('carts', function (Blueprint $table) {
            $table->index('user_id', 'carts_user_id_index');
        });

        // Cart Items
        Schema::table('cart_items', function (Blueprint $table) {
            $table->index(['cart_id', 'product_id'], 'cart_items_cart_product_index');
        });

        // Fulltext (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE products ADD FULLTEXT products_name_description_fulltext (name, description)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'views')) {
                $table->dropColumn('views');
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_user_created_index');
            $table->dropIndex('reviews_product_approved_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_status_created_index');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('carts_user_id_index');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_cart_product_index');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE products DROP INDEX products_name_description_fulltext');
        }
    }
};
