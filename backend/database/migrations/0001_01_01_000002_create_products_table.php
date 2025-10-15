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
            $table->id()->index()->unsigned();
            $table->unsignedBigInteger('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name')->index();
            $table->json('specifications')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('parentId')->nullable()->default(null);
            $table->string('language')->nullable()->default(null);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
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
