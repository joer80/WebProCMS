<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Section extends Component
{
    public function __construct(
        public string $slug,
        public string $tag = 'section',
        public string $defaultSectionClasses = 'py-section px-6 bg-white dark:bg-zinc-900',
        public string $defaultContainerClasses = 'max-w-6xl mx-auto',
        public ?string $defaultSticky = null,
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $fields = [
            ['key' => 'section_classes', 'default' => $attrs['default-section-classes'] ?? 'py-section px-6 bg-white dark:bg-zinc-900'],
            ['key' => 'section_container_classes', 'default' => $attrs['default-container-classes'] ?? 'max-w-6xl mx-auto'],
            ['key' => 'section_id', 'default' => '', 'label' => 'Section ID'],
            ['key' => 'section_attrs', 'default' => '[]', 'label' => 'Custom Attributes'],
        ];

        if (isset($attrs['default-sticky'])) {
            array_unshift($fields, ['key' => 'toggle_sticky', 'default' => $attrs['default-sticky'] ? '1' : '', 'label' => 'Sticky Header']);
        }

        return $fields;
    }

    public function render(): View
    {
        return view('components.dl.section');
    }
}
