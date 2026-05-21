<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        if (auth('petugas')->check()) {
            $user = auth('petugas')->user();
            return redirect($user->level === 'admin' ? '/admin/dashboard' : '/petugas/dashboard');
        }
        if (auth('masyarakat')->check()) {
            return redirect('/masyarakat/dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'guard'    => 'required|in:petugas,masyarakat',
        ]);

        $guard = $request->input('guard');
        $credentials = $request->only('username', 'password');

        if (!Auth::guard($guard)->attempt($credentials)) {
            return redirect('/login')->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'guard'));
        }

        $request->session()->regenerate();

        if ($guard === 'petugas') {
            return Auth::guard('petugas')->user()->level === 'admin'
                ? redirect('/admin/dashboard')
                : redirect('/petugas/dashboard');
        }

        return redirect('/masyarakat/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('petugas')->logout();
        Auth::guard('masyarakat')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
