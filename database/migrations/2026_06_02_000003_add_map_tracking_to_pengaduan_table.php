<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable()->after('lokasi');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
            $table->string('tracking_code', 12)->nullable()->unique()->after('lng');
        });
    }

    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng', 'tracking_code']);
        });
    }
};
