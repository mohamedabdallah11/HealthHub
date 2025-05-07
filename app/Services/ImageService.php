<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    public static function store($image, $disk, $width = 800, $height = 800)
    {
        $filename = 'product_' . time() . '.' . $image->extension();
        // Store original file first
        $path = $image->storeAs('products', $filename, $disk);
        // resize
        $manager = new ImageManager(new Driver());
        $resizedImage = $manager->read($image)
            ->scale($width, $height)
            ->encode();

        Storage::disk($disk)->put($path, $resizedImage);

        return $filename;
    }

    public static function delete($path, $disk)
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        return false;
    }
}
