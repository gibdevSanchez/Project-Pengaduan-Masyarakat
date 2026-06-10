<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        Pengaduan::findOrFail($id);

        $messages = Klarifikasi::where('id_pengaduan', $id)
            ->orderBy('created_at')
            ->get()
            ->map(fn($k) => [
                'dari'       => $k->dari,
                'jenis'      => $k->jenis,
                'pesan'      => $k->pesan,
                'created_at' => $k->created_at->format('d M Y, H:i'),
            ]);

        return response()->json($messages);
    }

    public function store(Request $request, $id): JsonResponse
    {
        $request->validate(['pesan' => 'required|string|min:3']);

        $pengaduan = Pengaduan::findOrFail($id);
        abort_if(
            in_array($pengaduan->status, ['selesai', 'tidak_valid']),
            409,
            'Thread klarifikasi sudah ditutup.'
        );

        $admin       = auth('petugas')->user();
        $klarifikasi = Klarifikasi::create([
            'id_pengaduan' => $id,
            'pesan'        => $request->pesan,
            'dari'         => 'admin',
            'jenis'        => 'chat',
            'petugas_id'   => $admin->id_petugas,
        ]);

        return response()->json([
            'dari'       => 'admin',
            'jenis'      => 'chat',
            'pesan'      => $klarifikasi->pesan,
            'created_at' => $klarifikasi->created_at->format('d M Y, H:i'),
        ], 201);
    }

    public function indexAnonimRespons($id): JsonResponse
    {
        Pengaduan::findOrFail($id);

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

        $pengaduan = Pengaduan::findOrFail($id);
        abort_if(!is_null($pengaduan->masyarakat_id), 403, 'Pengaduan ini bukan anonim.');
        abort_if(in_array($pengaduan->status, ['selesai', 'tidak_valid']), 409, 'Pengaduan sudah ditutup.');

        $admin = auth('petugas')->user();
        $data  = [
            'id_pengaduan' => $id,
            'pesan'        => $request->pesan,
            'dari'         => 'admin',
            'jenis'        => 'respons_anonim',
            'petugas_id'   => $admin->id_petugas,
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = ImageService::compressAndStore($request->file('foto'), 'klarifikasi');
        }

        $klarifikasi = Klarifikasi::create($data);

        return response()->json([
            'dari'       => 'admin',
            'pesan'      => $klarifikasi->pesan,
            'foto_url'   => $klarifikasi->foto ? Storage::url($klarifikasi->foto) : null,
            'created_at' => $klarifikasi->created_at->format('d M Y, H:i'),
        ], 201);
    }
}
