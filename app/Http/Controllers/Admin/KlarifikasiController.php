<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klarifikasi;
use App\Models\Pengaduan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
