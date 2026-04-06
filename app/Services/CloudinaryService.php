<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private ?Cloudinary $cloudinary = null;
    
    public function __construct()
    {
        $cloudUrl = env('CLOUDINARY_URL');
        if (!empty($cloudUrl) && str_contains($cloudUrl, '@')) {
            $this->cloudinary = new Cloudinary($cloudUrl);
        }
    }
    
    /**
     * Subir imagen a Cloudinary
     */
    public function uploadImage(UploadedFile $file, string $folder = 'products'): array
    {
        if (!$this->cloudinary) {
            throw new \Exception('Cloudinary no está configurado');
        }
        
        Log::info('CloudinaryService: Starting upload to folder: ' . $folder);
        
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder,
        ]);

        Log::info('CloudinaryService: Upload result', [
            'type' => gettype($result),
            'keys' => is_array($result) ? array_keys($result) : 'not array',
        ]);

        $publicId = $result['public_id'] ?? null;
        $url = $result['secure_url'] ?? $result['url'] ?? null;
        
        if (empty($url)) {
            Log::error('CloudinaryService: No URL in result', ['result' => $result]);
            throw new \Exception('Cloudinary no devolvió URL de imagen');
        }
        
        Log::info('CloudinaryService: Upload successful', [
            'public_id' => $publicId,
            'url' => $url,
        ]);

        return [
            'public_id' => $publicId,
            'url' => $url,
        ];
    }

    /**
     * Eliminar imagen de Cloudinary
     */
    public function deleteImage(?string $publicId): bool
    {
        if (!$publicId || !$this->cloudinary) {
            return false;
        }

        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
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
        $cloudUrl = env('CLOUDINARY_URL');
        
        return !empty($cloudUrl) 
            && $cloudUrl !== 'cloudinary://:@'
            && str_contains($cloudUrl, '@');
    }
}
