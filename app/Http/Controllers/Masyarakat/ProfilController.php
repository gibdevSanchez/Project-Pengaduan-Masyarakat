<?php

namespace App\Http\Controllers\Masyarakat;

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
        $user  = auth('masyarakat')->user();
        $total = Pengaduan::where('masyarakat_id', $user->id)->count();

        $selesai  = Pengaduan::where('masyarakat_id', $user->id)->where('status', 'selesai')->count();
        $diproses = Pengaduan::where('masyarakat_id', $user->id)->where('status', 'proses')->count();

        $stats = [
            'total'    => $total,
            'selesai'  => $selesai,
            'diproses' => $diproses,
        ];

        return view('masyarakat.profil', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = auth('masyarakat')->user();

        $request->validate([
            'telp'          => ['required', 'string', 'max:13'],
            'foto_profil'   => ['nullable', 'image', 'max:8192'],
            'password'      => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $data = ['telp' => $request->telp];

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $data['foto_profil'] = ImageService::compressAndStore($request->file('foto_profil'), 'profil');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        auth('masyarakat')->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
