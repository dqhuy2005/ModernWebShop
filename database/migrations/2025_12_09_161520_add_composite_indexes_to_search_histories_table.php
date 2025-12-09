<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('search_histories', function (Blueprint $table) {
            $table->index(['user_id', 'updated_at'], 'idx_user_updated');
            $table->index(['session_id', 'updated_at'], 'idx_session_updated');
            $table->index(['keyword', 'updated_at'], 'idx_keyword_updated');
        });
    }

    public function down(): void
    {
        Schema::table('search_histories', function (Blueprint $table) {
            $table->dropIndex('idx_user_updated');
            $table->dropIndex('idx_session_updated');
            $table->dropIndex('idx_keyword_updated');
        });
    }
};
