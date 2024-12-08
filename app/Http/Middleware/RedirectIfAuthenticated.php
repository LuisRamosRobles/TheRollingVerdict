<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'ADMIN') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'USER') {
                return redirect()->route('user.dashboard');
            }
        }

        return $next($request);
    }
}
