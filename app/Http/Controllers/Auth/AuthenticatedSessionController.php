<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\CashRegister;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // ✅ Bloquear si el usuario está desactivado
        if (! auth()->user()->is_active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Tu cuenta está desactivada. Contacta al gerente o administrador.',
            ]);
        }

        $request->session()->regenerate();

        // Abrir caja automáticamente si es horario laboral y no hay caja abierta
        $user = auth()->user();
        if (in_array($user->role, ['vendedor', 'gerente']) && 
            CashRegister::isBusinessHours() && 
            !CashRegister::isOpenToday()) {
            CashRegister::openForUser($user->id, 0);
        }

        return redirect()->intended(route('redirect.after.login'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
