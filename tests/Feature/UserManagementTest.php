<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Livewire;

it('managers can see users with a role at or below their own', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $manager = User::factory()->withRole(Role::Manager)->create();
    User::factory()->withRole(Role::Standard)->create(['name' => 'Standard User']);

    Livewire::actingAs($manager)
        ->test('pages::dashboard.users')
        ->assertSeeText('Standard User');
});

it('managers cannot see super users', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $manager = User::factory()->withRole(Role::Manager)->create();
    User::factory()->withRole(Role::Super)->create(['name' => 'Super Admin']);

    Livewire::actingAs($manager)
        ->test('pages::dashboard.users')
        ->assertDontSeeText('Super Admin');
});

it('managers can create a new standard user', function (): void {
    $manager = User::factory()->withRole(Role::Manager)->create();

    Livewire::actingAs($manager)
        ->test('pages::dashboard.users')
        ->call('openCreateModal')
        ->set('name', 'New User')
        ->set('email', 'newuser@example.com')
        ->set('password', 'password123')
        ->set('role', 'standard')
        ->call('save')
        ->assertHasNoErrors();

    expect(User::where('email', 'newuser@example.com')->exists())->toBeTrue();
});

it('managers cannot create users with a role higher than their own', function (): void {
    $manager = User::factory()->withRole(Role::Manager)->create();

    Livewire::actingAs($manager)
        ->test('pages::dashboard.users')
        ->call('openCreateModal')
        ->set('name', 'Attempted Super')
        ->set('email', 'superattempt@example.com')
        ->set('password', 'password123')
        ->set('role', 'super')
        ->call('save')
        ->assertHasErrors(['role']);
});

it('managers can delete standard users', function (): void {
    $manager = User::factory()->withRole(Role::Manager)->create();
    $standard = User::factory()->withRole(Role::Standard)->create();

    Livewire::actingAs($manager)
        ->test('pages::dashboard.users')
        ->call('deleteUser', $standard->id);

    expect(User::find($standard->id))->toBeNull();
});

it('users cannot delete themselves', function (): void {
    $manager = User::factory()->withRole(Role::Manager)->create();

    Livewire::actingAs($manager)
        ->test('pages::dashboard.users')
        ->call('deleteUser', $manager->id);

    expect(User::find($manager->id))->not->toBeNull();
});

it('managers cannot delete users with higher roles', function (): void {
    $manager = User::factory()->withRole(Role::Manager)->create();
    $admin = User::factory()->withRole(Role::Admin)->create();

    Livewire::actingAs($manager)
        ->test('pages::dashboard.users')
        ->call('deleteUser', $admin->id);

    expect(User::find($admin->id))->not->toBeNull();
});
