<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Berita;

class BeritaController extends Controller
{
    public function index()
    {
        $beritaBesar = Berita::aktifBesar()->latest()->first();

        $berita = Berita::published()
            ->where('format', 'biasa')
            ->with('penulis')
            ->latest()
            ->paginate(10);

        return view('masyarakat.berita', compact('berita', 'beritaBesar'));
    }
}
