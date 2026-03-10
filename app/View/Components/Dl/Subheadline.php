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
        public bool $noToggle = false,
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

        $fields = [];
        if (! ($attrs['no-toggle'] ?? false)) {
            $fields[] = ['key' => "toggle_{$prefix}", 'default' => '1'];
        }
        $fields = array_merge($fields, [
            ['key' => $prefix, 'default' => $attrs['default'] ?? '', 'type' => 'richtext'],
            ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? 'mt-4 text-lg text-zinc-500 dark:text-zinc-400'],
            ['key' => "{$prefix}_animation", 'default' => '', 'label' => 'Animation'],
            ['key' => "{$prefix}_animation_delay", 'default' => '', 'label' => 'Animation Delay'],
            ['key' => "{$prefix}_id", 'default' => '', 'label' => 'Element ID'],
            ['key' => "{$prefix}_attrs", 'default' => '[]', 'label' => 'Custom Attributes'],
        ]);

        return $fields;
    }

    public function render(): View
    {
        return view('components.dl.subheadline');
    }
}
