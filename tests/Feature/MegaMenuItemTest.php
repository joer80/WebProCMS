<?php

use App\Models\User;
use Livewire\Livewire;

$sampleColumns = [
    [
        'heading' => 'Platform',
        'links' => [
            ['icon' => 'bolt', 'title' => 'Performance', 'desc' => 'Fast delivery', 'url' => '/performance', 'new_tab' => false],
            ['icon' => 'shield-check', 'title' => 'Security', 'desc' => 'End-to-end encryption', 'url' => '/security', 'new_tab' => false],
        ],
    ],
    [
        'heading' => 'Company',
        'links' => [
            ['icon' => '', 'title' => 'About', 'desc' => '', 'url' => '/about', 'new_tab' => false],
        ],
    ],
];

it('can add a mega menu item', function () use ($sampleColumns): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [],
        ]])
        ->call('openAddModal')
        ->set('addType', 'mega')
        ->set('newPageLabel', 'Products')
        ->set('newMegaColumns', $sampleColumns)
        ->call('addItem')
        ->assertSet('menus.0.items', [
            [
                'label' => 'Products',
                'type' => 'mega',
                'active' => true,
                'columns' => $sampleColumns,
            ],
        ]);
});

it('populates editMegaColumns when opening edit modal for a mega item', function () use ($sampleColumns): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Products', 'type' => 'mega', 'active' => true, 'columns' => $sampleColumns],
            ],
        ]])
        ->call('openEditItemModal', 0)
        ->assertSet('editItemType', 'mega')
        ->assertSet('editMegaColumns', $sampleColumns);
});

it('can edit a mega menu item', function () use ($sampleColumns): void {
    $user = User::factory()->create();

    $updatedColumns = [
        ['heading' => 'Updated', 'links' => [['icon' => 'star', 'title' => 'New Link', 'desc' => '', 'url' => '/new', 'new_tab' => false]]],
    ];

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Products', 'type' => 'mega', 'active' => true, 'columns' => $sampleColumns],
            ],
        ]])
        ->call('openEditItemModal', 0)
        ->set('editItemLabel', 'Solutions')
        ->set('editMegaColumns', $updatedColumns)
        ->call('saveEditItem')
        ->assertSet('menus.0.items.0.label', 'Solutions')
        ->assertSet('menus.0.items.0.type', 'mega')
        ->assertSet('menus.0.items.0.columns', $updatedColumns);
});

it('can remove a mega menu item', function () use ($sampleColumns): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Products', 'type' => 'mega', 'active' => true, 'columns' => $sampleColumns],
            ],
        ]])
        ->call('removeItem', 0)
        ->assertSet('menus.0.items', []);
});

it('requires a label for mega menu items', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [],
        ]])
        ->call('openAddModal')
        ->set('addType', 'mega')
        ->set('newPageLabel', '')
        ->call('addItem')
        ->assertHasErrors(['newPageLabel']);
});

it('resets newMegaColumns when the add modal is re-opened', function () use ($sampleColumns): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [],
        ]])
        ->call('openAddModal')
        ->set('newMegaColumns', $sampleColumns)
        ->call('openAddModal')
        ->assertSet('newMegaColumns', []);
});

it('can add a column to a new mega item', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [],
        ]])
        ->call('addMegaColumnToNew')
        ->assertSet('newMegaColumns.0.heading', '')
        ->assertSet('newMegaColumns.0.links.0.title', '');
});

it('can remove a column from a new mega item', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [],
        ]])
        ->call('addMegaColumnToNew')
        ->call('addMegaColumnToNew')
        ->call('removeMegaColumn', 'new', 0)
        ->assertCount('newMegaColumns', 1);
});

it('can add and remove a link within a mega column', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.menus')
        ->set('menus', [[
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [],
        ]])
        ->call('addMegaColumnToNew')
        ->call('addMegaLink', 'new', 0)
        ->assertCount('newMegaColumns.0.links', 2)
        ->call('removeMegaLink', 'new', 0, 0)
        ->assertCount('newMegaColumns.0.links', 1);
});
