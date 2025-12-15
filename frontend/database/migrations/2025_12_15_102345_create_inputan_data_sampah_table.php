<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inputan_data_sampah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_ksm')->nullable();
            $table->uuid('id_upkp')->nullable();
            $table->date('tanggal');
            $table->decimal('sampah_masuk', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, verified
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Foreign keys (opsional jika pakai SQLite)
            // $table->foreign('id_ksm')->references('id')->on('ksm')->onDelete('cascade');
            // $table->foreign('id_upkp')->references('id')->on('upkp')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inputan_data_sampah');
    }
};