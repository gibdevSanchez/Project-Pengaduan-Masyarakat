<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isi'  => 'required|string|min:5|max:2000',
            'foto' => 'nullable|image|max:3072',
        ]);

        $data = [
            'masyarakat_id' => auth('masyarakat')->id(),
            'isi'           => $validated['isi'],
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('feedback', 'public');
        }

        Feedback::create($data);

        return back()->with('success', 'Feedback berhasil dikirim. Terima kasih!');
    }
}
