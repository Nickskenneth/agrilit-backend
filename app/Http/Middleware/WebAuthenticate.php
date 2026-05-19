<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Pastikan hanya pakar dan admin yang bisa akses CMS
        $user = Auth::guard('web')->user();
        if (!in_array($user->role, ['pakar', 'admin'])) {
            Auth::guard('web')->logout();
            return redirect()->route('login')
                ->with('error', 'Akses ditolak. Hanya untuk pakar dan admin.');
        }

        return $next($request);
    }
}
