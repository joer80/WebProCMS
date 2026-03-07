<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Link extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'link',
        public string $defaultLabel = 'View all →',
        public string $defaultUrl = '#',
        public string $defaultClasses = 'text-primary font-semibold hover:text-primary/80 transition-colors text-sm',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'link';

        $fields = [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => $prefix, 'default' => $attrs['default-label'] ?? 'View all →'],
            ['key' => "{$prefix}_url", 'default' => $attrs['default-url'] ?? '#'],
            ['key' => "{$prefix}_new_tab", 'default' => ''],
            ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? 'text-primary font-semibold hover:text-primary/80 transition-colors text-sm'],
            ['key' => "{$prefix}_id", 'default' => '', 'label' => 'Element ID'],
            ['key' => "{$prefix}_attrs", 'default' => '[]', 'label' => 'Custom Attributes'],
        ];

        if (isset($attrs['label-toggle'])) {
            $fields[0]['label'] = $attrs['label-toggle'];
        }

        if (isset($attrs['label-text'])) {
            $fields[1]['label'] = $attrs['label-text'];
        }

        if (isset($attrs['label-url'])) {
            $fields[2]['label'] = $attrs['label-url'];
        }

        return $fields;
    }

    public function render(): View
    {
        return view('components.dl.link');
    }
}
