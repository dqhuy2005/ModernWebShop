<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add performance indexes for search and sorting optimization
     */
    public function up(): void
    {
        // Index for orders.status (used in best_selling subquery)
        Schema::table('orders', function (Blueprint $table) {
            if (!$this->indexExists('orders', 'idx_orders_status')) {
                $table->index('status', 'idx_orders_status');
            }
        });

        // Composite index for order_details (product_id, order_id)
        Schema::table('order_details', function (Blueprint $table) {
            if (!$this->indexExists('order_details', 'idx_order_details_product_order')) {
                $table->index(['product_id', 'order_id'], 'idx_order_details_product_order');
            }
        });

        // Composite index for products (status, category_id) - for filtered queries
        Schema::table('products', function (Blueprint $table) {
            if (!$this->indexExists('products', 'idx_products_status_category')) {
                $table->index(['status', 'category_id'], 'idx_products_status_category');
            }
        });

        // Index for products.price (for price range filtering)
        Schema::table('products', function (Blueprint $table) {
            if (!$this->indexExists('products', 'idx_products_price')) {
                $table->index('price', 'idx_products_price');
            }
        });

        // Fulltext index for products.name (for faster search)
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE products ADD FULLTEXT INDEX idx_products_name_fulltext (name)');
        }

        // Composite index for search_histories (user_id, keyword)
        Schema::table('search_histories', function (Blueprint $table) {
            if (!$this->indexExists('search_histories', 'idx_search_histories_user_keyword')) {
                $table->index(['user_id', 'keyword'], 'idx_search_histories_user_keyword');
            }
        });

        // Composite index for search_histories (session_id, keyword)
        Schema::table('search_histories', function (Blueprint $table) {
            if (!$this->indexExists('search_histories', 'idx_search_histories_session_keyword')) {
                $table->index(['session_id', 'keyword'], 'idx_search_histories_session_keyword');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex('idx_order_details_product_order');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_status_category');
            $table->dropIndex('idx_products_price');
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE products DROP INDEX idx_products_name_fulltext');
        }

        Schema::table('search_histories', function (Blueprint $table) {
            $table->dropIndex('idx_search_histories_user_keyword');
            $table->dropIndex('idx_search_histories_session_keyword');
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);
        return !empty($indexes);
    }
};
