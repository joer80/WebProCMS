<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Video extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'video',
        public string $defaultWrapperClasses = 'rounded-card overflow-hidden aspect-video',
        public string $defaultVideoClasses = 'w-full h-full',
        public string $defaultVideoUrl = '',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'video';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => "{$prefix}_video_url", 'default' => $attrs['default-video-url'] ?? ''],
            ['key' => "{$prefix}_wrapper_classes", 'default' => $attrs['default-wrapper-classes'] ?? 'rounded-card overflow-hidden aspect-video'],
            ['key' => "{$prefix}_video_classes", 'default' => $attrs['default-video-classes'] ?? 'w-full h-full'],
        ];
    }

    public static function parseEmbedUrl(string $url): string
    {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return $url;
    }

    public function render(): View
    {
        return view('components.dl.video');
    }
}
