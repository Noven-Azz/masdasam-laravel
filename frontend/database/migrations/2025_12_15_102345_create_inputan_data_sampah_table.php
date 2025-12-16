<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inputan_data_sampah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_upkp')->nullable();
            $table->uuid('id_ksm')->nullable();
            $table->date('tanggal')->nullable();

            $table->double('hasil_pilahan_bahan_rdf')->nullable();
            $table->double('hasil_pilahan_bursam')->nullable();
            $table->double('hasil_pilahan_residu')->nullable();
            $table->double('hasil_pilahan_rongsok')->nullable();

            $table->double('pengangkutan_bahan_rdf')->nullable();
            $table->double('pengangkutan_bursam')->nullable();
            $table->double('pengangkutan_residu')->nullable();
            $table->double('pengangkutan_rongsok')->nullable();

            $table->double('pemusnahan_sampah_murni')->nullable();
            $table->double('pemusnahan_bahan_rdf')->nullable();
            $table->double('pemusnahan_residu')->nullable();

            $table->double('timbunan_sampah_murni')->nullable();
            $table->double('timbunan_bahan_rdf')->nullable();
            $table->double('timbunan_residu')->nullable();
            $table->double('timbunan_rdf')->nullable();
            $table->double('timbunan_rongsok')->nullable();
            $table->double('timbunan_bursam')->nullable()->default(0);

            $table->double('sampah_masuk')->nullable();
            $table->boolean('sudah_verifikasi')->nullable()->default(false);
            $table->double('penyusutan_jumlah')->nullable();
            $table->double('sampah_diolah')->default(0);
            $table->double('sampah_belum_diolah')->nullable()->default(0);

            $table->timestampsTz();

            $table->foreign('id_ksm')->references('id')->on('ksm')->onDelete('set null');
            $table->foreign('id_upkp')->references('id')->on('upkp')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inputan_data_sampah');
    }
};