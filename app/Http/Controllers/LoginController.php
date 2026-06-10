<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use App\Notifications\Admin\BruteForceDetected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

        $guard       = $request->input('guard');
        $credentials = $request->only('username', 'password');
        $ip          = $request->ip();
        $cacheKey    = 'login_attempts_' . $ip;

        if (!Auth::guard($guard)->attempt($credentials)) {
            $attempts = Cache::get($cacheKey, 0) + 1;
            Cache::put($cacheKey, $attempts, now()->addMinutes(10));

            $props = [
                'ip'       => $ip,
                'username' => $request->username,
                'guard'    => $guard,
                'attempts' => $attempts,
            ];

            activity('auth')
                ->withProperties($props)
                ->log("Login gagal: {$request->username} ({$guard})");

            if ($attempts >= 5) {
                activity('auth')
                    ->withProperties($props)
                    ->log("⚠ Brute force terdeteksi: {$attempts}x gagal dari IP {$ip} (username: {$request->username})");

                Petugas::where('level', 'admin')->get()->each(
                    fn($admin) => $admin->notify(new BruteForceDetected($ip, $guard, $attempts))
                );
            }

            return redirect('/login')->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput($request->only('username', 'guard'));
        }

        Cache::forget($cacheKey);

        $user     = Auth::guard($guard)->user();
        $userName = $user->nama_petugas ?? $user->nama;

        activity('auth')
            ->causedBy($user)
            ->withProperties(['ip' => $ip, 'guard' => $guard])
            ->log("Login berhasil: {$userName} ({$guard})");

        $request->session()->regenerate();

        if ($guard === 'petugas') {
            return $user->level === 'admin'
                ? redirect('/admin/dashboard')
                : redirect('/petugas/dashboard');
        }

        return redirect('/masyarakat/dashboard');
    }

    public function logout(Request $request)
    {
        $user  = auth('petugas')->user() ?? auth('masyarakat')->user();
        $guard = auth('petugas')->check() ? 'petugas' : 'masyarakat';

        if ($user) {
            $logoutName = $user->nama_petugas ?? $user->nama;
            activity('auth')
                ->causedBy($user)
                ->withProperties(['ip' => $request->ip(), 'guard' => $guard])
                ->log("Logout: {$logoutName}");
        }

        if (auth('petugas')->check()) {
            auth('petugas')->user()->updateQuietly(['last_seen_at' => null]);
        }

        Auth::guard('petugas')->logout();
        Auth::guard('masyarakat')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
