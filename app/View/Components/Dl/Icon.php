<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Icon extends Component
{
    public function __construct(
        public string $slug,
        public string $prefix = 'icon',
        public string $name = 'bolt',
        public string $defaultWrapperClasses = '',
        public string $defaultClasses = 'size-8',
        public ?string $defaultFeaturedClasses = null,
        public bool $featured = false,
    ) {}

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'icon';

        $fields = [];

        if (isset($attrs['default-wrapper-classes']) && $attrs['default-wrapper-classes'] !== '') {
            $fields[] = ['key' => "{$prefix}_wrapper_classes", 'default' => $attrs['default-wrapper-classes']];
        }

        $fields[] = ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? 'size-8'];

        if (array_key_exists('default-featured-classes', $attrs)) {
            $fields[] = ['key' => "{$prefix}_featured_classes", 'default' => $attrs['default-featured-classes']];
        }

        return $fields;
    }

    public function render(): View
    {
        return view('components.dl.icon');
    }
}
