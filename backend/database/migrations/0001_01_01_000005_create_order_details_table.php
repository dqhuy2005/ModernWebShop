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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('product_name')->comment('Snapshot tên sản phẩm tại thời điểm đặt hàng');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price')->default(0)->comment('Giá đơn vị tại thời điểm đặt hàng (VNĐ)');
            $table->unsignedBigInteger('total_price')->default(0)->comment('Thành tiền = unit_price × quantity (VNĐ)');
            $table->json('product_specifications')->nullable()->comment('Snapshot thông số sản phẩm');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
