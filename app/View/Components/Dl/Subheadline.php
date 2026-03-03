<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Subheadline extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'subheadline',
        public string $default = '',
        public string $defaultClasses = 'mt-4 text-lg text-zinc-500 dark:text-zinc-400',
        public string $tag = 'p',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'subheadline';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => $prefix, 'default' => $attrs['default'] ?? ''],
            ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? 'mt-4 text-lg text-zinc-500 dark:text-zinc-400'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.subheadline');
    }
}
