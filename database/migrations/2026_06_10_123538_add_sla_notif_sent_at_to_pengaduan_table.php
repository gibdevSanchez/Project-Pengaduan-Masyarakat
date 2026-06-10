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
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->timestamp('sla_notif_sent_at')->nullable()->after('selesai_at');
            $table->timestamp('sla_breach_notif_sent_at')->nullable()->after('sla_notif_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropColumn(['sla_notif_sent_at', 'sla_breach_notif_sent_at']);
        });
    }
};
