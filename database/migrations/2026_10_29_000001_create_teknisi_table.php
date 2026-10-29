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
        Schema::create('teknisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->text('alamat');
            $table->string('nomor_hp', 50);
            $table->string('spesialisasi', 100)->nullable()->default('Teknisi Jaringan');
            $table->string('status', 50)->default('Tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teknisi');
    }
};
