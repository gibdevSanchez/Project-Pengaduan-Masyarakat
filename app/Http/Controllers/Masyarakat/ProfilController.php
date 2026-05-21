<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth('masyarakat')->user();
        return view('masyarakat.profil', compact('user'));
    }
}
