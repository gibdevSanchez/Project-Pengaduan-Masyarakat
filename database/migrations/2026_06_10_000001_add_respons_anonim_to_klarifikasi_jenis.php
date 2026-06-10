<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite cannot MODIFY COLUMN; drop and recreate with extended enum
            Schema::table('klarifikasi', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });
            Schema::table('klarifikasi', function (Blueprint $table) {
                $table->enum('jenis', ['chat', 'tahapan', 'penutup', 'respons_anonim'])
                    ->default('chat')
                    ->after('dari');
            });
        } else {
            DB::statement("ALTER TABLE klarifikasi MODIFY COLUMN jenis ENUM('chat', 'tahapan', 'penutup', 'respons_anonim') DEFAULT 'chat'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('klarifikasi', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });
            Schema::table('klarifikasi', function (Blueprint $table) {
                $table->enum('jenis', ['chat', 'tahapan', 'penutup'])
                    ->default('chat')
                    ->after('dari');
            });
        } else {
            DB::statement("ALTER TABLE klarifikasi MODIFY COLUMN jenis ENUM('chat', 'tahapan', 'penutup') DEFAULT 'chat'");
        }
    }
};
