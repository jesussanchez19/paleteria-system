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
            $result = Cloudinary::upload($file->getRealPath(), [
                'folder' => $folder,
            ]);

            return [
                'public_id' => $result->getPublicId(),
                'url' => $result->getSecurePath(),
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload error: ' . $e->getMessage());
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
            Log::error('Cloudinary delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si Cloudinary está configurado
     */
    public static function isConfigured(): bool
    {
        $cloudUrl = config('cloudinary.cloud_url');
        return !empty($cloudUrl) && $cloudUrl !== 'cloudinary://:@';
    }
}
