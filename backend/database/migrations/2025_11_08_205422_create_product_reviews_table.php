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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('order_detail_id')->nullable()->index();

            // Review content
            $table->tinyInteger('rating')->unsigned()->comment('Rating from 1-5');
            $table->string('title')->nullable();
            $table->text('comment');

            // Media attachments (JSON array of file paths)
            $table->json('images')->nullable()->comment('Array of image paths');
            $table->json('videos')->nullable()->comment('Array of video paths');

            // Review status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->index();
            $table->text('admin_note')->nullable()->comment('Admin note for rejection reason');

            // Helpful votes
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);

            // Verification
            $table->boolean('is_verified_purchase')->default(true)->comment('Verified from completed order');

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onDelete('set null');

            // Indexes
            $table->index(['product_id', 'status', 'created_at']);
            $table->index(['user_id', 'product_id']); // For checking if user already reviewed
            $table->unique(['user_id', 'product_id', 'order_id']); // One review per product per order
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
