<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar si el usuario está desactivado
        if ($user->is_active === false) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Tu cuenta está desactivada. Contacta al gerente.');
        }

        if (!in_array($user->role, $roles, true)) {
            abort(403, 'No autorizado');
        }

        return $next($request);
    }
}
