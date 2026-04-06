<?php

namespace App\Models;

use App\Services\CloudinaryService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock',
        'category',
        'sale_type',
        'pieces_per_package',
        'image',
        'cloudinary_public_id',
        'description',
        'is_active',
    ];

    /**
     * URL completa de la imagen
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            // Si la imagen empieza con http, es una URL de Cloudinary
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            // Si no, es una imagen local
            return Storage::disk('public')->url($this->image);
        }
        return null;
    }

    /**
     * Verificar si la imagen está en Cloudinary
     */
    public function isCloudinaryImage(): bool
    {
        return !empty($this->cloudinary_public_id);
    }
}
