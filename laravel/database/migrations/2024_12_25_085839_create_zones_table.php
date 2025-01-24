<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(DB::raw('UUID()'));
            $table->string('name')->unique();
            $table->integer('refresh');
            $table->integer('retry');
            $table->integer('expire');
            $table->integer('ttl');
            $table->string('pri_dns');
            $table->string('sec_dns');
            $table->string('www')->nullable();
            $table->string('mail')->nullable();
            $table->string('ftp')->nullable();
            $table->unsignedBigInteger('owner')->nullable();
            $table->foreign('owner')->references('id')->on('dns_users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zones');
    }
}
