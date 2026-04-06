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
        Log::info('CloudinaryService: Starting upload to folder: ' . $folder);
        
        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder,
        ]);

        // Obtener los datos de la respuesta
        $response = $result->getResponse();
        
        Log::info('CloudinaryService: Raw response', [
            'response_type' => gettype($response),
            'response' => $response,
        ]);

        // La respuesta puede ser un array o un objeto
        if (is_array($response)) {
            $publicId = $response['public_id'] ?? null;
            $url = $response['secure_url'] ?? $response['url'] ?? null;
        } elseif (is_object($response)) {
            $publicId = $response->public_id ?? null;
            $url = $response->secure_url ?? $response->url ?? null;
        } else {
            // Intentar métodos del objeto result
            $publicId = method_exists($result, 'getPublicId') ? $result->getPublicId() : null;
            $url = method_exists($result, 'getSecurePath') ? $result->getSecurePath() : null;
        }
        
        if (empty($url)) {
            Log::error('CloudinaryService: No URL returned from upload');
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
        
        return $isConfigured;
    }
}
