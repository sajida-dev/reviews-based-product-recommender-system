<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function hasIndex(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        $result = DB::selectOne(
            'SELECT COUNT(1) AS aggregate
             FROM information_schema.statistics
             WHERE table_schema = ?
               AND table_name = ?
               AND index_name = ?',
            [$database, $table, $index]
        );

        return ((int) ($result->aggregate ?? 0)) > 0;
    }

    /**
     * Run the migrations.
     * Add missing columns and indexes to existing tables
     */
    public function up(): void
    {
        // Add views column safely
        if (! Schema::hasColumn('products', 'views')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('views')->default(0)->after('attributes');
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (! Schema::hasColumn('reviews', 'raw_text')) {
                    $table->text('raw_text')->nullable()->after('review');
                }
                if (! Schema::hasColumn('reviews', 'spam_flagged')) {
                    $table->boolean('spam_flagged')->default(false)->after('is_approved');
                }
            });
        }

        // Reviews indexes
        if (! $this->hasIndex('reviews', 'reviews_user_created_index')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'reviews_user_created_index');
            });
        }
        if (! $this->hasIndex('reviews', 'reviews_product_approved_index')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->index(['product_id', 'is_approved', 'created_at'], 'reviews_product_approved_index');
            });
        }

        // Orders
        if (! $this->hasIndex('orders', 'orders_user_status_created_index')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index(['user_id', 'status', 'created_at'], 'orders_user_status_created_index');
            });
        }

        // Carts
        if (! $this->hasIndex('carts', 'carts_user_id_index')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->index('user_id', 'carts_user_id_index');
            });
        }

        // Cart Items
        if (! $this->hasIndex('cart_items', 'cart_items_cart_product_index')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->index(['cart_id', 'product_id'], 'cart_items_cart_product_index');
            });
        }

        // Fulltext (MySQL only)
        if (DB::getDriverName() === 'mysql' && ! $this->hasIndex('products', 'products_name_description_fulltext')) {
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

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (Schema::hasColumn('reviews', 'spam_flagged')) {
                    $table->dropColumn('spam_flagged');
                }
                if (Schema::hasColumn('reviews', 'raw_text')) {
                    $table->dropColumn('raw_text');
                }
            });
        }

        if ($this->hasIndex('reviews', 'reviews_user_created_index')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropIndex('reviews_user_created_index');
            });
        }
        if ($this->hasIndex('reviews', 'reviews_product_approved_index')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropIndex('reviews_product_approved_index');
            });
        }

        if ($this->hasIndex('orders', 'orders_user_status_created_index')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex('orders_user_status_created_index');
            });
        }

        if ($this->hasIndex('carts', 'carts_user_id_index')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropIndex('carts_user_id_index');
            });
        }

        if ($this->hasIndex('cart_items', 'cart_items_cart_product_index')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropIndex('cart_items_cart_product_index');
            });
        }

        if (DB::getDriverName() === 'mysql' && $this->hasIndex('products', 'products_name_description_fulltext')) {
            DB::statement('ALTER TABLE products DROP INDEX products_name_description_fulltext');
        }
    }
};
