<?php

use App\Enums\PageCategory;
use App\Enums\Role;
use App\Enums\RowCategory;
use App\Models\DesignPage;
use App\Models\DesignRow;
use App\Models\User;
use Livewire\Livewire;

it('redirects unauthenticated users from the design library', function (): void {
    $this->get(route('dashboard.design-library.index'))->assertRedirect(route('login'));
});

it('redirects standard users from the design library', function (): void {
    $user = User::factory()->withRole(Role::Standard)->create();

    $this->actingAs($user)
        ->get(route('dashboard.design-library.index'))
        ->assertRedirect(route('dashboard'));
});

it('allows manager users to view the design library', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.design-library.index'))
        ->assertOk();
});

it('shows existing design rows', function (): void {
    $user = User::factory()->create();
    DesignRow::factory()->create(['name' => 'Hero Section']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->assertSeeText('Hero Section');
});

it('can create a new design row', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->call('openCreateModal')
        ->set('formName', 'My New Row')
        ->set('formCategory', RowCategory::Hero->value)
        ->set('formBladeCode', '<section>Hello</section>')
        ->call('save')
        ->assertHasNoErrors();

    expect(DesignRow::where('name', 'My New Row')->exists())->toBeTrue();
});

it('validates required fields when creating a row', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->call('openCreateModal')
        ->call('save')
        ->assertHasErrors(['formName', 'formCategory', 'formBladeCode']);
});

it('can edit an existing design row', function (): void {
    $user = User::factory()->create();
    $row = DesignRow::factory()->create(['name' => 'Original Name']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->call('openEditModal', $row->id)
        ->assertSet('formName', 'Original Name')
        ->set('formName', 'Updated Name')
        ->call('save');

    expect($row->fresh()->name)->toBe('Updated Name');
});

it('can delete a design row', function (): void {
    $user = User::factory()->create();
    $row = DesignRow::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->call('deleteItem', $row->id);

    expect(DesignRow::find($row->id))->toBeNull();
});

it('can switch to the pages tab', function (): void {
    $user = User::factory()->create();
    DesignPage::factory()->create(['name' => 'SaaS Landing Page']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->call('setTab', 'pages')
        ->assertSet('tab', 'pages')
        ->assertSeeText('SaaS Landing Page');
});

it('can create a new page template', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->call('setTab', 'pages')
        ->call('openCreateModal')
        ->set('formName', 'My SaaS Page')
        ->set('formCategory', PageCategory::SaaS->value)
        ->set('formBladeCode', '<div>SaaS Page</div>')
        ->call('save')
        ->assertHasNoErrors();

    expect(DesignPage::where('name', 'My SaaS Page')->exists())->toBeTrue();
});

it('can delete a design page template', function (): void {
    $user = User::factory()->create();
    $page = DesignPage::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->call('setTab', 'pages')
        ->call('deleteItem', $page->id);

    expect(DesignPage::find($page->id))->toBeNull();
});

it('filters rows by category', function (): void {
    $user = User::factory()->create();
    DesignRow::factory()->create(['name' => 'Hero Row', 'category' => RowCategory::Hero]);
    DesignRow::factory()->create(['name' => 'Footer Row', 'category' => RowCategory::Footer]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->set('category', RowCategory::Hero->value)
        ->assertSeeText('Hero Row')
        ->assertDontSeeText('Footer Row');
});

it('resets category when switching tabs', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->set('category', RowCategory::Hero->value)
        ->call('setTab', 'pages')
        ->assertSet('category', '');
});

it('syncs the library and indexes template files', function (): void {
    $user = User::factory()->create();

    expect(DesignRow::count())->toBe(0);

    Livewire::actingAs($user)
        ->test('pages::dashboard.design-library.index')
        ->call('syncLibrary');

    expect(DesignRow::count())->toBeGreaterThan(0);
});
