<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Tampung user dan beri tahu Intelephense (bisa bernilai null jika belum login)
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Cek apakah user kosong (belum login) ATAU bukan admin
        if (!$user || !$user->isAdmin()) {
            return redirect('/');
        }

        return $next($request);
    }
}
