<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    public function index(Request $request)
    {
        $tab   = $request->input('tab', 'all');
        $query = Pengaduan::with(['masyarakat', 'petugasAssigned']);

        match ($tab) {
            'invalid'                       => $query->where('status', 'tidak_valid'),
            'menunggu', 'proses', 'selesai' => $query->where('status', $tab),
            default                         => $query->where('status', '!=', 'tidak_valid'),
        };

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->search) {
            $query->where('isi_laporan', 'like', "%{$request->search}%");
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $pengaduan   = $query->latest()->paginate(20)->withQueryString();
        $petugasList = Petugas::where('level', 'petugas')->get();

        $slaHours = [
            'keamanan'      => (int) AppSetting::get('sla_keamanan', '24'),
            'infrastruktur' => (int) AppSetting::get('sla_infrastruktur', '72'),
            'lingkungan'    => (int) AppSetting::get('sla_lingkungan', '48'),
            'sosial'        => (int) AppSetting::get('sla_sosial', '72'),
            'lainnya'       => (int) AppSetting::get('sla_lainnya', '72'),
        ];

        return view('admin.pengaduan.index', compact('pengaduan', 'petugasList', 'tab', 'slaHours'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,proses,selesai,tidak_valid',
        ]);

        $pengaduan = Pengaduan::withTrashed()->findOrFail($id);
        $pengaduan->update([
            'status'     => $request->status,
            'selesai_at' => $request->status === 'selesai' ? now() : $pengaduan->selesai_at,
        ]);

        return back()->with('success', 'Status diperbarui.');
    }

    public function assign(Request $request, string $id)
    {
        $request->validate(['id_petugas' => 'required|exists:petugas,id_petugas']);
        Pengaduan::withTrashed()->findOrFail($id)->update(['id_petugas' => $request->id_petugas]);

        return back()->with('success', 'Pengaduan di-assign.');
    }

    public function destroy(string $id)
    {
        Pengaduan::findOrFail($id)->delete();
        return back()->with('success', 'Pengaduan dihapus (bisa dipulihkan).');
    }

    public function forceDestroy(string $id)
    {
        Pengaduan::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Pengaduan dihapus permanen.');
    }
}
