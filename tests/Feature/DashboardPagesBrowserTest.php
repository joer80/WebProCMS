<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Livewire;

it('shows the pages stat card on the dashboard', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    Livewire::withoutLazyLoading()
        ->actingAs($user)
        ->test('pages::dashboard')
        ->assertSeeText('Pages');
});

it('shows the new page button for managers', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    Livewire::withoutLazyLoading()
        ->actingAs($user)
        ->test('pages::dashboard')
        ->assertSeeText('New Page');
});

it('does not show the new page button for standard users', function (): void {
    $user = User::factory()->withRole(Role::Standard)->create();

    Livewire::withoutLazyLoading()
        ->actingAs($user)
        ->test('pages::dashboard')
        ->assertDontSeeText('New Page');
});

it('validates that page name is required', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->set('newPageName', '')
        ->set('newPageSlug', '')
        ->call('createPage')
        ->assertHasErrors(['newPageName', 'newPageSlug']);
});

it('validates that slug must be lowercase alphanumeric with hyphens', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->set('newPageName', 'Test Page')
        ->set('newPageSlug', 'Invalid Slug!')
        ->call('createPage')
        ->assertHasErrors(['newPageSlug']);
});

it('auto-derives slug from page name', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->set('newPageName', 'Our Team')
        ->assertSet('newPageSlug', 'our-team');
});

it('rejects a duplicate slug', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();
    $slug = 'home';

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->set('newPageName', 'Home')
        ->set('newPageSlug', $slug)
        ->call('createPage')
        ->assertHasErrors(['newPageSlug']);
});
