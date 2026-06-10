<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Pengaduan;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    public function index()
    {
        $berita = Berita::with('penulis')->latest()->paginate(20);
        return view('admin.berita.index', compact('berita'));
    }

    public function create()
    {
        $pengaduanList = Pengaduan::where('status', 'selesai')
            ->latest()->get(['id_pengaduan', 'isi_laporan']);
        return view('admin.berita.create', compact('pengaduanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'          => 'required|string|max:200',
            'isi'            => 'required|string|min:10',
            'foto'           => 'nullable|image|max:8192',
            'kategori'       => 'required|in:infrastruktur,lingkungan,keamanan,sosial,lainnya',
            'format'         => 'required|in:biasa,besar',
            'id_pengaduan'   => 'nullable|exists:pengaduan,id_pengaduan',
            'publikasi_mode' => 'required|in:draft,sekarang,jadwalkan',
            'mulai_tayang'   => 'required_if:publikasi_mode,jadwalkan|nullable|date',
            'selesai_tayang' => 'nullable|date',
        ]);

        $data = [
            'judul'        => $validated['judul'],
            'isi'          => $validated['isi'],
            'kategori'     => $validated['kategori'],
            'format'       => $validated['format'],
            'id_pengaduan' => $validated['id_pengaduan'] ?? null,
            'petugas_id'   => auth('petugas')->user()->id_petugas,
        ];

        $mode = $validated['publikasi_mode'];
        if ($mode === 'draft') {
            $data['is_published']   = false;
            $data['mulai_tayang']   = null;
            $data['selesai_tayang'] = null;
        } elseif ($mode === 'sekarang') {
            $data['is_published']   = true;
            $data['mulai_tayang']   = null;
            $data['selesai_tayang'] = $validated['selesai_tayang'] ?? null;
        } else {
            $data['is_published']   = true;
            $data['mulai_tayang']   = $validated['mulai_tayang'];
            $data['selesai_tayang'] = $validated['selesai_tayang'] ?? null;
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = ImageService::compressAndStore($request->file('foto'), 'berita');
        }

        Berita::create($data);

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil dibuat.');
    }

    public function edit(Berita $berita)
    {
        $pengaduanList = Pengaduan::where('status', 'selesai')
            ->latest()->get(['id_pengaduan', 'isi_laporan']);
        return view('admin.berita.edit', compact('berita', 'pengaduanList'));
    }

    public function update(Request $request, Berita $berita)
    {
        $validated = $request->validate([
            'judul'          => 'required|string|max:200',
            'isi'            => 'required|string|min:10',
            'foto'           => 'nullable|image|max:8192',
            'kategori'       => 'required|in:infrastruktur,lingkungan,keamanan,sosial,lainnya',
            'format'         => 'required|in:biasa,besar',
            'id_pengaduan'   => 'nullable|exists:pengaduan,id_pengaduan',
            'publikasi_mode' => 'required|in:draft,sekarang,jadwalkan',
            'mulai_tayang'   => 'required_if:publikasi_mode,jadwalkan|nullable|date',
            'selesai_tayang' => 'nullable|date',
        ]);

        $data = [
            'judul'        => $validated['judul'],
            'isi'          => $validated['isi'],
            'kategori'     => $validated['kategori'],
            'format'       => $validated['format'],
            'id_pengaduan' => $validated['id_pengaduan'] ?? null,
        ];

        $mode = $validated['publikasi_mode'];
        if ($mode === 'draft') {
            $data['is_published']   = false;
            $data['mulai_tayang']   = null;
            $data['selesai_tayang'] = null;
        } elseif ($mode === 'sekarang') {
            $data['is_published']   = true;
            $data['mulai_tayang']   = null;
            $data['selesai_tayang'] = $validated['selesai_tayang'] ?? null;
        } else {
            $data['is_published']   = true;
            $data['mulai_tayang']   = $validated['mulai_tayang'];
            $data['selesai_tayang'] = $validated['selesai_tayang'] ?? null;
        }

        if ($request->hasFile('foto')) {
            if ($berita->foto) {
                Storage::disk('public')->delete($berita->foto);
            }
            $data['foto'] = ImageService::compressAndStore($request->file('foto'), 'berita');
        }

        $berita->update($data);

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Berita $berita)
    {
        if ($berita->foto) {
            Storage::disk('public')->delete($berita->foto);
        }
        $berita->delete();
        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita dihapus.');
    }
}
