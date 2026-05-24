<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('klarifikasi', function (Blueprint $table) {
            $table->unsignedBigInteger('petugas_id')->nullable()->after('dari');
            $table->unsignedBigInteger('masyarakat_id')->nullable()->after('petugas_id');
            $table->foreign('petugas_id')->references('id_petugas')->on('petugas')->nullOnDelete();
            $table->foreign('masyarakat_id')->references('id')->on('masyarakat')->nullOnDelete();
            $table->dropColumn('id_pengirim');
        });
    }

    public function down(): void
    {
        Schema::table('klarifikasi', function (Blueprint $table) {
            $table->dropForeign(['petugas_id']);
            $table->dropForeign(['masyarakat_id']);
            $table->dropColumn(['petugas_id', 'masyarakat_id']);
            $table->unsignedBigInteger('id_pengirim')->after('dari');
        });
    }
};
