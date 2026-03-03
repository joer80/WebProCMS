<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Buttons extends Component
{
    public function __construct(
        public string $slug,
        public string $defaultWrapperClasses = 'mt-8 flex flex-wrap items-center gap-4',
        public string $defaultPrimaryLabel = 'Get Started',
        public string $defaultPrimaryClasses = 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors',
        public string $defaultSecondaryLabel = 'Learn More',
        public string $defaultSecondaryClasses = 'px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 transition-colors',
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        return [
            ['key' => 'buttons_wrapper_classes', 'default' => $attrs['default-wrapper-classes'] ?? 'mt-8 flex flex-wrap items-center gap-4'],
            ['key' => 'toggle_primary_button', 'default' => '1'],
            ['key' => 'primary_button', 'default' => $attrs['default-primary-label'] ?? 'Get Started'],
            ['key' => 'primary_button_classes', 'default' => $attrs['default-primary-classes'] ?? 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'],
            ['key' => 'primary_button_url', 'default' => '#'],
            ['key' => 'primary_button_new_tab', 'default' => ''],
            ['key' => 'toggle_secondary_button', 'default' => '1'],
            ['key' => 'secondary_button', 'default' => $attrs['default-secondary-label'] ?? 'Learn More'],
            ['key' => 'secondary_button_classes', 'default' => $attrs['default-secondary-classes'] ?? 'px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 transition-colors'],
            ['key' => 'secondary_button_url', 'default' => '#'],
            ['key' => 'secondary_button_new_tab', 'default' => ''],
        ];
    }

    public function render(): View
    {
        return view('components.dl.buttons');
    }
}
