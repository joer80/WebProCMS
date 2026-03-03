<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class ContentHeader extends Component
{
    public function __construct(
        public string $slug,
        public string $defaultWrapperClasses = 'text-center mb-12',
        public string $defaultHeading = '',
        public string $defaultHeadingTag = 'h2',
        public string $defaultHeadingClasses = 'font-heading text-4xl font-bold text-zinc-900 dark:text-white',
        public string $defaultSubheadline = '',
        public string $defaultSubheadlineClasses = 'mt-4 text-lg text-zinc-500 dark:text-zinc-400',
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
            ['key' => 'header_wrapper_classes', 'default' => $attrs['default-wrapper-classes'] ?? 'text-center mb-12'],
            ...Heading::schemaFields([
                'prefix' => 'headline',
                'default' => $attrs['default-heading'] ?? '',
                'default-tag' => $attrs['default-heading-tag'] ?? 'h2',
                'default-classes' => $attrs['default-heading-classes'] ?? 'font-heading text-4xl font-bold text-zinc-900 dark:text-white',
            ]),
            ...Subheadline::schemaFields([
                'prefix' => 'subheadline',
                'default' => $attrs['default-subheadline'] ?? '',
                'default-classes' => $attrs['default-subheadline-classes'] ?? 'mt-4 text-lg text-zinc-500 dark:text-zinc-400',
            ]),
        ];
    }

    public function render(): View
    {
        return view('components.dl.content-header');
    }
}
