<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next):Response
    {

        // Verifica si el usuario tiene el rol requerido
        if (Auth::check() && Auth::user()->role !== 'USER') {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
