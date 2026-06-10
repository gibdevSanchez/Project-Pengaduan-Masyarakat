<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class UtilitasController extends Controller
{
    public function index()
    {
        $closingTemplate = AppSetting::get(
            'closing_template',
            'Terima kasih telah menggunakan layanan M-Lapor. Pengaduan Anda telah berhasil ditangani oleh petugas kami.'
        );

        $slaHours = [
            'keamanan'      => AppSetting::get('sla_keamanan', '24'),
            'infrastruktur' => AppSetting::get('sla_infrastruktur', '72'),
            'lingkungan'    => AppSetting::get('sla_lingkungan', '48'),
            'sosial'        => AppSetting::get('sla_sosial', '72'),
            'lainnya'       => AppSetting::get('sla_lainnya', '72'),
        ];

        return view('admin.utilitas.index', compact('closingTemplate', 'slaHours'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'closing_template'  => 'required|string|min:10|max:1000',
            'sla_keamanan'      => 'required|integer|min:1|max:720',
            'sla_infrastruktur' => 'required|integer|min:1|max:720',
            'sla_lingkungan'    => 'required|integer|min:1|max:720',
            'sla_sosial'        => 'required|integer|min:1|max:720',
            'sla_lainnya'       => 'required|integer|min:1|max:720',
        ]);

        AppSetting::set('closing_template', $request->closing_template);

        foreach (['keamanan', 'infrastruktur', 'lingkungan', 'sosial', 'lainnya'] as $k) {
            AppSetting::set("sla_{$k}", (string) $request->input("sla_{$k}"));
        }

        return redirect()->route('admin.utilitas')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
