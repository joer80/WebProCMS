<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Heading extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'headline',
        public string $default = '',
        public string $defaultTag = 'h2',
        public string $defaultClasses = 'font-heading text-4xl font-bold text-zinc-900 dark:text-white',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'headline';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => "{$prefix}_htag", 'default' => $attrs['default-tag'] ?? 'h2'],
            ['key' => $prefix, 'default' => $attrs['default'] ?? ''],
            ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'],
            ['key' => "{$prefix}_id", 'default' => '', 'label' => 'Heading ID'],
            ['key' => "{$prefix}_attrs", 'default' => '[]', 'label' => 'Custom Attributes'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.heading');
    }
}
