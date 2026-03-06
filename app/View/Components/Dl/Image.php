<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Image extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'image',
        public string $defaultWrapperClasses = 'rounded-card overflow-hidden aspect-video',
        public string $defaultImageClasses = 'w-full h-full object-cover',
        public string $defaultImage = '',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'image';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => "{$prefix}_image", 'default' => ''],
            ['key' => "{$prefix}_image_alt", 'default' => ''],
            ['key' => "{$prefix}_wrapper_classes", 'default' => $attrs['default-wrapper-classes'] ?? 'rounded-card overflow-hidden aspect-video'],
            ['key' => "{$prefix}_image_classes", 'default' => $attrs['default-image-classes'] ?? 'w-full h-full object-cover'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.image');
    }
}
