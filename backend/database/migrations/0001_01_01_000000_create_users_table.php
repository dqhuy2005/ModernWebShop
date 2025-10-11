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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->index()->unsigned();
            $table->string('fullname')->index();;
            $table->string('email')->index()->unique();
            $table->string('phone')->index();
            $table->string('password');
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->string('language')->default('en');
            $table->string('birthday')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            //$table->rememberToken();
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
