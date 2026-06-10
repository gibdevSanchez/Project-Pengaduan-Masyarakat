<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Pengaduan;
use Illuminate\Http\JsonResponse;
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

        $slaValues = [];
        foreach (['keamanan', 'infrastruktur', 'lingkungan', 'sosial', 'lainnya'] as $k) {
            $slaValues[$k] = (string) $request->input("sla_{$k}");
            AppSetting::set("sla_{$k}", $slaValues[$k]);
        }

        $admin = auth('petugas')->user();
        activity('sistem')
            ->causedBy($admin)
            ->withProperties([
                'ip'  => $request->ip(),
                'sla' => $slaValues,
            ])
            ->log("Pengaturan sistem diubah oleh {$admin->nama_petugas}: SLA & closing template diperbarui");

        return redirect()->route('admin.utilitas')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function petaData(): JsonResponse
    {
        $points = Pengaduan::whereNotNull('lat')
            ->whereNotNull('lng')
            ->select('lat', 'lng', 'kategori', 'status', 'lokasi', 'created_at')
            ->get()
            ->map(fn($p) => [
                'lat'      => (float) $p->lat,
                'lng'      => (float) $p->lng,
                'kategori' => $p->kategori,
                'status'   => $p->status,
                'lokasi'   => $p->lokasi,
                'tgl'      => $p->created_at->format('d M Y'),
            ]);

        return response()->json($points);
    }
}
