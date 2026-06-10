<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Petugas;
use App\Notifications\Admin\NewPengaduanSubmitted;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'foto.*'      => 'image|max:8192',
            'kategori'    => 'required|in:infrastruktur,lingkungan,keamanan,sosial,lainnya',
            'lokasi'      => 'nullable|string|max:255',
            'lat'         => 'nullable|numeric|between:-90,90',
            'lng'         => 'nullable|numeric|between:-180,180',
        ]);

        do {
            $trackingCode = 'LPR-' . strtoupper(Str::random(6));
        } while (Pengaduan::where('tracking_code', $trackingCode)->exists());

        $pengaduan = Pengaduan::create([
            'masyarakat_id' => $request->boolean('anonim') ? null : auth('masyarakat')->user()->id,
            'isi_laporan'   => $request->isi_laporan,
            'status'        => 'menunggu',
            'kategori'      => $request->kategori,
            'lokasi'        => $request->lokasi ?: null,
            'lat'           => $request->filled('lat') ? $request->lat : null,
            'lng'           => $request->filled('lng') ? $request->lng : null,
            'tracking_code' => $trackingCode,
        ]);

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $pengaduan->fotos()->create([
                    'foto' => ImageService::compressAndStore($file, 'pengaduan'),
                ]);
            }
        }

        $user    = auth('masyarakat')->user();
        $pelapor = $user ? $user->nama : 'Anonim';
        Petugas::where('level', 'admin')->get()->each(
            fn($admin) => $admin->notify(new NewPengaduanSubmitted(
                $pengaduan->id_pengaduan,
                Str::limit($pengaduan->isi_laporan, 50),
                $pengaduan->kategori,
                $pelapor,
            ))
        );

        return redirect()->route('masyarakat.pengaduan.success', $trackingCode);
    }

    public function success(string $code)
    {
        return view('masyarakat.success', ['code' => $code]);
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
