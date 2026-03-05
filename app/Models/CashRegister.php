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
     * Verificar si estamos en horario laboral (8am - 5pm)
     */
    public static function isBusinessHours(): bool
    {
        $now = now();
        $start = $now->copy()->setTime(8, 30, 0);
        $end = $now->copy()->setTime(17, 0, 0);

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
}
