<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ContentTypeDefinition;
use App\Models\Location;
use Illuminate\Support\Facades\Route;

class MenuService
{
    /**
     * All available dynamic sources for the menu editor.
     *
     * @return array<string, string>
     */
    public static function availableSources(): array
    {
        return [
            'locations' => 'Locations',
            'categories' => 'Blog Categories',
            'content-types' => 'Content Types',
        ];
    }

    /**
     * Expand a dynamic menu item into resolved child links.
     *
     * @param  array<string, mixed>  $item
     * @return array{children: list<array{label: string, url: string}>, see_all_url: string|null}
     */
    public static function expand(array $item): array
    {
        $source = $item['source'] ?? '';
        $children = [];
        $seeAllUrl = null;

        switch ($source) {
            case 'locations':
                $children = Location::orderBy('name')->get()
                    ->filter(fn (Location $l) => Route::has('locations.show'))
                    ->map(fn (Location $l) => [
                        'label' => $l->name,
                        'url' => route('locations.show', $l->id),
                    ])->values()->all();
                if (Route::has('locations')) {
                    $seeAllUrl = route('locations');
                }
                break;

            case 'categories':
                $children = Category::orderBy('name')->get()
                    ->map(fn (Category $c) => [
                        'label' => $c->name,
                        'url' => Route::has('blog.index')
                            ? route('blog.index', ['category' => $c->slug])
                            : '#',
                    ])->all();
                if (Route::has('blog.index')) {
                    $seeAllUrl = route('blog.index');
                }
                break;

            case 'content-types':
                $children = ContentTypeDefinition::allOrdered()
                    ->filter(fn (ContentTypeDefinition $t) => Route::has("{$t->slug}.index"))
                    ->map(fn (ContentTypeDefinition $t) => [
                        'label' => $t->name,
                        'url' => route("{$t->slug}.index"),
                    ])->values()->all();
                break;
        }

        return [
            'children' => $children,
            'see_all_url' => $seeAllUrl,
        ];
    }
}
