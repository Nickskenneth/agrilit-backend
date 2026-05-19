<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebCheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = Auth::guard('web')->user();

        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
