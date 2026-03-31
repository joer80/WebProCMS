<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class ImageResizer
{
    /**
     * Resize an image stored on the media disk to a maximum width, in place.
     * No-ops if the image is already within the limit or the type is unsupported.
     */
    public static function resizeToMaxWidth(string $storagePath, int $maxWidth = 1920): void
    {
        $fullPath = Storage::disk('media')->path($storagePath);
        static::resizeAbsolutePath($fullPath, $maxWidth);
    }

    /**
     * Resize an image at an absolute filesystem path to a maximum width, in place.
     * No-ops if the image is already within the limit or the type is unsupported.
     * Use this before uploading a file to a remote disk.
     */
    public static function resizeAbsolutePath(string $absolutePath, int $maxWidth = 1920): void
    {
        if (! file_exists($absolutePath)) {
            return;
        }

        $info = @getimagesize($absolutePath);

        if (! $info || $info[0] <= $maxWidth) {
            return;
        }

        [$origWidth, $origHeight, $type] = $info;

        $src = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($absolutePath),
            IMAGETYPE_PNG => imagecreatefrompng($absolutePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($absolutePath),
            IMAGETYPE_GIF => imagecreatefromgif($absolutePath),
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
            IMAGETYPE_JPEG => imagejpeg($dst, $absolutePath, 85),
            IMAGETYPE_PNG => imagepng($dst, $absolutePath),
            IMAGETYPE_WEBP => imagewebp($dst, $absolutePath, 85),
            IMAGETYPE_GIF => imagegif($dst, $absolutePath),
            default => null,
        };

        imagedestroy($src);
        imagedestroy($dst);
    }
}
