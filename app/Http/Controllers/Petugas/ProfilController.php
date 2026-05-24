<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $petugas = auth('petugas')->user();
        return view('petugas.profil', compact('petugas'));
    }

    public function update(Request $request)
    {
        $petugas = auth('petugas')->user();

        $request->validate([
            'telp'          => ['required', 'string', 'max:13'],
            'foto_profil'   => ['nullable', 'image', 'max:2048'],
            'password'      => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $data = ['telp' => $request->telp];

        if ($request->hasFile('foto_profil')) {
            if ($petugas->foto_profil) {
                Storage::disk('public')->delete($petugas->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('profil', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $petugas->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
