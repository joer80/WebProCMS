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

it('loads nav items from the active website type config on mount', function (): void {
    config([
        'features.website_type' => 'saas',
        'navigation.saas.nav' => [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ],
    ]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->assertSet('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('reorders nav items by drag from index 2 to index 0', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
            ['label' => 'About', 'route' => 'about'],
        ])
        ->call('reorderNavItems', 2, 0)
        ->assertSet('navItems', [
            ['label' => 'About', 'route' => 'about'],
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('does not change nav items when reordering from and to the same index', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ])
        ->call('reorderNavItems', 1, 1)
        ->assertSet('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('reorders footer items by drag', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('footerItems', [
            ['label' => 'Contact', 'route' => 'contact'],
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'Privacy', 'url' => 'https://example.com/privacy'],
        ])
        ->call('reorderFooterItems', 0, 2)
        ->assertSet('footerItems', [
            ['label' => 'Blog', 'route' => 'blog.index'],
            ['label' => 'Privacy', 'url' => 'https://example.com/privacy'],
            ['label' => 'Contact', 'route' => 'contact'],
        ]);
});

it('moves an item up in the list', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ])
        ->call('moveUp', 1)
        ->assertSet('navItems', [
            ['label' => 'Pricing', 'route' => 'pricing'],
            ['label' => 'Features', 'route' => 'features'],
        ]);
});

it('does not move the first item further up', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ])
        ->call('moveUp', 0)
        ->assertSet('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('moves an item down in the list', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ])
        ->call('moveDown', 0)
        ->assertSet('navItems', [
            ['label' => 'Pricing', 'route' => 'pricing'],
            ['label' => 'Features', 'route' => 'features'],
        ]);
});

it('does not move the last item further down', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ])
        ->call('moveDown', 1)
        ->assertSet('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('removes an item from the list', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('navItems', [
            ['label' => 'Features', 'route' => 'features'],
            ['label' => 'Pricing', 'route' => 'pricing'],
        ])
        ->call('removeItem', 0)
        ->assertSet('navItems', [
            ['label' => 'Pricing', 'route' => 'pricing'],
        ]);
});

it('adds a page route item via the modal', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('navItems', [])
        ->set('addType', 'page')
        ->set('newPageRoute', 'about')
        ->set('newPageLabel', 'About Us')
        ->call('addItem')
        ->assertSet('navItems', [
            ['label' => 'About Us', 'route' => 'about'],
        ])
        ->assertSet('showAddModal', false);
});

it('adds a custom url item with new window flag via the modal', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('navItems', [])
        ->set('addType', 'custom')
        ->set('newCustomLabel', 'Our Store')
        ->set('newCustomUrl', 'https://store.example.com')
        ->set('newCustomNewWindow', true)
        ->call('addItem')
        ->assertSet('navItems', [
            ['label' => 'Our Store', 'url' => 'https://store.example.com', 'new_window' => true],
        ])
        ->assertSet('showAddModal', false);
});

it('omits new_window key when false for custom url items', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('navItems', [])
        ->set('addType', 'custom')
        ->set('newCustomLabel', 'Our Store')
        ->set('newCustomUrl', 'https://store.example.com')
        ->set('newCustomNewWindow', false)
        ->call('addItem')
        ->assertSet('navItems', [
            ['label' => 'Our Store', 'url' => 'https://store.example.com'],
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

it('saves nav changes to the config file and redirects for a fresh load', function (): void {
    $configPath = config_path('navigation.php');
    $originalContent = file_get_contents($configPath);

    config([
        'features.website_type' => 'saas',
        'navigation' => require $configPath,
    ]);

    $user = User::factory()->create();

    try {
        Livewire::actingAs($user)
            ->test('pages::dashboard.menus')
            ->set('navItems', [['label' => 'Test Page', 'route' => 'about']])
            ->call('save')
            ->assertRedirect(route('dashboard.menus'));

        expect(config('navigation.saas.nav'))->toBe([
            ['label' => 'Test Page', 'route' => 'about'],
        ]);

        $written = require $configPath;
        expect($written['saas']['nav'])->toBe([
            ['label' => 'Test Page', 'route' => 'about'],
        ]);
    } finally {
        file_put_contents($configPath, $originalContent);
    }
});

it('sets justSaved to true on mount when the session flash is present', function (): void {
    session()->put('menus.saved', true);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->assertSet('justSaved', true);
});
