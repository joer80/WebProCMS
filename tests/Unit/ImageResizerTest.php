<?php

use App\Support\ImageResizer;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class);

it('does not resize an image already within the max width', function (): void {
    Storage::fake('media');

    // Create a 800x600 JPEG (well under 1920px)
    $img = imagecreatetruecolor(800, 600);
    ob_start();
    imagejpeg($img);
    $jpeg = ob_get_clean();
    imagedestroy($img);

    Storage::disk('media')->put('posts/small.jpg', $jpeg);

    ImageResizer::resizeToMaxWidth('posts/small.jpg');

    $info = getimagesize(Storage::disk('media')->path('posts/small.jpg'));
    expect($info[0])->toBe(800);
});

it('resizes an oversized image to 1920px wide while preserving aspect ratio', function (): void {
    Storage::fake('media');

    // Create a 3840x2160 (4K) JPEG
    $img = imagecreatetruecolor(3840, 2160);
    ob_start();
    imagejpeg($img);
    $jpeg = ob_get_clean();
    imagedestroy($img);

    Storage::disk('media')->put('posts/large.jpg', $jpeg);

    ImageResizer::resizeToMaxWidth('posts/large.jpg');

    $info = getimagesize(Storage::disk('media')->path('posts/large.jpg'));
    expect($info[0])->toBe(1920);
    expect($info[1])->toBe(1080); // Aspect ratio preserved (2160 * 1920/3840)
});

it('handles a non-existent file without throwing', function (): void {
    Storage::fake('media');

    expect(fn () => ImageResizer::resizeToMaxWidth('posts/ghost.jpg'))->not->toThrow(Exception::class);
});
