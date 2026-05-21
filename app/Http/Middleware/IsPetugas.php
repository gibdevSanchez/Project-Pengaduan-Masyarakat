<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsPetugas
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('petugas')->check()) {
            return redirect('/login');
        }

        if (auth('petugas')->user()->level !== 'petugas') {
            return redirect('/admin/dashboard');
        }

        return $next($request);
    }
}
