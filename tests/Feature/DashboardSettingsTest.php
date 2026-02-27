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

it('loads the session driver from config on mount', function (): void {
    config(['session.driver' => 'redis']);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->assertSet('sessionDriver', 'redis');
});

it('writes SESSION_DRIVER to .env and dispatches a notification', function (): void {
    $envPath = base_path('.env');
    $originalEnv = file_get_contents($envPath);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->set('sessionDriver', 'file')
        ->call('saveSessionDriver')
        ->assertDispatched('notify', message: 'Settings saved.');

    expect(file_get_contents($envPath))->toContain('SESSION_DRIVER=file');

    file_put_contents($envPath, $originalEnv);
});

it('loads the cache store from config on mount', function (): void {
    config(['cache.default' => 'redis']);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->assertSet('cacheStore', 'redis');
});

it('writes CACHE_STORE to .env and dispatches a notification', function (): void {
    $envPath = base_path('.env');
    $originalEnv = file_get_contents($envPath);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->set('cacheStore', 'file')
        ->call('saveCacheStore')
        ->assertDispatched('notify', message: 'Settings saved.');

    expect(file_get_contents($envPath))->toContain('CACHE_STORE=file');

    file_put_contents($envPath, $originalEnv);
});

it('loads the full page cache driver from config on mount', function (): void {
    config(['responsecache.cache_store' => 'redis']);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->assertSet('fullPageCacheDriver', 'redis');
});

it('defaults full page cache driver to file when env is not set', function (): void {
    config(['responsecache.cache_store' => 'file']);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->assertSet('fullPageCacheDriver', 'file');
});

it('loads the full page cache lifetime from config on mount', function (): void {
    config(['responsecache.cache_lifetime_in_seconds' => 7200]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->assertSet('fullPageCacheLifetime', 7200);
});

it('writes RESPONSE_CACHE_DRIVER and RESPONSE_CACHE_LIFETIME to .env and dispatches a notification', function (): void {
    $envPath = base_path('.env');
    $originalEnv = file_get_contents($envPath);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->set('fullPageCacheDriver', 'file')
        ->set('fullPageCacheLifetime', 7200)
        ->call('saveFullPageCache')
        ->assertDispatched('notify', message: 'Settings saved.');

    $env = file_get_contents($envPath);
    expect($env)->toContain('RESPONSE_CACHE_DRIVER=file')
        ->and($env)->toContain('RESPONSE_CACHE_LIFETIME=7200');

    file_put_contents($envPath, $originalEnv);
});
