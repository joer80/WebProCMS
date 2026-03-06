<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Badge extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'badge',
        public string $defaultLabel = 'Now in Beta',
        public string $defaultClasses = 'inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'badge';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => $prefix, 'default' => $attrs['default-label'] ?? 'Now in Beta'],
            ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? 'inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full'],
            ['key' => "{$prefix}_id", 'default' => '', 'label' => 'Element ID'],
            ['key' => "{$prefix}_attrs", 'default' => '[]', 'label' => 'Custom Attributes'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.badge');
    }
}
