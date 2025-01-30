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
        Schema::create('nameservers', function (Blueprint $table) {
            $table->bigIncrements('id');          // Auto-incrementing primary key
            $table->string('nameserver_name', 255); // Nameserver name (e.g., ns1.example.com)
            $table->string('ip_address', 45);     // IP address (IPv4 or IPv6)
            $table->string('host', 255);          // Domain that the nameserver is associated with
            $table->integer('ttl')->nullable()->default(86400); // TTL (default to 86400 seconds)
            $table->timestamps();
            $table->unique(['nameserver_name']);
               // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nameservers');
    }
};
