<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dns_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable(false);
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->rememberToken(); // Add the remember_token column
            $table->string('api_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns_users');
        
    }
};
