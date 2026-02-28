<?php

use App\Enums\Role;
use App\Models\Location;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

// ── Business Info ──────────────────────────────────────────────────────────────

it('loads business info from config on mount', function (): void {
    config([
        'business.url' => 'https://example.com',
        'business.phone' => '+1 (512) 555-0100',
        'business.email' => 'sales@example.com',
        'business.address_street' => '100 Congress Ave',
        'business.address_city_state_zip' => 'Austin, TX 78701',
        'business.hours' => 'Mon–Fri, 9am–5pm',
    ]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->assertSet('businessUrl', 'https://example.com')
        ->assertSet('businessPhone', '+1 (512) 555-0100')
        ->assertSet('businessEmail', 'sales@example.com')
        ->assertSet('businessAddressStreet', '100 Congress Ave')
        ->assertSet('businessAddressCityStateZip', 'Austin, TX 78701')
        ->assertSet('businessHours', 'Mon–Fri, 9am–5pm');
});

it('writes config/business.php and dispatches a notification when saving business info', function (): void {
    $path = config_path('business.php');
    $original = file_get_contents($path);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->set('businessUrl', 'https://test.com')
        ->set('businessPhone', '+1 (555) 000-0001')
        ->set('businessEmail', 'info@test.com')
        ->set('businessAddressStreet', '1 Main St')
        ->set('businessAddressCityStateZip', 'Dallas, TX 75201')
        ->set('businessHours', 'Mon–Fri, 8am–6pm')
        ->call('saveBusinessInfo')
        ->assertHasNoErrors()
        ->assertDispatched('notify', message: 'Business info saved.');

    $written = file_get_contents($path);
    expect($written)
        ->toContain("'url' => 'https://test.com'")
        ->toContain("'phone' => '+1 (555) 000-0001'")
        ->toContain("'email' => 'info@test.com'")
        ->toContain("'address_street' => '1 Main St'")
        ->toContain("'address_city_state_zip' => 'Dallas, TX 75201'")
        ->toContain("'hours' => 'Mon–Fri, 8am–6pm'");

    file_put_contents($path, $original);
});

it('keeps admin_email as an env() call in the written business config', function (): void {
    $path = config_path('business.php');
    $original = file_get_contents($path);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->call('saveBusinessInfo');

    expect(file_get_contents($path))->toContain("env('BUSINESS_ADMIN_EMAIL'");

    file_put_contents($path, $original);
});

it('validates business email format', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->set('businessEmail', 'not-an-email')
        ->call('saveBusinessInfo')
        ->assertHasErrors(['businessEmail']);
});

// ── SEO Settings ───────────────────────────────────────────────────────────────

it('loads SEO settings from config on mount', function (): void {
    config([
        'seo.schema.type' => 'LocalBusiness',
        'seo.schema.logo' => 'https://example.com/logo.png',
        'seo.schema.description' => 'We build things.',
        'seo.schema.address.city' => 'Austin',
        'seo.schema.address.region' => 'TX',
        'seo.schema.address.postal_code' => '78701',
        'seo.schema.address.country' => 'US',
        'seo.og.default_image' => 'https://example.com/og.jpg',
        'seo.twitter.handle' => '@example',
    ]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->assertSet('seoSchemaType', 'LocalBusiness')
        ->assertSet('seoSchemaLogo', 'https://example.com/logo.png')
        ->assertSet('seoSchemaDescription', 'We build things.')
        ->assertSet('seoAddressCity', 'Austin')
        ->assertSet('seoAddressRegion', 'TX')
        ->assertSet('seoAddressPostalCode', '78701')
        ->assertSet('seoAddressCountry', 'US')
        ->assertSet('seoOgDefaultImage', 'https://example.com/og.jpg')
        ->assertSet('seoTwitterHandle', '@example');
});

it('writes config/seo.php and dispatches a notification when saving SEO settings', function (): void {
    $path = config_path('seo.php');
    $original = file_get_contents($path);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->set('seoSchemaType', 'LocalBusiness')
        ->set('seoSchemaDescription', 'A local biz.')
        ->set('seoAddressCity', 'Houston')
        ->set('seoAddressRegion', 'TX')
        ->set('seoAddressPostalCode', '77001')
        ->set('seoAddressCountry', 'US')
        ->call('saveSeoSettings')
        ->assertHasNoErrors()
        ->assertDispatched('notify', message: 'SEO settings saved.');

    $written = file_get_contents($path);
    expect($written)
        ->toContain("'type' => 'LocalBusiness'")
        ->toContain("'description' => 'A local biz.'")
        ->toContain("'city' => 'Houston'")
        ->toContain("'region' => 'TX'")
        ->toContain("'postal_code' => '77001'");

    file_put_contents($path, $original);
});

it('keeps phone and email as config() references in the written seo config', function (): void {
    $path = config_path('seo.php');
    $original = file_get_contents($path);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->call('saveSeoSettings');

    $written = file_get_contents($path);
    expect($written)
        ->toContain("config('business.phone'")
        ->toContain("config('business.email'");

    file_put_contents($path, $original);
});

it('validates seo schema type is an allowed value', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->set('seoSchemaType', 'InvalidType')
        ->call('saveSeoSettings')
        ->assertHasErrors(['seoSchemaType']);
});

// ── General ────────────────────────────────────────────────────────────────────

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
    $artisan = Artisan::spy();
    $envPath = base_path('.env');
    $originalEnv = file_get_contents($envPath);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->set('sessionDriver', 'file')
        ->call('saveSessionDriver')
        ->assertDispatched('notify', message: 'Settings saved.');

    expect(file_get_contents($envPath))->toContain('SESSION_DRIVER=file');

    $artisan->shouldNotHaveReceived('call');

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
    $artisan = Artisan::spy();
    $envPath = base_path('.env');
    $originalEnv = file_get_contents($envPath);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings')
        ->set('cacheStore', 'file')
        ->call('saveCacheStore')
        ->assertDispatched('notify', message: 'Settings saved.');

    expect(file_get_contents($envPath))->toContain('CACHE_STORE=file');

    $artisan->shouldNotHaveReceived('call');

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
    $artisan = Artisan::spy();
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

    $artisan->shouldNotHaveReceived('call');

    file_put_contents($envPath, $originalEnv);
});
