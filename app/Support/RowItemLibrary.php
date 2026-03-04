<?php

namespace App\Support;

class RowItemLibrary
{
    /**
     * Available items that can be added to a row via the content editor.
     *
     * Blade snippets are loaded from resources/design-library/items/{key}.blade.php.
     * Each entry defines display metadata and an optional default prefix used for
     * uniqueness checks when inserting into an existing row.
     *
     * @return array<string, array{name: string, icon: string, prefix?: string, blade: string}>
     */
    public static function items(): array
    {
        $definitions = [
            'heading' => ['name' => 'Heading', 'icon' => 'document-text', 'prefix' => 'headline'],
            'subheadline' => ['name' => 'Subheadline', 'icon' => 'bars-3-bottom-left', 'prefix' => 'subheadline'],
            'buttons' => ['name' => 'Buttons', 'icon' => 'cursor-arrow-rays'],
            'image' => ['name' => 'Image', 'icon' => 'photo', 'prefix' => 'section_image'],
            'video' => ['name' => 'Video', 'icon' => 'film', 'prefix' => 'section_video'],
            'link' => ['name' => 'Link', 'icon' => 'link', 'prefix' => 'section_link'],
            'accordion' => ['name' => 'Accordion', 'icon' => 'chevron-up-down', 'prefix' => 'faqs'],
        ];

        $items = [];

        foreach ($definitions as $key => $meta) {
            $path = resource_path("design-library/items/{$key}.blade.php");
            $items[$key] = array_merge($meta, ['blade' => trim(file_get_contents($path))]);
        }

        return $items;
    }
}
