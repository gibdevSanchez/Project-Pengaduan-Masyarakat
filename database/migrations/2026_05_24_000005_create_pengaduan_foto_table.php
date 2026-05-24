<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengaduan')
                  ->constrained('pengaduan', 'id_pengaduan')
                  ->cascadeOnDelete();
            $table->string('foto');
            $table->timestamps();
        });

        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan_foto');

        Schema::table('pengaduan', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('isi_laporan');
        });
    }
};
