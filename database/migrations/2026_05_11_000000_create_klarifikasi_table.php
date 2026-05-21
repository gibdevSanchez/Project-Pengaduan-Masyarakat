<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klarifikasi', function (Blueprint $table) {
            $table->id('id_klarifikasi');
            $table->foreignId('id_pengaduan')
                  ->constrained('pengaduan', 'id_pengaduan')
                  ->cascadeOnDelete();
            $table->text('pesan');
            $table->enum('dari', ['petugas', 'masyarakat']);
            $table->unsignedBigInteger('id_pengirim');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klarifikasi');
    }
};
