<?php

use App\Enums\Role;
use App\Models\User;
use Spatie\ResponseCache\Middlewares\CacheResponse;

it('shows edit page button for manager on an editable page', function (): void {
    $manager = User::factory()->create(['role' => Role::Manager]);

    $this->actingAs($manager)
        ->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Edit Page')
        ->assertSee(route('dashboard.design-library.editor'));
});

it('shows edit page button for admin on an editable page', function (): void {
    $admin = User::factory()->create(['role' => Role::Admin]);

    $this->actingAs($admin)
        ->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Edit Page');
});

it('does not show edit page button for standard users', function (): void {
    $standard = User::factory()->create(['role' => Role::Standard]);

    $this->actingAs($standard)
        ->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee('Edit Page');
});

it('does not show edit page button for guests', function (): void {
    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee('Edit Page');
});
