<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Livewire;

it('redirects unauthenticated users from the menus page', function (): void {
    $this->get(route('dashboard.menus'))->assertRedirect(route('login'));
});

it('redirects standard users from the menus page to the dashboard', function (): void {
    $user = User::factory()->withRole(Role::Standard)->create();

    $this->actingAs($user)
        ->get(route('dashboard.menus'))
        ->assertRedirect(route('dashboard'));
});

it('shows the menus page to manager users', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.menus'))
        ->assertOk()
        ->assertSeeText('Menus');
});

it('loads menus from the flat navigation config on mount', function (): void {
    config([
        'navigation.menus' => [
            [
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [
                    ['label' => 'Features', 'url' => '#', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                ],
            ],
        ],
    ]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->assertSet('menus', [
            [
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [
                    ['label' => 'Features', 'url' => '#', 'active' => true],
                    ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
                ],
            ],
        ]);
});

it('reorders items in the active menu by drag from index 2 to index 0', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Features', 'route' => 'features'],
                ['label' => 'Pricing', 'route' => 'pricing'],
                ['label' => 'About', 'route' => 'about'],
            ],
        ]])
        ->call('reorderItems', 2, 0)
        ->assertSet('menus.0.items', [
            ['label' => 'About', 'route' => 'about'],
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('does not change items when reordering from and to the same index', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Features', 'route' => 'features'],
                ['label' => 'Pricing', 'route' => 'pricing'],
            ],
        ]])
        ->call('reorderItems', 1, 1)
        ->assertSet('menus.0.items', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('reorders footer menu column order by drag', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('footerSlugs', ['company', 'legal', 'resources'])
        ->call('reorderFooterSlugs', 0, 2)
        ->assertSet('footerSlugs', ['legal', 'resources', 'company']);
});

it('moves an item up in the list', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Features', 'route' => 'features'],
                ['label' => 'Pricing', 'route' => 'pricing'],
            ],
        ]])
        ->call('moveItemUp', 1)
        ->assertSet('menus.0.items', [
            ['label' => 'Pricing', 'route' => 'pricing'],
            ['label' => 'Features', 'route' => 'features'],
        ]);
});

it('does not move the first item further up', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Features', 'route' => 'features'],
                ['label' => 'Pricing', 'route' => 'pricing'],
            ],
        ]])
        ->call('moveItemUp', 0)
        ->assertSet('menus.0.items', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('moves an item down in the list', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Features', 'route' => 'features'],
                ['label' => 'Pricing', 'route' => 'pricing'],
            ],
        ]])
        ->call('moveItemDown', 0)
        ->assertSet('menus.0.items', [
            ['label' => 'Pricing', 'route' => 'pricing'],
            ['label' => 'Features', 'route' => 'features'],
        ]);
});

it('does not move the last item further down', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Features', 'route' => 'features'],
                ['label' => 'Pricing', 'route' => 'pricing'],
            ],
        ]])
        ->call('moveItemDown', 1)
        ->assertSet('menus.0.items', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('removes an item from the list', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Features', 'route' => 'features'],
                ['label' => 'Pricing', 'route' => 'pricing'],
            ],
        ]])
        ->call('removeItem', 0)
        ->assertSet('menus.0.items', [
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('adds a page route item via the modal', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [['slug' => 'main-navigation', 'label' => 'Main Navigation', 'items' => []]])
        ->set('addType', 'page')
        ->set('newPageRoute', 'about')
        ->set('newPageLabel', 'About Us')
        ->call('addItem')
        ->assertSet('menus.0.items', [
            ['label' => 'About Us', 'route' => 'about', 'active' => true],
        ])
        ->assertSet('showAddModal', false);
});

it('adds a custom url item with new window flag via the modal', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [['slug' => 'main-navigation', 'label' => 'Main Navigation', 'items' => []]])
        ->set('addType', 'custom')
        ->set('newCustomLabel', 'Our Store')
        ->set('newCustomUrl', 'https://store.example.com')
        ->set('newCustomNewWindow', true)
        ->call('addItem')
        ->assertSet('menus.0.items', [
            ['label' => 'Our Store', 'url' => 'https://store.example.com', 'active' => true, 'new_window' => true],
        ])
        ->assertSet('showAddModal', false);
});

it('omits new_window key when false for custom url items', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [['slug' => 'main-navigation', 'label' => 'Main Navigation', 'items' => []]])
        ->set('addType', 'custom')
        ->set('newCustomLabel', 'Our Store')
        ->set('newCustomUrl', 'https://store.example.com')
        ->set('newCustomNewWindow', false)
        ->call('addItem')
        ->assertSet('menus.0.items', [
            ['label' => 'Our Store', 'url' => 'https://store.example.com', 'active' => true],
        ]);
});

it('validates a page route is required when adding a page item', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('addType', 'page')
        ->set('newPageRoute', '')
        ->set('newPageLabel', 'About')
        ->call('addItem')
        ->assertHasErrors(['newPageRoute']);
});

it('validates a label is required when adding a custom url', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('addType', 'custom')
        ->set('newCustomLabel', '')
        ->set('newCustomUrl', 'https://example.com')
        ->call('addItem')
        ->assertHasErrors(['newCustomLabel']);
});

it('validates the url format when adding a custom url', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('addType', 'custom')
        ->set('newCustomLabel', 'My Link')
        ->set('newCustomUrl', 'not-a-url')
        ->call('addItem')
        ->assertHasErrors(['newCustomUrl']);
});

it('auto-populates the page label when a route is selected', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('newPageLabel', '')
        ->set('newPageRoute', 'blog.index')
        ->assertSet('newPageLabel', 'Blog Index');
});

it('does not overwrite a manually set page label when route changes', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('newPageLabel', 'Read Our Blog')
        ->set('newPageRoute', 'blog.index')
        ->assertSet('newPageLabel', 'Read Our Blog');
});

it('saves menus changes to the config file', function (): void {
    $configPath = config_path('navigation.php');
    $originalContent = file_get_contents($configPath);

    config([
        'navigation' => require $configPath,
    ]);

    $user = User::factory()->create();

    try {
        Livewire::actingAs($user)
            ->test('pages::dashboard.menus')
            ->set('menus', [[
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [['label' => 'Test Page', 'route' => 'about', 'active' => true]],
            ]])
            ->call('save');

        $written = require $configPath;
        $mainNav = collect($written['menus'])->firstWhere('slug', 'main-navigation');

        expect($mainNav['items'])->toBe([
            ['label' => 'Test Page', 'route' => 'about', 'active' => true],
        ]);
    } finally {
        file_put_contents($configPath, $originalContent);
    }
});
