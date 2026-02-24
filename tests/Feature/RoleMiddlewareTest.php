<?php

use App\Enums\Role;
use App\Models\User;

it('redirects standard users from manager-only routes', function (): void {
    $user = User::factory()->withRole(Role::Standard)->create();

    $this->actingAs($user)
        ->get(route('dashboard.blog.index'))
        ->assertRedirect(route('dashboard'));
});

it('allows manager users through manager-only routes', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.blog.index'))
        ->assertOk();
});

it('allows admin users through manager-only routes', function (): void {
    $user = User::factory()->withRole(Role::Admin)->create();

    $this->actingAs($user)
        ->get(route('dashboard.blog.index'))
        ->assertOk();
});

it('allows super users through manager-only routes', function (): void {
    $user = User::factory()->withRole(Role::Super)->create();

    $this->actingAs($user)
        ->get(route('dashboard.blog.index'))
        ->assertOk();
});

it('redirects standard users from the users management page', function (): void {
    $user = User::factory()->withRole(Role::Standard)->create();

    $this->actingAs($user)
        ->get(route('dashboard.users'))
        ->assertRedirect(route('dashboard'));
});

it('allows managers to access the users management page', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.users'))
        ->assertOk();
});

it('allows all authenticated users to access the main dashboard', function (): void {
    $user = User::factory()->withRole(Role::Standard)->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});
