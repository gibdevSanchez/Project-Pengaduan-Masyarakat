<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id('id_pengaduan');
            $table->date('tgl_pengaduan');
            $table->char('nik', 16)->nullable();
            $table->text('isi_laporan');
            $table->string('foto', 255)->nullable();
            $table->enum('status', ['menunggu', 'proses', 'selesai', 'tidak_valid'])->default('menunggu');
            $table->text('takedown_reason')->nullable();
            $table->enum('kategori', ['infrastruktur', 'lingkungan', 'keamanan', 'sosial', 'lainnya'])->default('lainnya');
            $table->string('lokasi', 255)->nullable();
            $table->unsignedBigInteger('id_petugas')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('nik')->references('nik')->on('masyarakat')->nullOnDelete();
            $table->foreign('id_petugas')->references('id_petugas')->on('petugas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropForeign(['nik']);
            $table->dropForeign(['id_petugas']);
        });
        Schema::dropIfExists('pengaduan');
    }
};
