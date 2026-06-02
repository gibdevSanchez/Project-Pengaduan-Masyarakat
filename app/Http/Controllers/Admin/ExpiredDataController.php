<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Storage;

class ExpiredDataController extends Controller
{
    public function index()
    {
        $data = Pengaduan::onlyTrashed()
            ->with('masyarakat')
            ->latest('deleted_at')
            ->paginate(20);

        return view('admin.expired.index', compact('data'));
    }

    public function destroy(string $id)
    {
        $pengaduan = Pengaduan::onlyTrashed()->with('fotos')->findOrFail($id);

        $pengaduan->fotos->each(
            fn($foto) => Storage::disk('public')->delete($foto->foto)
        );
        $pengaduan->forceDelete();

        return back()->with('success', 'Data dihapus permanen.');
    }

    public function destroyAll()
    {
        Pengaduan::onlyTrashed()->with('fotos')->get()->each(function ($p) {
            $p->fotos->each(fn($f) => Storage::disk('public')->delete($f->foto));
            $p->forceDelete();
        });

        return back()->with('success', 'Semua expired data dihapus permanen.');
    }
}
