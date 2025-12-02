<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('order_detail_id')->nullable()->index();
            $table->tinyInteger('rating')->unsigned();
            $table->string('title')->nullable();
            $table->text('comment');
            $table->json('images')->nullable();
            $table->json('videos')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->index();
            $table->text('admin_note')->nullable();
            $table->boolean('is_verified_purchase')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onDelete('set null');
            $table->index(['product_id', 'status', 'created_at']);
            $table->index(['user_id', 'product_id']);
            $table->unique(['user_id', 'product_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
