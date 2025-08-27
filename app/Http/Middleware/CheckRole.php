<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if ($role === 'super' && $user->role !== 'super') {
            abort(403, 'Access denied. Super user role required.');
        }

        if ($role === 'user' && $user->role !== 'user') {
            abort(403, 'Access denied. User role required.');
        }

        return $next($request);
    }
}
