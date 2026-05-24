<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropForeign(['nik']);
            $table->dropColumn('nik');
            $table->unsignedBigInteger('masyarakat_id')->nullable()->after('id_pengaduan');
            $table->foreign('masyarakat_id')->references('id')->on('masyarakat')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropForeign(['masyarakat_id']);
            $table->dropColumn('masyarakat_id');
            $table->char('nik', 16)->nullable()->after('id_pengaduan');
            $table->foreign('nik')->references('nik')->on('masyarakat')->nullOnDelete();
        });
    }
};
