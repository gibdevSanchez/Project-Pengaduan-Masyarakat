<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
    public function index()
    {
        $petugas = Petugas::where('level', 'petugas')->latest('id_petugas')->get();
        return view('admin.petugas.index', compact('petugas'));
    }

    public function create()
    {
        return view('admin.petugas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_petugas' => 'required|string|max:50',
            'username'     => 'required|string|max:25|unique:petugas,username',
            'password'     => 'required|string|min:6|confirmed',
            'telp'         => 'required|string|max:13',
        ]);

        Petugas::create([
            'nama_petugas' => $request->nama_petugas,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'telp'         => $request->telp,
            'level'        => 'petugas',
        ]);

        return redirect()->route('admin.petugas.index')
            ->with('success', 'Akun petugas berhasil dibuat.');
    }

    public function edit($id)
    {
        $petugas = Petugas::where('id_petugas', $id)->where('level', 'petugas')->firstOrFail();
        return view('admin.petugas.edit', compact('petugas'));
    }

    public function update(Request $request, $id)
    {
        $petugas = Petugas::where('id_petugas', $id)->where('level', 'petugas')->firstOrFail();

        $request->validate([
            'nama_petugas' => 'required|string|max:50',
            'username'     => 'required|string|max:25|unique:petugas,username,' . $id . ',id_petugas',
            'telp'         => 'required|string|max:13',
            'password'     => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'nama_petugas' => $request->nama_petugas,
            'username'     => $request->username,
            'telp'         => $request->telp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $petugas->update($data);

        return redirect()->route('admin.petugas.index')
            ->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $petugas = Petugas::where('id_petugas', $id)->where('level', 'petugas')->firstOrFail();
        $petugas->delete();

        return redirect()->route('admin.petugas.index')
            ->with('success', 'Akun petugas berhasil dihapus.');
    }
}
