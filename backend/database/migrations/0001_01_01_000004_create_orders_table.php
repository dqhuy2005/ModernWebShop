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
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->unsignedInteger('total_items')->default(0);
            $table->enum('status', [
                'pending',      // Chờ xử lý
                'confirmed',    // Đã xác nhận
                'processing',   // Đang xử lý
                'shipping',     // Đang giao hàng
                'shipped',      // Đã giao
                'completed',    // Hoàn thành
                'cancelled',    // Đã hủy
                'refunded'      // Đã hoàn tiền
            ])->default('pending')->index();
            $table->string('customer_email')->nullable()->after('user_id');
            $table->string('customer_name')->nullable()->after('customer_email');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->text('address')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

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
