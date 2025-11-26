<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->index();
            $table->string('name');
            $table->string('slug')->nullable()->index();
            $table->longText('description')->nullable();
            $table->json('specifications')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->string('currency', 10)->default('VND');
            $table->boolean('status')->default(true)->index();
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('language', 10)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->boolean('is_hot')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            // Indexes
            $table->index('price');
            $table->fullText('name', 'idx_products_name_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
