<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ksm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique()->nullable();
            $table->uuid('id_upkp')->nullable();
            $table->text('nama_ksm');
            $table->text('no_hp');
            $table->text('alamat');
            $table->text('kelurahan');
            $table->text('kecamatan');
            $table->timestamps();

            $table->foreign('id_upkp')->references('id')->on('upkp')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ksm');
    }
};