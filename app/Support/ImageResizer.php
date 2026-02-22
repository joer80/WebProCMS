<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class ImageResizer
{
    /**
     * Resize an image stored on the public disk to a maximum width, in place.
     * No-ops if the image is already within the limit or the type is unsupported.
     */
    public static function resizeToMaxWidth(string $storagePath, int $maxWidth = 1920): void
    {
        $fullPath = Storage::disk('public')->path($storagePath);

        if (! file_exists($fullPath)) {
            return;
        }

        $info = @getimagesize($fullPath);

        if (! $info || $info[0] <= $maxWidth) {
            return;
        }

        [$origWidth, $origHeight, $type] = $info;

        $src = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($fullPath),
            IMAGETYPE_PNG => imagecreatefrompng($fullPath),
            IMAGETYPE_WEBP => imagecreatefromwebp($fullPath),
            IMAGETYPE_GIF => imagecreatefromgif($fullPath),
            default => null,
        };

        if (! $src) {
            return;
        }

        $newWidth = $maxWidth;
        $newHeight = (int) round($origHeight * ($maxWidth / $origWidth));
        $dst = imagecreatetruecolor($newWidth, $newHeight);

        if ($type === IMAGETYPE_PNG) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($dst, $fullPath, 85),
            IMAGETYPE_PNG => imagepng($dst, $fullPath),
            IMAGETYPE_WEBP => imagewebp($dst, $fullPath, 85),
            IMAGETYPE_GIF => imagegif($dst, $fullPath),
            default => null,
        };

        imagedestroy($src);
        imagedestroy($dst);
    }
}
