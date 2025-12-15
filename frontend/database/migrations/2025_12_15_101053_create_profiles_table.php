<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('role')->nullable(); // ksm | upkp | dlh
            $table->uuid('id_ksm')->nullable();
            $table->uuid('id_upkp')->nullable();
            $table->timestamps();

            // Jika ingin foreign key (boleh diaktifkan bila tabelnya sudah ada)
            // $table->foreign('id_ksm')->references('id')->on('ksm')->onDelete('set null');
            // $table->foreign('id_upkp')->references('id')->on('upkp')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};