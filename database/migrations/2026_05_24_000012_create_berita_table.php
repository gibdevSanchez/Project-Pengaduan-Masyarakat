<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('isi');
            $table->enum('kategori', ['infrastruktur', 'lingkungan', 'keamanan', 'sosial', 'lainnya']);
            $table->enum('format', ['biasa', 'besar'])->default('biasa');
            $table->unsignedBigInteger('petugas_id');
            $table->unsignedBigInteger('id_pengaduan')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('mulai_tayang')->nullable();
            $table->timestamp('selesai_tayang')->nullable();
            $table->timestamps();

            $table->foreign('petugas_id')->references('id_petugas')->on('petugas')->cascadeOnDelete();
            $table->foreign('id_pengaduan')->references('id_pengaduan')->on('pengaduan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
