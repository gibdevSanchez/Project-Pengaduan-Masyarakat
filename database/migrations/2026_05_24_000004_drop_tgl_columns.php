<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropColumn('tgl_pengaduan');
        });

        Schema::table('tanggapan', function (Blueprint $table) {
            $table->dropColumn('tgl_tanggapan');
        });
    }

    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->date('tgl_pengaduan')->nullable()->after('id_pengaduan');
        });

        Schema::table('tanggapan', function (Blueprint $table) {
            $table->date('tgl_tanggapan')->nullable()->after('id_pengaduan');
        });
    }
};
