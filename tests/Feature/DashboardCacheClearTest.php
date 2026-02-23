<?php

use App\Models\User;
use Livewire\Livewire;
use Spatie\ResponseCache\Facades\ResponseCache;

it('redirects unauthenticated users from the tools page', function (): void {
    $this->get(route('dashboard.tools'))->assertRedirect(route('login'));
});

it('shows the tools page to authenticated users', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.tools'))
        ->assertOk()
        ->assertSeeText('Public Cache');
});

it('clears the response cache and dispatches a notification', function (): void {
    ResponseCache::shouldReceive('clear')->once();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.tools')
        ->call('clearCache')
        ->assertDispatched('notify', message: 'Public cache cleared.');
});
