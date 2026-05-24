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
            'foto'        => 'nullable|array|max:5',
            'foto.*'      => 'image|max:2048',
            'kategori'    => 'required|in:infrastruktur,lingkungan,keamanan,sosial,lainnya',
            'lokasi'      => 'nullable|string|max:255',
        ]);

        $pengaduan = Pengaduan::create([
            'masyarakat_id' => $request->boolean('anonim') ? null : auth('masyarakat')->user()->id,
            'isi_laporan'=> $request->isi_laporan,
            'status'     => 'menunggu',
            'kategori'   => $request->kategori,
            'lokasi'     => $request->lokasi,
        ]);

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $pengaduan->fotos()->create([
                    'foto' => $file->store('pengaduan', 'public'),
                ]);
            }
        }

        return redirect()->route('masyarakat.dashboard')
            ->with('success', 'Pengaduan berhasil dikirim! Kami akan segera menindaklanjutinya.');
    }

    public function show(string $id)
    {
        $pengaduan = Pengaduan::with(['tanggapan.petugas', 'klarifikasi', 'fotos'])
            ->where('id_pengaduan', $id)
            ->where('masyarakat_id', auth('masyarakat')->user()->id)
            ->firstOrFail();

        return view('masyarakat.detail', compact('pengaduan'));
    }
}
