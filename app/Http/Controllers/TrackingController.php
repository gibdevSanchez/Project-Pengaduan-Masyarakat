<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrackingController extends Controller
{
    public function show()
    {
        $code      = request('code');
        $pengaduan = null;
        $notFound  = false;

        if ($code) {
            $pengaduan = Pengaduan::with(['klarifikasi' => fn($q) =>
                $q->whereIn('jenis', ['tahapan', 'penutup', 'respons_anonim'])->orderBy('created_at')
            ])->whereNull('masyarakat_id')
              ->where('tracking_code', strtoupper(trim($code)))->first();

            if (!$pengaduan) {
                $notFound = true;
            }
        }

        if (request()->wantsJson()) {
            if (!$code || !$pengaduan) {
                return response()->json(['found' => false], 404);
            }
            return response()->json([
                'found'           => true,
                'tracking_code'   => $pengaduan->tracking_code,
                'status'          => $pengaduan->status,
                'kategori'        => $pengaduan->kategori,
                'created_at'      => $pengaduan->created_at->format('d M Y, H:i'),
                'lokasi'          => $pengaduan->lokasi,
                'isi_laporan'     => Str::limit($pengaduan->isi_laporan, 200),
                'updated_at_diff' => $pengaduan->updated_at->diffForHumans(),
                'klarifikasi'     => $pengaduan->klarifikasi->map(fn($k) => [
                    'jenis'      => $k->jenis,
                    'pesan'      => $k->pesan,
                    'foto_url'   => $k->foto ? Storage::url($k->foto) : null,
                    'created_at' => $k->created_at->format('d M Y, H:i'),
                ]),
            ]);
        }

        return view('public.track', compact('pengaduan', 'notFound', 'code'));
    }
}
