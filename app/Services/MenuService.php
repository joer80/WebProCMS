<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ContentTypeDefinition;
use App\Models\Location;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Route as RoutesFacade;

class MenuService
{
    /** @var list<string> */
    private const SKIP_ROUTES = [
        'logout', 'login', 'register', 'design-library.preview',
        'blog.show', 'services.content-editor', '404', 'locations.show',
    ];

    /** @var list<string> */
    private const SKIP_PREFIXES = [
        'dashboard.', 'profile.', 'password.', 'two-factor.',
        'verification.', 'sanctum.', 'livewire.',
    ];

    /**
     * All available dynamic sources for the menu editor.
     *
     * @return array<string, string>
     */
    public static function availableSources(): array
    {
        $sources = [
            'pages' => 'Listed Pages',
            'locations' => 'Locations',
            'categories' => 'Blog Categories',
            'content-types' => 'Content Types',
        ];

        return $sources;
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
            case 'pages':
                foreach (static::listedRoutes() as $routeName => $label) {
                    if (Route::has($routeName)) {
                        $children[] = ['label' => $label, 'url' => route($routeName)];
                    }
                }
                break;

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

    /**
     * All public listed routes, keyed by route name with a display label value.
     * Shared by the "pages" dynamic source and the menus editor route picker.
     *
     * @return array<string, string>
     */
    public static function listedRoutes(): array
    {
        return collect(RoutesFacade::getRoutes()->getRoutesByName())
            ->keys()
            ->filter(function (string $name): bool {
                if (in_array($name, static::SKIP_ROUTES, true)) {
                    return false;
                }

                foreach (static::SKIP_PREFIXES as $prefix) {
                    if (str_starts_with($name, $prefix)) {
                        return false;
                    }
                }

                // Exclude all .show routes from the pages source (they are detail/template pages)
                if (str_ends_with($name, '.show')) {
                    return false;
                }

                // Exclude routes with required parameters — they can't be linked to without context.
                $route = RoutesFacade::getRoutes()->getByName($name);
                if ($route) {
                    $requiredParams = array_diff($route->parameterNames(), array_keys($route->defaults));
                    if (! empty($requiredParams)) {
                        return false;
                    }
                }

                return true;
            })
            ->sort()
            ->mapWithKeys(fn (string $name): array => [
                $name => ucwords(str_replace(['.', '-', '_'], ' ', preg_replace('/\.index$/', '', $name))),
            ])
            ->all();
    }
}
