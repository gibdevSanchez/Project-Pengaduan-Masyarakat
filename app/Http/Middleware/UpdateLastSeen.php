<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('petugas')->check()) {
            auth('petugas')->user()->updateQuietly(['last_seen_at' => now()]);
        }

        return $next($request);
    }
}
