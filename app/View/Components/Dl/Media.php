<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Media extends Component
{
    public function __construct(
        public string $slug,
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
        return [
            ['key' => 'toggle_image', 'default' => '1'],
            ['key' => 'image', 'default' => ''],
            ['key' => 'image_alt', 'default' => ''],
            ['key' => 'toggle_image_lazy', 'default' => '', 'label' => 'Lazy Load'],
            ['key' => 'image_object_fit', 'default' => 'cover'],
            ['key' => 'image_border_radius', 'default' => ''],
            ['key' => 'image_wrapper_classes', 'default' => $attrs['default-wrapper-classes'] ?? 'rounded-card overflow-hidden aspect-video'],
            ['key' => 'image_animation', 'default' => '', 'label' => 'Animation'],
            ['key' => 'image_animation_delay', 'default' => '', 'label' => 'Animation Delay'],
            ['key' => 'image_classes', 'default' => $attrs['default-image-classes'] ?? 'w-full h-full object-cover'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.media');
    }
}
