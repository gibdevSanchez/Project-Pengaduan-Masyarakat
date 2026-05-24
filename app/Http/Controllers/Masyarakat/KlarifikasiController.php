<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Klarifikasi;
use App\Models\Pengaduan;
use Illuminate\Http\Request;

class KlarifikasiController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate(['pesan' => 'required|string|min:3']);

        $masyarakat = auth('masyarakat')->user();
        $pengaduan  = Pengaduan::where('id_pengaduan', $id)
            ->where('masyarakat_id', $masyarakat->id)
            ->firstOrFail();

        abort_if(in_array($pengaduan->status, ['selesai', 'tidak_valid']), 403, 'Thread klarifikasi sudah ditutup.');

        $last = Klarifikasi::where('id_pengaduan', $id)->latest('id_klarifikasi')->first();
        abort_if(is_null($last) || $last->dari !== 'petugas', 403, 'Tidak ada pertanyaan yang menunggu jawaban.');

        Klarifikasi::create([
            'id_pengaduan' => $id,
            'pesan'        => $request->pesan,
            'dari'         => 'masyarakat',
            'masyarakat_id'=> $masyarakat->id,
        ]);

        return redirect()->back()->with('success', 'Jawaban berhasil dikirim.');
    }
}
