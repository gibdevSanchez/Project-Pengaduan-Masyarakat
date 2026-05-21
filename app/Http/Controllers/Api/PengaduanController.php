<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    public function index()
    {
        return response()->json(Pengaduan::latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'isi_laporan' => 'required|string',
            'foto'        => 'nullable|string',
            'kategori'    => 'nullable|in:infrastruktur,lingkungan,keamanan,sosial,lainnya',
            'lokasi'      => 'nullable|string|max:255',
        ]);

        $pengaduan = Pengaduan::create([
            'tgl_pengaduan' => now()->toDateString(),
            'nik'           => auth('sanctum')->user()?->nik,
            'isi_laporan'   => $request->isi_laporan,
            'foto'          => $request->foto,
            'status'        => 'menunggu',
            'kategori'      => $request->kategori ?? 'lainnya',
            'lokasi'        => $request->lokasi,
        ]);

        return response()->json($pengaduan, 201);
    }

    public function show($id)
    {
        return response()->json(Pengaduan::findOrFail($id));
    }

    public function tanggapan($id)
    {
        $pengaduan = Pengaduan::with('tanggapan.petugas')->findOrFail($id);

        return response()->json($pengaduan->tanggapan);
    }
}
