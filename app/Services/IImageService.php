<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

interface IImageService
{
    public function uploadCategoryImage(UploadedFile $file, ?string $oldImage = null): string;
    
    public function uploadProductImage(UploadedFile $file, ?string $oldImage = null): string;
    
    public function uploadAvatar(UploadedFile $file, ?string $oldAvatar = null): string;
    
    public function deleteImage(string $path): bool;
    
    public function deleteAvatarByUrl(string $url): bool;
    
    public function validateImage(UploadedFile $file): bool;
}
