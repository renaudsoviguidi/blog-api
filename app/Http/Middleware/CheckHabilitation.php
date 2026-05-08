<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckHabilitation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $slug): Response
    {
        $user = Auth::user();

        if (!$user || !$user->hasHabilitation($slug)) {
            abort(403, 'Accès non autorisé');
        }

        return $next($request);
    }
}
