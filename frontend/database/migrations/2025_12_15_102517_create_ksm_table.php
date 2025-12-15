<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ksm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_ksm');
            $table->uuid('id_upkp')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('no_telepon')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('pengurus_ksm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_ksm');
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('no_telepon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengurus_ksm');
        Schema::dropIfExists('ksm');
    }
};