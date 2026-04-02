<?php

use App\Models\Category;
use App\Models\ContentTypeDefinition;
use App\Models\Location;
use App\Models\User;
use App\Services\MenuService;
use Livewire\Livewire;

it('can add a dynamic menu item with source locations', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [],
        ]])
        ->call('openAddModal')
        ->set('addType', 'dynamic')
        ->set('newDynamicSource', 'locations')
        ->set('newPageLabel', 'Locations')
        ->call('addItem')
        ->assertSet('menus.0.items', [
            [
                'label' => 'Locations',
                'type' => 'dynamic',
                'source' => 'locations',
                'active' => true,
            ],
        ]);
});

it('can add a dynamic item with a see all link', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [],
        ]])
        ->call('openAddModal')
        ->set('addType', 'dynamic')
        ->set('newDynamicSource', 'categories')
        ->set('newPageLabel', 'Blog')
        ->set('newDynamicShowAll', true)
        ->set('newDynamicShowAllLabel', 'View All Posts')
        ->call('addItem')
        ->assertSet('menus.0.items', [
            [
                'label' => 'Blog',
                'type' => 'dynamic',
                'source' => 'categories',
                'show_all' => true,
                'show_all_label' => 'View All Posts',
                'active' => true,
            ],
        ]);
});

it('can edit a dynamic menu item', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Locations', 'type' => 'dynamic', 'source' => 'locations', 'active' => true],
            ],
        ]])
        ->call('openEditItemModal', 0)
        ->assertSet('editItemType', 'dynamic')
        ->assertSet('editDynamicSource', 'locations')
        ->set('editItemLabel', 'Our Locations')
        ->set('editDynamicSource', 'categories')
        ->call('saveEditItem')
        ->assertSet('menus.0.items.0.label', 'Our Locations')
        ->assertSet('menus.0.items.0.source', 'categories');
});

it('can remove a dynamic menu item', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Locations', 'type' => 'dynamic', 'source' => 'locations', 'active' => true],
            ],
        ]])
        ->call('removeItem', 0)
        ->assertSet('menus.0.items', []);
});

it('availableSources returns the expected keys', function (): void {
    $sources = MenuService::availableSources();

    expect($sources)
        ->toHaveKey('locations')
        ->toHaveKey('categories')
        ->toHaveKey('content-types')
        ->not->toHaveKey('pages');
});

it('expand resolves location children and see all url', function (): void {
    Location::factory()->create(['name' => 'Seattle Office']);
    Location::factory()->create(['name' => 'Portland Office']);

    $result = MenuService::expand([
        'label' => 'Locations',
        'type' => 'dynamic',
        'source' => 'locations',
        'active' => true,
    ]);

    expect($result['children'])->toHaveCount(2)
        ->and($result['children'][0]['label'])->toBe('Portland Office')
        ->and($result['children'][1]['label'])->toBe('Seattle Office')
        ->and($result['see_all_url'])->toBe(route('locations'));
});

it('expand resolves categories children', function (): void {
    Category::factory()->create(['name' => 'Technology', 'slug' => 'technology']);
    Category::factory()->create(['name' => 'Design', 'slug' => 'design']);

    $result = MenuService::expand([
        'label' => 'Blog',
        'type' => 'dynamic',
        'source' => 'categories',
        'active' => true,
    ]);

    expect($result['children'])->toHaveCount(2)
        ->and($result['children'][0]['label'])->toBe('Design')
        ->and($result['see_all_url'])->toBe(route('blog.index'));
});

it('expand resolves content-types children using index routes', function (): void {
    // minutes.index is registered in routes/web.php; create the definition so allOrdered() returns it
    ContentTypeDefinition::create(['name' => 'Minutes', 'slug' => 'minutes', 'singular' => 'Minute', 'icon' => 'document', 'fields' => []]);

    $result = MenuService::expand([
        'label' => 'Content',
        'type' => 'dynamic',
        'source' => 'content-types',
        'active' => true,
    ]);

    $labels = array_column($result['children'], 'label');
    expect($labels)->toContain('Minutes')
        ->and($result['see_all_url'])->toBeNull();
});

it('expand returns empty children for unknown source', function (): void {
    $result = MenuService::expand([
        'label' => 'Unknown',
        'type' => 'dynamic',
        'source' => 'nonexistent',
        'active' => true,
    ]);

    expect($result['children'])->toBeEmpty()
        ->and($result['see_all_url'])->toBeNull();
});
