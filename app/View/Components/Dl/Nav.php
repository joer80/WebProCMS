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
        public string $defaultDropdownClasses = '',
        public string $defaultDropdownItemClasses = '',
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
            ['key' => "{$prefix}_dropdown_classes", 'default' => $attrs['default-dropdown-classes'] ?? 'absolute top-full left-0 z-50 mt-7 min-w-48 rounded-b-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900 py-1'],
            ['key' => "{$prefix}_dropdown_item_classes", 'default' => $attrs['default-dropdown-item-classes'] ?? 'block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors'],
            ['key' => "{$prefix}_id", 'default' => '', 'label' => 'Element ID'],
            ['key' => "{$prefix}_attrs", 'default' => '[]', 'label' => 'Custom Attributes'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.nav');
    }
}
