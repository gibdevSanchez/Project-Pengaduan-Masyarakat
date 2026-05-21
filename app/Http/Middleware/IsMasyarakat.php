<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsMasyarakat
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('masyarakat')->check()) {
            return redirect('/login');
        }

        return $next($request);
    }
}
