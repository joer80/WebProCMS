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
        public ?string $defaultObjectFit = null,
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

        $fields = [];

        if (! empty($attrs['note'])) {
            $fields[] = ['key' => "{$prefix}_note", 'type' => 'note', 'message' => $attrs['note'], 'default' => '', 'label' => 'Info'];
        }

        $fields[] = ['key' => "{$prefix}_classes", 'default' => $attrs['default-classes'] ?? ''];

        if (array_key_exists('default-featured-classes', $attrs)) {
            $fields[] = ['key' => "{$prefix}_featured_classes", 'default' => $attrs['default-featured-classes']];
        }

        if (array_key_exists('default-object-fit', $attrs)) {
            $fields[] = ['key' => "{$prefix}_object_fit", 'default' => $attrs['default-object-fit'] ?? 'cover'];
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
