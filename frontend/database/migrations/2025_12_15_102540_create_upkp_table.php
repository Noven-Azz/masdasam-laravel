<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('upkp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique()->nullable();
            $table->string('nama_upkp');
            $table->string('nama_operator');
            $table->string('no_hp_operator');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upkp');
    }
};