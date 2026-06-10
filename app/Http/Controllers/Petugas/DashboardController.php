<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('petugas.dashboard');
    }

    public function complaints(Request $request): JsonResponse
    {
        $petugas = auth('petugas')->user();

        $result = Pengaduan::with(['masyarakat', 'petugasAssigned', 'fotos'])
            ->when(
                $request->boolean('mine'),
                fn($q) => $q->where('id_petugas', $petugas->id_petugas),
                fn($q) => $q->where(fn($q2) => $q2->whereNull('id_petugas')
                                                   ->orWhere('id_petugas', $petugas->id_petugas))
            )
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->kategori, fn($q, $k) => $q->where('kategori', $k))
            ->when($request->search, fn($q, $s) => $q->where('isi_laporan', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15);

        $result->getCollection()->transform(function ($p) {
            return [
                'id'         => $p->id_pengaduan,
                'tgl'        => $p->created_at->toDateString(),
                'nama'       => $p->masyarakat?->nama ?? 'Anonim',
                'isAnonim'   => is_null($p->masyarakat_id),
                'nikMasked'  => $p->masyarakat?->nik ? '****' . substr($p->masyarakat->nik, -4) : null,
                'telp'       => $p->masyarakat?->telp,
                'initials'   => strtoupper(substr($p->masyarakat?->nama ?? 'A', 0, 1)),
                'snippet'    => \Illuminate\Support\Str::limit($p->isi_laporan, 75),
                'isiLaporan' => $p->isi_laporan,
                'fotos'      => $p->fotos->map(fn($f) => \Illuminate\Support\Facades\Storage::url($f->foto))->toArray(),
                'status'     => $p->status,
                'kategori'   => $p->kategori,
                'lokasi'     => $p->lokasi,
                'lat'           => $p->lat ? (float) $p->lat : null,
                'lng'           => $p->lng ? (float) $p->lng : null,
                'id_petugas'    => $p->id_petugas,
                'tracking_code' => $p->tracking_code,
            ];
        });

        return response()->json($result);
    }
}
