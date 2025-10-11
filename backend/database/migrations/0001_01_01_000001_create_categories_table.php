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
        Schema::create('categories', function (Blueprint $table) {
            $table->id()->index()->unsigned();
            $table->string('name')->index();
            $table->unsignedInteger('parentId')->nullable()->default(null);
            $table->string('language')->default(null);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            //$table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
