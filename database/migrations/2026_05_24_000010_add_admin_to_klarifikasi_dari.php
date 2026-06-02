<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires explicit ALTER for enum changes; SQLite ignores it (stores as TEXT)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement(
                "ALTER TABLE klarifikasi MODIFY COLUMN dari ENUM('petugas', 'masyarakat', 'admin') NOT NULL"
            );
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement(
                "ALTER TABLE klarifikasi MODIFY COLUMN dari ENUM('petugas', 'masyarakat') NOT NULL"
            );
        }
    }
};
