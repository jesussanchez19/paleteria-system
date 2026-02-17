<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
<<<<<<< Updated upstream
    protected $fillable = [
        'user_id',
        'total',
        'sold_at',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
=======
    protected $fillable = ['user_id', 'total', 'payment_method'];
>>>>>>> Stashed changes
}
