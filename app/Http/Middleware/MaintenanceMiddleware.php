<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    /**
     * Rutas que siempre están permitidas (incluso en mantenimiento)
     */
    protected array $except = [
        'login',
        'logout',
        'mantenimiento',
        'health',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el modo mantenimiento está activado
        $maintenanceMode = app_setting('maintenance_mode', '0');
        
        if ($maintenanceMode !== '1') {
            return $next($request);
        }

        // Permitir rutas excluidas
        $currentRoute = $request->route()?->getName();
        if ($currentRoute && in_array($currentRoute, $this->except)) {
            return $next($request);
        }

        // Permitir a admins navegar normalmente
        $user = $request->user();
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // Si el usuario está autenticado pero no es admin, cerrar sesión y mostrar mantenimiento
        if ($user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Mostrar página de mantenimiento
        return response()->view('maintenance', [], 503);
    }
}
