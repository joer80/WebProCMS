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
            ['key' => 'section_animation', 'default' => '', 'label' => 'Entrance Animation'],
            ['key' => 'section_animation_delay', 'default' => '', 'label' => 'Animation Delay'],
            ['key' => 'section_bg_image', 'default' => '', 'label' => 'Background Image'],
            ['key' => 'section_bg_position', 'default' => '', 'label' => 'Background Position'],
            ['key' => 'section_bg_size', 'default' => '', 'label' => 'Background Size'],
            ['key' => 'section_bg_repeat', 'default' => '', 'label' => 'Background Repeat'],
            ['key' => 'section_id', 'default' => '', 'label' => 'Section ID'],
            ['key' => 'section_attrs', 'default' => '[]', 'label' => 'Custom Attributes'],
        ];

        if (isset($attrs['default-sticky'])) {
            array_unshift($fields, ['key' => 'toggle_sticky', 'default' => $attrs['default-sticky'] ? '1' : '', 'label' => 'Sticky Header']);
        }

        return $fields;
    }

    /**
     * @return array<string, string>
     */
    public static function animationPresets(): array
    {
        return [
            'fade-up' => 'animate-in fade-in-0 slide-in-from-bottom-8 duration-700',
            'fade-down' => 'animate-in fade-in-0 slide-in-from-top-8 duration-700',
            'fade-left' => 'animate-in fade-in-0 slide-in-from-right-8 duration-700',
            'fade-right' => 'animate-in fade-in-0 slide-in-from-left-8 duration-700',
            'zoom-in' => 'animate-in zoom-in-95 fade-in-0 duration-700',
            'fade' => 'animate-in fade-in-0 duration-700',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function animationDelays(): array
    {
        return [
            'delay-100' => '100ms',
            'delay-200' => '200ms',
            'delay-300' => '300ms',
            'delay-500' => '500ms',
            'delay-700' => '700ms',
            'delay-1000' => '1000ms',
        ];
    }

    public function render(): View
    {
        return view('components.dl.section');
    }
}
