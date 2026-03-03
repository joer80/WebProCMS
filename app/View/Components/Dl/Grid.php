<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Grid extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'items',
        public string $defaultItems = '[]',
        public string $defaultGridClasses = 'grid md:grid-cols-3 gap-8',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'items';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => "grid_{$prefix}", 'default' => $attrs['default-items'] ?? '[]'],
            ['key' => "{$prefix}_grid_classes", 'default' => $attrs['default-grid-classes'] ?? 'grid md:grid-cols-3 gap-8'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.grid');
    }
}
