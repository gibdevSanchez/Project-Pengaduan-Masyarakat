<?php

use App\Models\AppSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'sla_keamanan'      => '24',
            'sla_infrastruktur' => '72',
            'sla_lingkungan'    => '48',
            'sla_sosial'        => '72',
            'sla_lainnya'       => '72',
        ];

        foreach ($defaults as $key => $value) {
            AppSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    public function down(): void
    {
        \DB::table('app_settings')->whereIn('key', [
            'sla_keamanan', 'sla_infrastruktur', 'sla_lingkungan', 'sla_sosial', 'sla_lainnya',
        ])->delete();
    }
};
