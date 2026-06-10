<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Pengaduan;
use App\Models\Petugas;
use App\Notifications\Masyarakat\PengaduanStatusUpdated;
use App\Notifications\Petugas\PengaduanAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        $pengaduan  = Pengaduan::withTrashed()->findOrFail($id);
        $oldStatus  = $pengaduan->status;
        $newStatus  = $request->status;

        $pengaduan->update([
            'status'     => $newStatus,
            'selesai_at' => $newStatus === 'selesai' ? now() : $pengaduan->selesai_at,
        ]);

        $admin = auth('petugas')->user();
        activity('pengaduan')
            ->causedBy($admin)
            ->withProperties(['ip' => $request->ip(), 'id_pengaduan' => $id, 'dari' => $oldStatus, 'ke' => $newStatus])
            ->log("Status pengaduan #{$id} diubah: {$oldStatus} → {$newStatus} oleh {$admin->nama_petugas}");

        if ($pengaduan->masyarakat) {
            $pengaduan->masyarakat->notify(new PengaduanStatusUpdated(
                (int) $id,
                Str::limit($pengaduan->isi_laporan, 50),
                $newStatus,
            ));
        }

        return back()->with('success', 'Status diperbarui.');
    }

    public function assign(Request $request, string $id)
    {
        $request->validate(['id_petugas' => 'required|exists:petugas,id_petugas']);

        $pengaduan = Pengaduan::withTrashed()->findOrFail($id);
        $petugas   = Petugas::find($request->id_petugas);
        $pengaduan->update(['id_petugas' => $request->id_petugas]);

        $admin = auth('petugas')->user();
        activity('pengaduan')
            ->causedBy($admin)
            ->withProperties(['ip' => $request->ip(), 'id_pengaduan' => $id, 'id_petugas' => $request->id_petugas])
            ->log("Pengaduan #{$id} di-assign ke {$petugas?->nama_petugas} oleh {$admin->nama_petugas}");

        if ($petugas) {
            $petugas->notify(new PengaduanAssigned(
                (int) $id,
                Str::limit($pengaduan->isi_laporan, 50),
                $pengaduan->kategori,
            ));
        }

        return back()->with('success', 'Pengaduan di-assign.');
    }

    public function destroy(string $id)
    {
        $pengaduan = Pengaduan::findOrFail($id);
        $admin     = auth('petugas')->user();

        activity('pengaduan')
            ->causedBy($admin)
            ->withProperties(['ip' => request()->ip(), 'id_pengaduan' => $id])
            ->log("Pengaduan #{$id} dihapus (soft) oleh {$admin->nama_petugas}");

        $pengaduan->delete();
        return back()->with('success', 'Pengaduan dihapus (bisa dipulihkan).');
    }

    public function forceDestroy(string $id)
    {
        $pengaduan = Pengaduan::withTrashed()->findOrFail($id);
        $admin     = auth('petugas')->user();

        activity('pengaduan')
            ->causedBy($admin)
            ->withProperties(['ip' => request()->ip(), 'id_pengaduan' => $id])
            ->log("Pengaduan #{$id} dihapus permanen oleh {$admin->nama_petugas}");

        $pengaduan->forceDelete();
        return back()->with('success', 'Pengaduan dihapus permanen.');
    }
}
