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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_type_id')->constrained('notification_types')->onDelete('cascade');
            $table->string('name', 100)->comment('Tên template');
            $table->string('subject', 255)->comment('Tiêu đề email (có thể chứa biến {{variable}})');
            $table->text('body_html')->comment('Nội dung HTML của email');
            $table->text('body_text')->nullable()->comment('Nội dung text thuần (fallback)');
            $table->json('available_variables')->nullable()->comment('Danh sách biến có thể sử dụng: ["order_id", "customer_name"]');
            $table->string('locale', 10)->default('vi')->comment('Ngôn ngữ: vi, en');
            $table->boolean('is_active')->default(true)->comment('Trạng thái kích hoạt');
            $table->boolean('is_default')->default(false)->comment('Template mặc định cho loại này');
            $table->integer('version')->default(1)->comment('Phiên bản template');
            $table->text('preview_data')->nullable()->comment('Dữ liệu mẫu để preview');
            $table->timestamps();
            $table->softDeletes();

            $table->index('notification_type_id');
            $table->index('locale');
            $table->index('is_active');
            $table->index(['notification_type_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
