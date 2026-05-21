<?php

namespace App\Http\Controllers;

use App\Models\Masyarakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showForm()
    {
        if (auth('masyarakat')->check()) {
            return redirect('/masyarakat/dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nik'                  => 'required|digits:16|unique:masyarakat,nik',
            'nama'                 => 'required|string|max:35',
            'username'             => 'required|string|max:25|unique:masyarakat,username',
            'password'             => 'required|string|min:6|confirmed',
            'telp'                 => 'required|string|max:13',
        ]);

        $masyarakat = Masyarakat::create([
            'nik'      => $request->nik,
            'nama'     => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'telp'     => $request->telp,
        ]);

        Auth::guard('masyarakat')->login($masyarakat);
        $request->session()->regenerate();

        return redirect('/masyarakat/dashboard');
    }
}
