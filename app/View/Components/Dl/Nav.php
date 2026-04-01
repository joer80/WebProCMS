<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Nav extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'nav',
        public string $defaultMenu = 'main-navigation',
        public string $defaultClasses = '',
        public string $defaultItemClasses = '',
        public string $defaultActiveItemClasses = '',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'nav';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => "{$prefix}_menu", 'default' => $attrs['default-menu'] ?? 'main-navigation'],
            ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? ''],
            ['key' => "{$prefix}_item_classes", 'default' => $attrs['default-item-classes'] ?? ''],
            ['key' => "{$prefix}_active_item_classes", 'default' => $attrs['default-active-item-classes'] ?? ''],
            ['key' => "{$prefix}_id", 'default' => '', 'label' => 'Element ID'],
            ['key' => "{$prefix}_attrs", 'default' => '[]', 'label' => 'Custom Attributes'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.nav');
    }
}
