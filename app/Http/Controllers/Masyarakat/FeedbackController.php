<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Petugas;
use App\Notifications\Admin\NewFeedbackSubmitted;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isi'  => 'required|string|min:5|max:2000',
            'foto' => 'nullable|image|max:8192',
        ]);

        $data = [
            'masyarakat_id' => auth('masyarakat')->id(),
            'isi'           => $validated['isi'],
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = ImageService::compressAndStore($request->file('foto'), 'feedback');
        }

        $feedback = Feedback::create($data);

        $pengirim = auth('masyarakat')->user()?->nama ?? 'Anonim';
        Petugas::where('level', 'admin')->get()->each(
            fn($admin) => $admin->notify(new NewFeedbackSubmitted(
                $feedback->id,
                Str::limit($feedback->isi, 50),
                $pengirim,
            ))
        );

        return back()->with('success', 'Feedback berhasil dikirim. Terima kasih!');
    }
}
