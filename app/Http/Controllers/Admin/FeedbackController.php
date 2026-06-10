<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackPenugasan;
use App\Models\Petugas;
use App\Notifications\Petugas\NewFeedbackAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = Feedback::with(['masyarakat', 'penugasan'])
            ->latest()
            ->paginate(30);

        return view('admin.feedback.index', compact('feedback'));
    }

    public function show(Feedback $feedback)
    {
        $feedback->load(['masyarakat', 'penugasan.petugas']);
        $petugasList = Petugas::where('level', 'petugas')->orderBy('nama_petugas')->get();

        return view('admin.feedback.show', compact('feedback', 'petugasList'));
    }

    public function assign(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'tipe'       => 'required|in:individual,global',
            'petugas_id' => 'required_if:tipe,individual|nullable|exists:petugas,id_petugas',
        ]);

        if ($validated['tipe'] === 'global') {
            // Only one global penugasan per feedback
            if ($feedback->penugasan()->where('tipe', 'global')->exists()) {
                return back()->with('error', 'Penugasan global sudah ada untuk feedback ini.');
            }
            FeedbackPenugasan::create([
                'feedback_id' => $feedback->id,
                'tipe'        => 'global',
                'petugas_id'  => null,
            ]);
        } else {
            FeedbackPenugasan::create([
                'feedback_id' => $feedback->id,
                'tipe'        => 'individual',
                'petugas_id'  => $validated['petugas_id'],
            ]);

            $petugas = Petugas::find($validated['petugas_id']);
            $petugas?->notify(new NewFeedbackAssigned(
                $feedback->id,
                Str::limit($feedback->isi, 50),
            ));
        }

        return back()->with('success', 'Feedback berhasil ditugaskan.');
    }

    public function updatePenugasanStatus(Request $request, Feedback $feedback, FeedbackPenugasan $penugasan)
    {
        abort_if($penugasan->feedback_id !== $feedback->id, 404);
        abort_if($penugasan->tipe !== 'global', 403);

        $validated = $request->validate([
            'status' => 'required|in:pending,proses,selesai,invalid',
            'pesan'  => 'required_if:status,selesai|required_if:status,invalid|nullable|string|max:1000',
        ]);

        $penugasan->update([
            'status' => $validated['status'],
            'pesan'  => $validated['pesan'] ?? null,
        ]);

        return back()->with('success', 'Status penugasan diperbarui.');
    }

    public function destroyPenugasan(Feedback $feedback, FeedbackPenugasan $penugasan)
    {
        abort_if($penugasan->feedback_id !== $feedback->id, 404);
        $penugasan->delete();

        return back()->with('success', 'Penugasan dihapus.');
    }
}
