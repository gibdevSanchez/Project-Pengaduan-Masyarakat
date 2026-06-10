<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Klarifikasi;
use App\Models\Pengaduan;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KlarifikasiController extends Controller
{
    public function index($id): JsonResponse
    {
        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::findOrFail($id);
        abort_if($pengaduan->id_petugas !== $petugas->id_petugas, 403);

        $messages = Klarifikasi::where('id_pengaduan', $id)
            ->orderBy('created_at')
            ->get()
            ->map(fn($k) => [
                'dari'       => $k->dari,
                'jenis'      => $k->jenis,
                'pesan'      => $k->pesan,
                'foto_url'   => $k->foto ? Storage::url($k->foto) : null,
                'created_at' => $k->created_at->format('d M Y, H:i'),
            ]);

        return response()->json($messages);
    }

    public function store(Request $request, $id): JsonResponse
    {
        $request->validate([
            'pesan' => 'required|string|min:3',
            'foto'  => 'nullable|image|max:8192',
        ]);

        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::findOrFail($id);

        abort_if($pengaduan->id_petugas !== $petugas->id_petugas, 403);
        abort_if(is_null($pengaduan->masyarakat_id), 403, 'Pengaduan anonim tidak dapat dikomunikasikan.');
        abort_if(in_array($pengaduan->status, ['selesai', 'tidak_valid']), 409, 'Thread klarifikasi sudah ditutup.');

        $data = [
            'id_pengaduan' => $id,
            'pesan'        => $request->pesan,
            'dari'         => 'petugas',
            'petugas_id'   => $petugas->id_petugas,
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = ImageService::compressAndStore($request->file('foto'), 'klarifikasi');
        }

        $klarifikasi = Klarifikasi::create($data);

        return response()->json([
            'dari'       => 'petugas',
            'jenis'      => 'chat',
            'pesan'      => $klarifikasi->pesan,
            'foto_url'   => $klarifikasi->foto ? Storage::url($klarifikasi->foto) : null,
            'created_at' => $klarifikasi->created_at->format('d M Y, H:i'),
        ], 201);
    }

    public function storeTahapan(Request $request, $id): JsonResponse
    {
        $request->validate([
            'pesan' => 'required|string|min:5',
            'foto'  => 'nullable|image|max:8192',
        ]);

        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::findOrFail($id);

        abort_if($pengaduan->id_petugas !== $petugas->id_petugas, 403);
        abort_if(in_array($pengaduan->status, ['selesai', 'tidak_valid']), 409, 'Pengaduan sudah ditutup.');

        $data = [
            'id_pengaduan' => $id,
            'pesan'        => $request->pesan,
            'dari'         => 'petugas',
            'jenis'        => 'tahapan',
            'petugas_id'   => $petugas->id_petugas,
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = ImageService::compressAndStore($request->file('foto'), 'klarifikasi');
        }

        $klarifikasi = Klarifikasi::create($data);

        return response()->json([
            'jenis'      => 'tahapan',
            'dari'       => 'petugas',
            'pesan'      => $klarifikasi->pesan,
            'foto_url'   => $klarifikasi->foto ? Storage::url($klarifikasi->foto) : null,
            'created_at' => $klarifikasi->created_at->format('d M Y, H:i'),
        ], 201);
    }

    public function indexAnonimRespons($id): JsonResponse
    {
        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::findOrFail($id);
        abort_if($pengaduan->id_petugas !== $petugas->id_petugas, 403);

        $messages = Klarifikasi::where('id_pengaduan', $id)
            ->where('jenis', 'respons_anonim')
            ->orderBy('created_at')
            ->get()
            ->map(fn($k) => [
                'dari'       => $k->dari,
                'pesan'      => $k->pesan,
                'foto_url'   => $k->foto ? Storage::url($k->foto) : null,
                'created_at' => $k->created_at->format('d M Y, H:i'),
            ]);

        return response()->json($messages);
    }

    public function storeAnonimRespons(Request $request, $id): JsonResponse
    {
        $request->validate([
            'pesan' => 'required|string|min:3',
            'foto'  => 'nullable|image|max:8192',
        ]);

        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::findOrFail($id);

        abort_if($pengaduan->id_petugas !== $petugas->id_petugas, 403);
        abort_if(!is_null($pengaduan->masyarakat_id), 403, 'Pengaduan ini bukan anonim.');
        abort_if(in_array($pengaduan->status, ['selesai', 'tidak_valid']), 409, 'Pengaduan sudah ditutup.');

        $data = [
            'id_pengaduan' => $id,
            'pesan'        => $request->pesan,
            'dari'         => 'petugas',
            'jenis'        => 'respons_anonim',
            'petugas_id'   => $petugas->id_petugas,
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = ImageService::compressAndStore($request->file('foto'), 'klarifikasi');
        }

        $klarifikasi = Klarifikasi::create($data);

        return response()->json([
            'dari'       => 'petugas',
            'pesan'      => $klarifikasi->pesan,
            'foto_url'   => $klarifikasi->foto ? Storage::url($klarifikasi->foto) : null,
            'created_at' => $klarifikasi->created_at->format('d M Y, H:i'),
        ], 201);
    }

    public function storeSelesai(Request $request, $id): JsonResponse
    {
        $request->validate(['pesan_tambahan' => 'nullable|string|max:500']);

        $petugas   = auth('petugas')->user();
        $pengaduan = Pengaduan::findOrFail($id);

        abort_if($pengaduan->id_petugas !== $petugas->id_petugas, 403);
        abort_if($pengaduan->status === 'selesai', 409, 'Pengaduan sudah selesai.');

        $template = AppSetting::get(
            'closing_template',
            'Terima kasih telah menggunakan layanan M-Lapor. Pengaduan Anda telah berhasil ditangani oleh petugas kami.'
        );

        $pesanTambahan = $request->filled('pesan_tambahan')
            ? "\n\nPesan dari petugas: " . trim($request->pesan_tambahan)
            : '';

        Klarifikasi::create([
            'id_pengaduan' => $id,
            'pesan'        => $template . $pesanTambahan,
            'dari'         => 'petugas',
            'jenis'        => 'penutup',
            'petugas_id'   => $petugas->id_petugas,
        ]);

        $pengaduan->update([
            'status'     => 'selesai',
            'selesai_at' => now(),
        ]);

        return response()->json(['status' => 'selesai']);
    }
}
