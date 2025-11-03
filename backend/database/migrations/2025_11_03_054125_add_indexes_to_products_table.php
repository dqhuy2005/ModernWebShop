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
        Schema::table('products', function (Blueprint $table) {
            // Add indexes for filtering and sorting
            $table->index('price', 'idx_products_price');
            $table->index('category_id', 'idx_products_category_id');
            $table->index('status', 'idx_products_status');
            $table->index('is_hot', 'idx_products_is_hot');
            $table->index('views', 'idx_products_views');
            $table->index('created_at', 'idx_products_created_at');

            // Composite index for common query patterns
            $table->index(['category_id', 'status', 'price'], 'idx_products_category_status_price');
            $table->index(['status', 'is_hot', 'views'], 'idx_products_status_hot_views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop indexes in reverse order
            $table->dropIndex('idx_products_status_hot_views');
            $table->dropIndex('idx_products_category_status_price');
            $table->dropIndex('idx_products_created_at');
            $table->dropIndex('idx_products_views');
            $table->dropIndex('idx_products_is_hot');
            $table->dropIndex('idx_products_status');
            $table->dropIndex('idx_products_category_id');
            $table->dropIndex('idx_products_price');
        });
    }
};
