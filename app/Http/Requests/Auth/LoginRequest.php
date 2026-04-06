<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        $this->ensureUserIsNotLocked();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            $this->incrementFailedAttempts();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Login exitoso - resetear intentos fallidos
        $this->resetFailedAttempts();
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $maxAttempts = (int) app_setting('max_login_attempts', '5');
        
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $maxAttempts)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Verificar si el usuario está bloqueado por intentos fallidos
     */
    protected function ensureUserIsNotLocked(): void
    {
        $user = User::where('email', $this->input('email'))->first();
        
        if (!$user) {
            return;
        }

        if ($user->locked_until && now()->lt($user->locked_until)) {
            $minutes = now()->diffInMinutes($user->locked_until);
            
            throw ValidationException::withMessages([
                'email' => "Tu cuenta está bloqueada. Intenta de nuevo en {$minutes} minutos.",
            ]);
        }

        // Si el bloqueo expiró, resetear
        if ($user->locked_until && now()->gte($user->locked_until)) {
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);
        }
    }

    /**
     * Incrementar intentos fallidos y bloquear si excede el límite
     */
    protected function incrementFailedAttempts(): void
    {
        $user = User::where('email', $this->input('email'))->first();
        
        if (!$user) {
            return;
        }

        $maxAttempts = (int) app_setting('max_login_attempts', '5');
        $newAttempts = $user->failed_login_attempts + 1;

        $updateData = ['failed_login_attempts' => $newAttempts];

        // Si excede el límite, bloquear por 15 minutos
        if ($newAttempts >= $maxAttempts) {
            $updateData['locked_until'] = now()->addMinutes(15);
            
            // Registrar en auditoría
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'action' => 'account.locked',
                'module' => 'auth',
                'entity_type' => 'User',
                'entity_id' => $user->id,
                'meta' => [
                    '_entity_name' => $user->name,
                    'motivo' => "Excedió {$maxAttempts} intentos de login",
                    'bloqueado_hasta' => $updateData['locked_until']->format('d/m/Y H:i'),
                ],
            ]);
        }

        $user->update($updateData);
    }

    /**
     * Resetear intentos fallidos después de login exitoso
     */
    protected function resetFailedAttempts(): void
    {
        $user = Auth::user();
        
        if ($user && ($user->failed_login_attempts > 0 || $user->locked_until)) {
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
