<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullname')->index();
            $table->string('email')->unique();
            $table->foreignId('role_id')->nullable()->constrained('roles')->onUpdate('cascade')->onDelete('set null');
            $table->string('phone')->nullable()->index();
            $table->string('address')->nullable()->index();
            $table->string('password');
            $table->string('image')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->string('language', 10)->default('en');
            $table->date('birthday')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
