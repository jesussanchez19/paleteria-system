<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupRecord extends Model
{
    protected $fillable = [
        'filename',
        'size_bytes',
        'created_by',
        'downloaded',
        'downloaded_at',
        'notes',
    ];

    protected $casts = [
        'downloaded' => 'boolean',
        'downloaded_at' => 'datetime',
        'size_bytes' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getSizeFormattedAttribute(): string
    {
        $bytes = $this->size_bytes;
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getStatusAttribute(): string
    {
        if ($this->downloaded) {
            return 'Descargado';
        }
        return 'Pendiente de descargar';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->downloaded ? 'emerald' : 'amber';
    }
}
