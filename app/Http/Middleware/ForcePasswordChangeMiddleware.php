<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChangeMiddleware
{
    /**
     * Rutas excluidas donde no se fuerza el cambio de contraseña
     */
    protected array $except = [
        'password.change',
        'password.change.update',
        'logout',
        'profile.edit',
        'profile.update',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $forceDays = (int) app_setting('force_password_change_days', '0');
        
        // Si es 0, está deshabilitado
        if ($forceDays <= 0) {
            return $next($request);
        }

        // Verificar si la ruta actual está excluida
        $currentRoute = $request->route()?->getName();
        if ($currentRoute && in_array($currentRoute, $this->except)) {
            return $next($request);
        }

        // Verificar si necesita cambiar contraseña
        $passwordChangedAt = $user->password_changed_at;
        
        if (!$passwordChangedAt) {
            // Nunca ha cambiado contraseña, forzar cambio
            return $this->redirectToPasswordChange($request);
        }

        $daysSinceChange = now()->diffInDays($passwordChangedAt);
        
        if ($daysSinceChange >= $forceDays) {
            return $this->redirectToPasswordChange($request);
        }

        return $next($request);
    }

    protected function redirectToPasswordChange(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Debes cambiar tu contraseña.',
                'redirect' => route('profile.edit'),
            ], 403);
        }

        return redirect()->route('profile.edit')
            ->with('warning', 'Por seguridad, debes cambiar tu contraseña antes de continuar.');
    }
}
