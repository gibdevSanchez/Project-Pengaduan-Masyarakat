<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('petugas')->check()) {
            return redirect('/login');
        }

        if (auth('petugas')->user()->level !== 'admin') {
            return redirect('/petugas/dashboard');
        }

        return $next($request);
    }
}
