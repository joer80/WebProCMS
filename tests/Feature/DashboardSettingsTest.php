<?php

use App\Enums\Role;
use App\Models\Location;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Livewire\Livewire;

it('redirects unauthenticated users from the settings page', function (): void {
    $this->get(route('dashboard.settings'))->assertRedirect(route('login'));
});

it('shows the settings page to manager users', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.settings'))
        ->assertOk()
        ->assertSeeText('Locations');
});

it('loads the saved locations_mode setting on mount', function (): void {
    Setting::set('locations_mode', 'multiple');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->assertSet('locationsMode', 'multiple');
});

it('defaults locations mode to single when no setting exists', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->assertSet('locationsMode', 'single');
});

it('saves the locations mode setting and dispatches a notification', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->set('locationsMode', 'multiple')
        ->call('saveLocationsMode')
        ->assertDispatched('notify', message: 'Settings saved.');

    expect(Setting::get('locations_mode'))->toBe('multiple');
});

it('seeds 5 locations when locations_mode is multiple', function (): void {
    Setting::set('locations_mode', 'multiple');

    (new \App\Jobs\SeedDemoDataJob)->handle();

    expect(Location::count())->toBe(5);
});

it('seeds 1 location when locations_mode is single', function (): void {
    Setting::set('locations_mode', 'single');

    (new \App\Jobs\SeedDemoDataJob)->handle();

    expect(Location::count())->toBe(1);
});

it('does not create duplicate posts when seeded twice', function (): void {
    Setting::set('locations_mode', 'single');

    (new \App\Jobs\SeedDemoDataJob)->handle();
    $countAfterFirst = Post::query()->where('is_seeded', true)->count();

    (new \App\Jobs\SeedDemoDataJob)->handle();
    $countAfterSecond = Post::query()->where('is_seeded', true)->count();

    expect($countAfterSecond)->toBe($countAfterFirst);
});
