<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tanggapan', function (Blueprint $table) {
            $table->dropForeign(['id_petugas']);
            $table->unsignedBigInteger('id_petugas')->nullable()->change();
            $table->foreign('id_petugas')->references('id_petugas')->on('petugas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tanggapan', function (Blueprint $table) {
            $table->dropForeign(['id_petugas']);
            $table->unsignedBigInteger('id_petugas')->nullable(false)->change();
            $table->foreign('id_petugas')->references('id_petugas')->on('petugas')->cascadeOnDelete();
        });
    }
};
