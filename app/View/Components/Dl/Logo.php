<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Logo extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'logo',
        public string $defaultClasses = 'h-8 w-auto',
        public string $defaultDarkBg = '',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string, label?: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'logo';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => "toggle_{$prefix}_dark_bg", 'default' => $attrs['default-dark-bg'] ?? '', 'label' => 'Use Dark Logo'],
            ['key' => "{$prefix}_image", 'default' => '', 'label' => 'Logo Image', 'fallback_url' => '__branding_logo__'],
            ['key' => "{$prefix}_image_alt", 'default' => '', 'label' => 'Logo Alt Text (default: "'.config('app.name').' Logo")'],
            ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? 'h-8 w-auto'],
            ['key' => "{$prefix}_id", 'default' => '', 'label' => 'Element ID'],
            ['key' => "{$prefix}_attrs", 'default' => '[]', 'label' => 'Custom Attributes'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.logo');
    }
}
