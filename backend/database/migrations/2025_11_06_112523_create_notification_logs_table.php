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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_type_id')->constrained('notification_types')->onDelete('cascade');
            $table->foreignId('email_template_id')->nullable()->constrained('email_templates')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Người nhận');
            $table->string('recipient_email', 255)->comment('Email người nhận');
            $table->string('recipient_name', 255)->nullable()->comment('Tên người nhận');
            $table->string('recipient_phone', 20)->nullable()->comment('SĐT người nhận (cho SMS)');

            // Reference to related entity (order, user, promotion, etc.)
            $table->string('related_type', 50)->nullable()->comment('Loại entity liên quan: Order, User, Promotion');
            $table->unsignedBigInteger('related_id')->nullable()->comment('ID của entity liên quan');

            $table->string('channel', 20)->default('email')->comment('Kênh gửi: email, sms, push');
            $table->enum('status', ['pending', 'sending', 'sent', 'failed', 'read', 'clicked'])->default('pending');
            $table->string('subject', 255)->nullable()->comment('Tiêu đề email đã gửi');
            $table->text('content')->nullable()->comment('Nội dung đã gửi (để tracking)');
            $table->json('template_data')->nullable()->comment('Dữ liệu đã render vào template');

            // Tracking & retry
            $table->integer('retry_count')->default(0)->comment('Số lần thử gửi lại');
            $table->integer('max_retry')->default(3)->comment('Số lần thử tối đa');
            $table->timestamp('scheduled_at')->nullable()->comment('Thời gian lên lịch gửi');
            $table->timestamp('sent_at')->nullable()->comment('Thời gian gửi thành công');
            $table->timestamp('read_at')->nullable()->comment('Thời gian đọc (nếu tracking)');
            $table->timestamp('clicked_at')->nullable()->comment('Thời gian click link (nếu tracking)');
            $table->timestamp('failed_at')->nullable()->comment('Thời gian thất bại');

            // Error handling
            $table->text('error_message')->nullable()->comment('Thông báo lỗi nếu gửi thất bại');
            $table->json('error_trace')->nullable()->comment('Chi tiết lỗi kỹ thuật');

            // Email service info
            $table->string('email_service', 50)->nullable()->comment('Service đã gửi: smtp, sendgrid, ses, etc.');
            $table->string('message_id', 255)->nullable()->comment('ID từ email service để tracking');

            $table->text('notes')->nullable()->comment('Ghi chú thêm');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('notification_type_id');
            $table->index('user_id');
            $table->index('recipient_email');
            $table->index('status');
            $table->index('channel');
            $table->index(['related_type', 'related_id']);
            $table->index('scheduled_at');
            $table->index('sent_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
