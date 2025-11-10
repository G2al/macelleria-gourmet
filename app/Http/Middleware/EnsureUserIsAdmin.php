<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Se non è loggato → rimandalo al login normale
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Se è cliente → rimandalo alla dashboard
        if (auth()->user()->role !== 'admin') {
            return redirect('/dashboard');
        }

        // Se è admin → tutto ok
        return $next($request);
    }
}
