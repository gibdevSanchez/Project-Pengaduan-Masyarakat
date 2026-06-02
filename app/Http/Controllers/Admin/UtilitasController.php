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

        return view('admin.utilitas.index', compact('closingTemplate'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'closing_template' => 'required|string|min:10|max:1000',
        ]);

        AppSetting::set('closing_template', $request->closing_template);

        return redirect()->route('admin.utilitas')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
