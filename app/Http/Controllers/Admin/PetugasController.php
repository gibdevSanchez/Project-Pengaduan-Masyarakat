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

        $petugas = Petugas::create([
            'nama_petugas' => $request->nama_petugas,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'telp'         => $request->telp,
            'level'        => 'petugas',
        ]);

        $admin = auth('petugas')->user();
        activity('petugas')
            ->causedBy($admin)
            ->withProperties(['ip' => $request->ip(), 'id_petugas' => $petugas->id_petugas, 'username' => $petugas->username])
            ->log("Petugas dibuat: {$petugas->nama_petugas} (@{$petugas->username}) oleh {$admin->nama_petugas}");

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

        $changedPassword = false;
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $changedPassword   = true;
        }

        $petugas->update($data);

        $admin = auth('petugas')->user();
        activity('petugas')
            ->causedBy($admin)
            ->withProperties([
                'ip'              => $request->ip(),
                'id_petugas'      => $petugas->id_petugas,
                'username'        => $petugas->username,
                'password_changed'=> $changedPassword,
            ])
            ->log("Petugas diperbarui: {$petugas->nama_petugas} (@{$petugas->username}) oleh {$admin->nama_petugas}");

        return redirect()->route('admin.petugas.index')
            ->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $petugas = Petugas::where('id_petugas', $id)->where('level', 'petugas')->firstOrFail();

        $admin = auth('petugas')->user();
        activity('petugas')
            ->causedBy($admin)
            ->withProperties(['ip' => request()->ip(), 'id_petugas' => $petugas->id_petugas, 'username' => $petugas->username])
            ->log("Petugas dihapus: {$petugas->nama_petugas} (@{$petugas->username}) oleh {$admin->nama_petugas}");

        $petugas->delete();

        return redirect()->route('admin.petugas.index')
            ->with('success', 'Akun petugas berhasil dihapus.');
    }
}
