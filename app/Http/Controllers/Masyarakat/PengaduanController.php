<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    public function create()
    {
        return view('masyarakat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'isi_laporan' => 'required|string|min:10',
            'foto'        => 'nullable|image|max:2048',
            'kategori'    => 'required|in:infrastruktur,lingkungan,keamanan,sosial,lainnya',
            'lokasi'      => 'nullable|string|max:255',
        ]);

        $foto = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('pengaduan', 'public');
        }

        Pengaduan::create([
            'tgl_pengaduan' => now()->toDateString(),
            'nik'           => $request->boolean('anonim') ? null : auth('masyarakat')->user()->nik,
            'isi_laporan'   => $request->isi_laporan,
            'foto'          => $foto,
            'status'        => 'menunggu',
            'kategori'      => $request->kategori,
            'lokasi'        => $request->lokasi,
        ]);

        return redirect()->route('masyarakat.dashboard')
            ->with('success', 'Pengaduan berhasil dikirim! Kami akan segera menindaklanjutinya.');
    }

    public function show(string $id)
    {
        $pengaduan = Pengaduan::with(['tanggapan.petugas', 'klarifikasi'])
            ->where('id_pengaduan', $id)
            ->where('nik', auth('masyarakat')->user()->nik)
            ->firstOrFail();

        return view('masyarakat.detail', compact('pengaduan'));
    }
}
