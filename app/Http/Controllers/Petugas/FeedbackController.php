<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\FeedbackPenugasan;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $petugasId = auth('petugas')->user()->id_petugas;

        $individual = FeedbackPenugasan::with('feedback.masyarakat')
            ->where('tipe', 'individual')
            ->where('petugas_id', $petugasId)
            ->where('hidden_by_petugas', false)
            ->latest()
            ->get();

        $global = FeedbackPenugasan::with('feedback.masyarakat')
            ->where('tipe', 'global')
            ->latest()
            ->get();

        return view('petugas.feedback.index', compact('individual', 'global'));
    }

    public function updateStatus(Request $request, FeedbackPenugasan $penugasan)
    {
        $petugasId = auth('petugas')->user()->id_petugas;
        abort_if($penugasan->tipe !== 'individual' || $penugasan->petugas_id !== $petugasId, 403);
        abort_if($penugasan->isFinal(), 403);

        $validated = $request->validate([
            'status' => 'required|in:proses,selesai,invalid',
            'pesan'  => 'required_if:status,selesai|required_if:status,invalid|nullable|string|max:1000',
        ]);

        $penugasan->update([
            'status' => $validated['status'],
            'pesan'  => $validated['pesan'] ?? null,
        ]);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function hide(FeedbackPenugasan $penugasan)
    {
        $petugasId = auth('petugas')->user()->id_petugas;
        abort_if($penugasan->tipe !== 'individual' || $penugasan->petugas_id !== $petugasId, 403);
        abort_if(!$penugasan->isFinal(), 403);

        $penugasan->update(['hidden_by_petugas' => true]);

        return back()->with('success', 'Feedback disembunyikan.');
    }
}
