<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Accordion extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'faqs',
        public string $defaultItems = '[]',
        public string $defaultWrapperClasses = 'divide-y divide-zinc-200 dark:divide-zinc-700',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'faqs';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => "grid_{$prefix}", 'default' => $attrs['default-items'] ?? '[]'],
            ['key' => "{$prefix}_wrapper_classes", 'default' => $attrs['default-wrapper-classes'] ?? 'divide-y divide-zinc-200 dark:divide-zinc-700'],
            ['key' => "{$prefix}_id", 'default' => '', 'label' => 'Element ID'],
            ['key' => "{$prefix}_attrs", 'default' => '[]', 'label' => 'Custom Attributes'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.accordion');
    }
}
