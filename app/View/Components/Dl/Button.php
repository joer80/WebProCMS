<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Button extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'button',
        public string $default = 'Get Started',
        public string $defaultUrl = '#',
        public string $defaultClasses = 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'button';

        return [
            ['key' => "toggle_{$prefix}", 'default' => '1'],
            ['key' => $prefix, 'default' => $attrs['default'] ?? 'Get Started'],
            ['key' => "{$prefix}_url", 'default' => $attrs['default-url'] ?? '#'],
            ['key' => "{$prefix}_new_tab", 'default' => ''],
            ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'],
        ];
    }

    public function render(): View
    {
        return view('components.dl.button');
    }
}
