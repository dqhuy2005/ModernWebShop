<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('viewed_at');
            $table->timestamps();

            $table->index(['product_id', 'viewed_at']);
            $table->index(['product_id', 'ip_address', 'viewed_at']);
            $table->index(['product_id', 'user_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_views');
    }
};
