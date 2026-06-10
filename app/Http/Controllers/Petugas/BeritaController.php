<?php

namespace App\Http\Controllers\Petugas;

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
        $petugas = auth('petugas')->user();
        $berita  = Berita::where('petugas_id', $petugas->id_petugas)->latest()->paginate(15);
        return view('petugas.berita.index', compact('berita'));
    }

    public function create()
    {
        $pengaduanList = Pengaduan::where('status', 'selesai')
            ->where('id_petugas', auth('petugas')->user()->id_petugas)
            ->latest()->get(['id_pengaduan', 'isi_laporan']);
        return view('petugas.berita.create', compact('pengaduanList'));
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

        return redirect()->route('petugas.berita.index')
            ->with('success', 'Berita berhasil dibuat.');
    }

    public function edit(Berita $berita)
    {
        abort_if($berita->petugas_id !== auth('petugas')->user()->id_petugas, 403);

        $pengaduanList = Pengaduan::where('status', 'selesai')
            ->where('id_petugas', auth('petugas')->user()->id_petugas)
            ->latest()->get(['id_pengaduan', 'isi_laporan']);

        return view('petugas.berita.edit', compact('berita', 'pengaduanList'));
    }

    public function update(Request $request, Berita $berita)
    {
        abort_if($berita->petugas_id !== auth('petugas')->user()->id_petugas, 403);

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

        return redirect()->route('petugas.berita.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Berita $berita)
    {
        abort_if($berita->petugas_id !== auth('petugas')->user()->id_petugas, 403);
        $berita->delete();

        return redirect()->route('petugas.berita.index')
            ->with('success', 'Berita dihapus.');
    }
}
