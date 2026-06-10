<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $petugas = auth('petugas')->user();

        $stats = [
            'total'   => Pengaduan::where('id_petugas', $petugas->id_petugas)->count(),
            'selesai' => Pengaduan::where('id_petugas', $petugas->id_petugas)->where('status', 'selesai')->count(),
            'ditolak' => Pengaduan::where('id_petugas', $petugas->id_petugas)->where('status', 'tidak_valid')->count(),
        ];

        return view('petugas.profil', compact('petugas', 'stats'));
    }

    public function update(Request $request)
    {
        $petugas = auth('petugas')->user();

        $request->validate([
            'telp'        => ['required', 'string', 'max:13'],
            'foto_profil' => ['nullable', 'image', 'max:8192'],
            'password'    => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $data            = ['telp' => $request->telp];
        $changedPassword = false;
        $changedFoto     = false;

        if ($request->hasFile('foto_profil')) {
            if ($petugas->foto_profil) {
                Storage::disk('public')->delete($petugas->foto_profil);
            }
            $data['foto_profil'] = ImageService::compressAndStore($request->file('foto_profil'), 'profil');
            $changedFoto         = true;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $changedPassword  = true;
        }

        $petugas->update($data);

        $changes = collect([
            $changedPassword ? 'password diubah' : null,
            $changedFoto     ? 'foto profil diubah' : null,
            'nomor telepon diperbarui',
        ])->filter()->implode(', ');

        activity('profil')
            ->causedBy($petugas)
            ->withProperties([
                'ip'              => $request->ip(),
                'role'            => 'petugas',
                'password_changed'=> $changedPassword,
                'foto_changed'    => $changedFoto,
            ])
            ->log("Profil diperbarui oleh petugas {$petugas->nama_petugas}: {$changes}");

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
