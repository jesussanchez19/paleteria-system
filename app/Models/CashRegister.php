<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegister extends Model
{
    protected $fillable = [
        'user_id',
        'opening_amount',
        'closing_amount',
        'expected_amount',
        'difference',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opening_amount' => 'decimal:2',
        'closing_amount' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'difference' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener la caja abierta del usuario actual
     */
    public static function getOpenRegister(?int $userId = null): ?self
    {
        return static::whereNull('closed_at')
            ->where('user_id', $userId ?? auth()->id())
            ->latest()
            ->first();
    }

    /**
     * Verificar si hay una caja abierta hoy
     */
    public static function isOpenToday(): bool
    {
        return static::whereDate('opened_at', now()->toDateString())
            ->whereNull('closed_at')
            ->exists();
    }

    /**
     * Verificar si estamos en horario laboral (configurable desde settings)
     */
    public static function isBusinessHours(): bool
    {
        $now = now();
        
        // Leer horario desde configuración o usar valores por defecto
        $openTime = app_setting('business_open_time', '08:30');
        $closeTime = app_setting('business_close_time', '17:00');
        
        // Parsear horarios
        [$openHour, $openMin] = array_map('intval', explode(':', $openTime));
        [$closeHour, $closeMin] = array_map('intval', explode(':', $closeTime));
        
        $start = $now->copy()->setTime($openHour, $openMin, 0);
        $end = $now->copy()->setTime($closeHour, $closeMin, 0);

        return $now->between($start, $end);
    }

    /**
     * Abrir caja para el usuario actual
     */
    public static function openForUser(int $userId, float $openingAmount = 0): self
    {
        return static::create([
            'user_id' => $userId,
            'opening_amount' => $openingAmount,
            'opened_at' => now(),
        ]);
    }

    /**
     * Obtener cualquier caja abierta (sin importar usuario)
     */
    public static function getAnyOpenRegister(): ?self
    {
        return static::whereNull('closed_at')
            ->latest()
            ->first();
    }

    /**
     * Cerrar caja automáticamente (por horario)
     */
    public function autoClose(): void
    {
        // Calcular ventas del turno
        $salesDuringShift = \App\Models\Sale::where('created_at', '>=', $this->opened_at)->sum('total');
        $expected = (float)$this->opening_amount + $salesDuringShift;

        $this->update([
            'expected_amount' => $expected,
            'closed_at' => now(),
        ]);

        audit_log('cash.auto_closed', 'caja', $this, [
            'motivo' => 'Cierre automático por fin de horario laboral',
            'esperado' => '$' . number_format($expected, 2),
        ]);
    }

    /**
     * Sincronizar estado de caja con horario laboral
     * - Si estamos en horario y no hay caja → Abrir
     * - Si estamos fuera de horario y hay caja abierta → Cerrar
     */
    public static function syncWithBusinessHours(): ?self
    {
        $openRegister = static::getAnyOpenRegister();
        $isBusinessHours = static::isBusinessHours();

        // Fuera de horario con caja abierta → Cerrar automáticamente
        if (!$isBusinessHours && $openRegister) {
            $openRegister->autoClose();
            return null;
        }

        // En horario sin caja abierta → Abrir automáticamente
        if ($isBusinessHours && !$openRegister) {
            $userId = auth()->id() ?? 1; // Usuario actual o admin por defecto
            $openRegister = static::openForUser($userId, 0);
            
            audit_log('cash.auto_opened', 'caja', $openRegister, [
                'motivo' => 'Apertura automática por inicio de horario laboral',
                'usuario' => auth()->user()?->name ?? 'Sistema',
            ]);
        }

        return $openRegister;
    }

    /**
     * Obtener información del horario laboral
     */
    public static function getBusinessHoursInfo(): array
    {
        $openTime = app_setting('business_open_time', '08:30');
        $closeTime = app_setting('business_close_time', '17:00');
        
        return [
            'open_time' => $openTime,
            'close_time' => $closeTime,
            'is_open' => static::isBusinessHours(),
            'current_time' => now()->format('H:i'),
        ];
    }
}
