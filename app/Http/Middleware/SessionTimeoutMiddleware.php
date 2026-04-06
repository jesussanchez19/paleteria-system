<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $timeoutMinutes = (int) app_setting('session_timeout', '120');
        
        // Si el timeout es 0, está deshabilitado
        if ($timeoutMinutes <= 0) {
            return $next($request);
        }

        $lastActivity = session('last_activity_time');
        
        if ($lastActivity && now()->diffInMinutes($lastActivity) >= $timeoutMinutes) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('warning', 'Tu sesión expiró por inactividad. Por favor, inicia sesión nuevamente.');
        }

        // Actualizar última actividad
        session(['last_activity_time' => now()]);
        
        // También actualizar en la base de datos
        if ($user && method_exists($user, 'update')) {
            $user->update(['last_activity_at' => now()]);
        }

        return $next($request);
    }
}
