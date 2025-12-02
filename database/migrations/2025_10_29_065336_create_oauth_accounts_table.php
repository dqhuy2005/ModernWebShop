<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('oauth_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('provider');
            $table->string('provider_id');
            $table->string('provider_token', 500)->nullable();
            $table->string('provider_refresh_token', 500)->nullable();
            $table->string('avatar')->nullable();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_accounts');
    }
};
