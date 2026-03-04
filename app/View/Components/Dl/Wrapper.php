<?php

namespace App\View\Components\Dl;

use Illuminate\View\Component;
use Illuminate\View\View;

class Wrapper extends Component
{
    /** @var list<string> */
    private const VOID_ELEMENTS = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'];

    public bool $isVoid;

    public function __construct(
        public string $slug,
        public string $prefix = 'wrapper',
        public string $tag = 'div',
        public string $defaultClasses = '',
        public ?string $defaultFeaturedClasses = null,
        public bool $featured = false,
    ) {
        $this->isVoid = in_array(strtolower($this->tag), self::VOID_ELEMENTS);
    }

    /**
     * Schema fields contributed by this component to the design library row.
     *
     * @param  array<string, string>  $attrs
     * @return list<array{key: string, default: string}>
     */
    public static function schemaFields(array $attrs): array
    {
        $prefix = $attrs['prefix'] ?? 'wrapper';

        $fields = [
            ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? ''],
        ];

        if (array_key_exists('default-featured-classes', $attrs)) {
            $fields[] = ['key' => "{$prefix}_featured_classes", 'default' => $attrs['default-featured-classes']];
        }

        $fields[] = ['key' => "{$prefix}_id", 'default' => '', 'label' => 'Element ID'];
        $fields[] = ['key' => "{$prefix}_attrs", 'default' => '[]', 'label' => 'Custom Attributes'];

        return $fields;
    }

    public function render(): View
    {
        return view('components.dl.wrapper');
    }
}
