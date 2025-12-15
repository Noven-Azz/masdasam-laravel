<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengurus_ksm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_ksm')->unique()->nullable();
            $table->text('ketua_ksm');
            $table->text('sekretaris_ksm');
            $table->text('seksi_iuran_pengguna_ksm');
            $table->text('seksi_pengoperasian_dan_pemliharaan_ksm');
            $table->text('seksi_penyuluhan_kesehatan_ksm');
            $table->text('laki_laki')->nullable();
            $table->text('perempuan')->nullable();
            $table->text('bendahara_ksm')->nullable();
            $table->timestamps();

            $table->foreign('id_ksm')->references('id')->on('ksm')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengurus_ksm');
    }
};