<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Subir imagen a Cloudinary
     */
    public function uploadImage(UploadedFile $file, string $folder = 'products'): array
    {
        try {
            Log::info('CloudinaryService: Starting upload to folder: ' . $folder);
            
            $result = Cloudinary::upload($file->getRealPath(), [
                'folder' => $folder,
            ]);

            $publicId = $result->getPublicId();
            $url = $result->getSecurePath();
            
            Log::info('CloudinaryService: Upload successful', [
                'public_id' => $publicId,
                'url' => $url,
            ]);

            return [
                'public_id' => $publicId,
                'url' => $url,
            ];
        } catch (\Exception $e) {
            Log::error('CloudinaryService: Upload error - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar imagen de Cloudinary
     */
    public function deleteImage(?string $publicId): bool
    {
        if (!$publicId) {
            return false;
        }

        try {
            Cloudinary::destroy($publicId);
            return true;
        } catch (\Exception $e) {
            Log::error('CloudinaryService: Delete error - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si Cloudinary está configurado
     */
    public static function isConfigured(): bool
    {
        // Intentar obtener de config primero, luego de env directamente
        $cloudUrl = config('cloudinary.cloud_url') ?: env('CLOUDINARY_URL');
        
        $isConfigured = !empty($cloudUrl) 
            && $cloudUrl !== 'cloudinary://:@'
            && str_contains($cloudUrl, '@');
        
        Log::debug('CloudinaryService: isConfigured check', [
            'cloud_url_exists' => !empty($cloudUrl),
            'is_valid' => $isConfigured,
        ]);
        
        return $isConfigured;
    }
}
