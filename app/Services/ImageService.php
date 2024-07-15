<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * storeImage
     *
     * @param  string $imagePath
     * @param  string $destinationDirectory
     * @return string
     */
    public static function storeImage(string $imagePath, string $destinationDirectory = 'products'): string
    {
        $uploadedFile = new UploadedFile($imagePath, basename($imagePath));

        $destinationPath = Storage::disk('public')->putFile($destinationDirectory, $uploadedFile);

        if (!$destinationPath) {
            throw new Exception("Failed to store image");
        }

        return $destinationPath;
    }
}
