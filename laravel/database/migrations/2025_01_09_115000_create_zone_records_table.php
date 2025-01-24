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
        Schema::create('zone_records', function (Blueprint $table) {
            $table->id();
            $table->string('host'); 
            $table->enum('type', ['A', 'AAAA', 'CNAME', 'DNAME', 'DS', 'LOC', 'MX', 'NAPTR', 'NS', 'PTR', 'RP', 'SRV', 'SSHFP', 'TXT', 'WKS']);
            $table->string('destination');
            $table->boolean('valid')->default(true);
            $table->unsignedBigInteger('zone_id')->nullable();            
            $table->timestamps();
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zone_records');
    }
};
