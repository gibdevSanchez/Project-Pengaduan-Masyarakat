<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    public static function compressAndStore(UploadedFile $file, string $directory): string
    {
        $path    = $directory . '/' . uniqid() . '.jpg';
        $encoded = Image::read($file)->scaleDown(width: 1200)->toJpeg(80);
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}
