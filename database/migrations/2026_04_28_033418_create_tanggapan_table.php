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
        Schema::create('tanggapan', function (Blueprint $table) {
            $table->id('id_tanggapan');
            $table->foreignId('id_pengaduan')
                  ->constrained('pengaduan', 'id_pengaduan')
                  ->cascadeOnDelete();
            $table->date('tgl_tanggapan');
            $table->text('tanggapan');
            $table->foreignId('id_petugas')
                  ->constrained('petugas', 'id_petugas')
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanggapan', function (Blueprint $table) {
            $table->dropForeign(['id_pengaduan']);
            $table->dropForeign(['id_petugas']);
        });
        Schema::dropIfExists('tanggapan');
    }
};
