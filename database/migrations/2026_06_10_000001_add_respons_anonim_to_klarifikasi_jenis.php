<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE klarifikasi MODIFY COLUMN jenis ENUM('chat', 'tahapan', 'penutup', 'respons_anonim') DEFAULT 'chat'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE klarifikasi MODIFY COLUMN jenis ENUM('chat', 'tahapan', 'penutup') DEFAULT 'chat'");
    }
};
