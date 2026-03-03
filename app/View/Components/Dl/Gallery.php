<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Gallery extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'gallery',
        public string $defaultItems = '[{"image":"","alt":"Photo 1","caption":""},{"image":"","alt":"Photo 2","caption":""},{"image":"","alt":"Photo 3","caption":""}]',
        public string $defaultGridClasses = 'grid grid-cols-2 md:grid-cols-3 gap-4',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'gallery';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => "grid_{$prefix}", 'default' => $attrs['default-items'] ?? '[{"image":"","alt":"Photo 1","caption":""},{"image":"","alt":"Photo 2","caption":""},{"image":"","alt":"Photo 3","caption":""}]'],
            ['key' => "{$prefix}_grid_classes", 'default' => $attrs['default-grid-classes'] ?? 'grid grid-cols-2 md:grid-cols-3 gap-4'],
            ['key' => 'toggle_lightbox', 'default' => '1', 'group' => 'images', 'label' => 'Lightbox'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.gallery');
    }
}
