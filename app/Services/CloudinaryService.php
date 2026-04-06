<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    /**
     * Subir imagen a Cloudinary
     */
    public function uploadImage(UploadedFile $file, string $folder = 'products'): array
    {
        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder,
            'resource_type' => 'image',
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ],
        ]);

        return [
            'public_id' => $result->getPublicId(),
            'url' => $result->getSecurePath(),
        ];
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
            report($e);
            return false;
        }
    }

    /**
     * Verificar si Cloudinary está configurado
     */
    public static function isConfigured(): bool
    {
        return !empty(env('CLOUDINARY_URL')) || 
               (!empty(env('CLOUDINARY_CLOUD_NAME')) && 
                !empty(env('CLOUDINARY_KEY')) && 
                !empty(env('CLOUDINARY_SECRET')));
    }
}
