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
        // Orders table - for dashboard statistics and filtering
        Schema::table('orders', function (Blueprint $table) {
            // Composite index for status + created_at (used in revenue calculations)
            $table->index(['status', 'created_at'], 'idx_orders_status_created');
            
            // Index for date-based filtering
            $table->index('created_at', 'idx_orders_created_at');
            
            // Index for price range filtering
            $table->index('total_amount', 'idx_orders_total_amount');
        });

        // Carts table - for concurrent access and user lookups
        Schema::table('carts', function (Blueprint $table) {
            // Unique composite index to prevent duplicate cart items + handle race conditions
            $table->unique(['user_id', 'product_id'], 'idx_carts_user_product_unique');
            
            // Index for soft delete queries
            $table->index(['user_id', 'deleted_at'], 'idx_carts_user_deleted');
        });

        // Products table - for search and filtering
        Schema::table('products', function (Blueprint $table) {
            // Composite index for active + hot products
            $table->index(['status', 'is_hot', 'views'], 'idx_products_status_hot_views');
            
            // Index for category filtering
            $table->index(['category_id', 'status'], 'idx_products_category_status');
            
            // Full-text index for product name search (if using MySQL 5.7+)
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE products ADD FULLTEXT idx_products_name_fulltext (name)');
            }
        });

        // Order details - for order summary queries
        Schema::table('order_details', function (Blueprint $table) {
            // Composite index for product sales statistics
            $table->index(['product_id', 'created_at'], 'idx_order_details_product_created');
        });

        // Product views - for analytics
        if (Schema::hasTable('product_views')) {
            Schema::table('product_views', function (Blueprint $table) {
                $table->index(['product_id', 'created_at'], 'idx_product_views_product_created');
                $table->index(['ip_address', 'product_id', 'created_at'], 'idx_product_views_tracking');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status_created');
            $table->dropIndex('idx_orders_created_at');
            $table->dropIndex('idx_orders_total_amount');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique('idx_carts_user_product_unique');
            $table->dropIndex('idx_carts_user_deleted');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_status_hot_views');
            $table->dropIndex('idx_products_category_status');
            
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE products DROP INDEX idx_products_name_fulltext');
            }
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex('idx_order_details_product_created');
        });

        if (Schema::hasTable('product_views')) {
            Schema::table('product_views', function (Blueprint $table) {
                $table->dropIndex('idx_product_views_product_created');
                $table->dropIndex('idx_product_views_tracking');
            });
        }
    }
};
