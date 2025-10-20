<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    protected $manager;
    protected $config;
    protected $useResize;

    public function __construct()
    {
        // Check if GD extension is available
        $this->useResize = extension_loaded('gd');

        // Only initialize ImageManager if GD is available
        if ($this->useResize) {
            try {
                $this->manager = new \Intervention\Image\ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );
            } catch (\Exception $e) {
                $this->useResize = false;
            }
        }

        $this->config = config('image');
    }

    /**
     * Check if image resize is available
     */
    public function canResize(): bool
    {
        return $this->useResize;
    }

    public function uploadProductImage(UploadedFile $file, ?string $oldImage = null): string
    {
        if ($oldImage) {
            $this->deleteImage($oldImage);
        }

        $filename = $this->generateFilename($file);
        $path = $this->config['paths']['products'] . '/' . $filename;

        // If GD is available, resize image
        if ($this->useResize) {
            $image = $this->manager->read($file->getRealPath());
            $sizes = $this->config['sizes']['product'];

            $image->scale(
                width: $sizes['large']['width'],
                height: $sizes['large']['height']
            );

            $encoded = $image->toJpeg(quality: $this->config['quality']);
            Storage::disk('public')->put($path, $encoded);
        } else {
            // Fallback: Store original image without resize
            Storage::disk('public')->putFileAs(
                $this->config['paths']['products'],
                $file,
                $filename
            );
        }

        return $path;
    }

    public function uploadAvatar(UploadedFile $file, ?string $oldAvatar = null): string
    {
        if ($oldAvatar) {
            $this->deleteImage($oldAvatar);
        }

        $filename = $this->generateFilename($file);
        $path = $this->config['paths']['avatars'] . '/' . $filename;

        // If GD is available, resize and crop to square
        if ($this->useResize) {
            $image = $this->manager->read($file->getRealPath());
            $sizes = $this->config['sizes']['avatar'];

            $image->cover(
                width: $sizes['large']['width'],
                height: $sizes['large']['height']
            );

            $encoded = $image->toJpeg(quality: $this->config['quality']);
            Storage::disk('public')->put($path, $encoded);
        } else {
            // Fallback: Store original image without resize
            Storage::disk('public')->putFileAs(
                $this->config['paths']['avatars'],
                $file,
                $filename
            );
        }

        return $path;
    }

    public function deleteImage(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return uniqid() . '_' . time() . '.' . $extension;
    }

    public function validateImage(UploadedFile $file): bool
    {
        if ($file->getSize() > $this->config['max_size'] * 1024) {
            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->config['allowed_extensions'])) {
            return false;
        }

        return true;
    }

    public function createThumbnail(string $imagePath, string $type = 'product'): ?string
    {
        // Can't create thumbnail without GD extension
        if (!$this->useResize) {
            return null;
        }

        if (!Storage::disk('public')->exists($imagePath)) {
            return null;
        }

        $image = $this->manager->read(Storage::disk('public')->path($imagePath));

        $sizes = $this->config['sizes'][$type]['thumbnail'];

        if ($type === 'avatar') {
            $image->cover($sizes['width'], $sizes['height']);
        } else {
            $image->scale($sizes['width'], $sizes['height']);
        }

        $pathInfo = pathinfo($imagePath);
        $thumbnailPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];

        $encoded = $image->toJpeg(quality: $this->config['quality']);
        Storage::disk('public')->put($thumbnailPath, $encoded);

        return $thumbnailPath;
    }
}
