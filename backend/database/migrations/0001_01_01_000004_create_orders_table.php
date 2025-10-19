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
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->index()->unsigned();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('total_amount')->default(0)->comment('Tổng tiền (VNĐ)');
            $table->unsignedInteger('total_items')->default(0)->comment('Tổng số lượng sản phẩm');
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipping', 'shipped', 'completed', 'delivered', 'cancelled', 'refunded'])->default('pending')->index();
            $table->string(column: 'address')->nullable();
            $table->longText(column: 'note')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
