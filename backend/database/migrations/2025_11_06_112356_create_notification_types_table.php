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
        Schema::create('notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Mã loại thông báo: order_created, order_confirmed, otp, etc.');
            $table->string('name', 100)->comment('Tên hiển thị của loại thông báo');
            $table->string('category', 50)->default('general')->comment('Nhóm: order, user, promotion, system');
            $table->text('description')->nullable()->comment('Mô tả chi tiết loại thông báo');
            $table->boolean('is_active')->default(true)->comment('Trạng thái kích hoạt');
            $table->boolean('email_enabled')->default(true)->comment('Cho phép gửi email');
            $table->boolean('sms_enabled')->default(false)->comment('Cho phép gửi SMS');
            $table->boolean('push_enabled')->default(false)->comment('Cho phép push notification');
            $table->json('default_config')->nullable()->comment('Cấu hình mặc định (retry_count, delay, etc.)');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('code');
            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_types');
    }
};
