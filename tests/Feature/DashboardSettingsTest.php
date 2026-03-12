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
    Setting::set('business.url', 'https://example.com');
    Setting::set('business.phone', '+1 (512) 555-0100');
    Setting::set('business.email', 'sales@example.com');
    Setting::set('business.address_street', '100 Congress Ave');
    Setting::set('business.address_city_state_zip', 'Austin, TX 78701');
    Setting::set('business.hours', 'Mon–Fri, 9am–5pm');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.general')
        ->assertSet('businessUrl', 'https://example.com')
        ->assertSet('businessPhone', '+1 (512) 555-0100')
        ->assertSet('businessEmail', 'sales@example.com')
        ->assertSet('businessAddressStreet', '100 Congress Ave')
        ->assertSet('businessAddressCityStateZip', 'Austin, TX 78701')
        ->assertSet('businessHours', 'Mon–Fri, 9am–5pm');
});

it('saves business info to settings and dispatches a notification', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.general')
        ->set('businessUrl', 'https://test.com')
        ->set('businessPhone', '+1 (555) 000-0001')
        ->set('businessEmail', 'info@test.com')
        ->set('businessAddressStreet', '1 Main St')
        ->set('businessAddressCityStateZip', 'Dallas, TX 75201')
        ->set('businessHours', 'Mon–Fri, 8am–6pm')
        ->call('saveBusinessInfo')
        ->assertHasNoErrors()
        ->assertDispatched('notify', message: 'Business info saved.');

    expect(Setting::get('business.url'))->toBe('https://test.com');
    expect(Setting::get('business.phone'))->toBe('+1 (555) 000-0001');
    expect(Setting::get('business.email'))->toBe('info@test.com');
    expect(Setting::get('business.address_street'))->toBe('1 Main St');
    expect(Setting::get('business.address_city_state_zip'))->toBe('Dallas, TX 75201');
    expect(Setting::get('business.hours'))->toBe('Mon–Fri, 8am–6pm');
});

it('validates business email format', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.general')
        ->set('businessEmail', 'not-an-email')
        ->call('saveBusinessInfo')
        ->assertHasErrors(['businessEmail']);
});

// ── SEO Settings ───────────────────────────────────────────────────────────────

it('loads SEO settings from config on mount', function (): void {
    Setting::set('seo.schema', [
        'type' => 'LocalBusiness',
        'logo' => 'https://example.com/logo.png',
        'description' => 'We build things.',
        'address' => [
            'city' => 'Austin',
            'region' => 'TX',
            'postal_code' => '78701',
            'country' => 'US',
        ],
    ]);
    Setting::set('seo.og.default_image', 'https://example.com/og.jpg');
    Setting::set('seo.twitter.handle', '@example');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.general')
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

it('saves SEO settings to settings and dispatches a notification', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.general')
        ->set('seoSchemaType', 'LocalBusiness')
        ->set('seoSchemaDescription', 'A local biz.')
        ->set('seoAddressCity', 'Houston')
        ->set('seoAddressRegion', 'TX')
        ->set('seoAddressPostalCode', '77001')
        ->set('seoAddressCountry', 'US')
        ->call('saveSeoSettings')
        ->assertHasNoErrors()
        ->assertDispatched('notify', message: 'SEO settings saved.');

    $schema = Setting::get('seo.schema');
    expect($schema['type'])->toBe('LocalBusiness');
    expect($schema['description'])->toBe('A local biz.');
    expect($schema['address']['city'])->toBe('Houston');
    expect($schema['address']['region'])->toBe('TX');
    expect($schema['address']['postal_code'])->toBe('77001');
});

it('validates seo schema type is an allowed value', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.general')
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
        ->get(route('dashboard.settings.general'))
        ->assertOk()
        ->assertSeeText('Business Information');
});

it('seeds 5 locations', function (): void {
    (new \App\Jobs\SeedDemoDataJob)->handle();

    expect(Location::count())->toBe(5);
});

it('does not create duplicate posts when seeded twice', function (): void {
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
        ->test('pages::dashboard.settings.advanced')
        ->assertSet('sessionDriver', 'redis');
});

it('writes SESSION_DRIVER to .env and dispatches a notification', function (): void {
    $artisan = Artisan::spy();
    $envPath = base_path('.env');
    $originalEnv = file_get_contents($envPath);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.advanced')
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
        ->test('pages::dashboard.settings.advanced')
        ->assertSet('cacheStore', 'redis');
});

it('writes CACHE_STORE to .env and dispatches a notification', function (): void {
    $artisan = Artisan::spy();
    $envPath = base_path('.env');
    $originalEnv = file_get_contents($envPath);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.advanced')
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
        ->test('pages::dashboard.settings.advanced')
        ->assertSet('fullPageCacheDriver', 'redis');
});

it('defaults full page cache driver to file when env is not set', function (): void {
    config(['responsecache.cache_store' => 'file']);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.advanced')
        ->assertSet('fullPageCacheDriver', 'file');
});

it('loads the full page cache lifetime from config on mount', function (): void {
    config(['responsecache.cache_lifetime_in_seconds' => 7200]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.advanced')
        ->assertSet('fullPageCacheLifetime', 7200);
});

// ── Design — Typography ─────────────────────────────────────────────────────────

it('loads font choices from branding config on mount', function (): void {
    Setting::set('branding.body_font', 'inter');
    Setting::set('branding.heading_font', 'system');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.design')
        ->assertSet('bodyFont', 'inter')
        ->assertSet('headingFont', 'system')
        ->assertSet('sectionSpacing', 'medium');
});

it('saves typography to css files and settings', function (): void {
    app()->detectEnvironment(fn () => 'local');

    $appCssPath = resource_path('css/app.css');
    $publicCssPath = resource_path('css/public.css');
    $editorCssPath = resource_path('css/editor.css');

    $originalApp = file_get_contents($appCssPath);
    $originalPublic = file_get_contents($publicCssPath);
    $originalEditor = file_get_contents($editorCssPath);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.design')
        ->set('bodyFont', 'inter')
        ->set('headingFont', 'instrument-sans')
        ->set('sectionSpacing', 'large')
        ->call('saveTypography')
        ->assertHasNoErrors()
        ->assertDispatched('notify', message: 'Typography saved. Rebuild assets to apply.');

    expect(file_get_contents($appCssPath))
        ->toContain("--font-sans: 'Inter',")
        ->toContain("--font-heading: 'Instrument Sans',")
        ->toContain('--spacing-section: 6rem')
        ->toContain('--spacing-section-banner: 5rem')
        ->toContain('--spacing-section-hero: 7rem');

    expect(file_get_contents($publicCssPath))->toContain("--font-sans: 'Inter',");
    expect(file_get_contents($editorCssPath))->toContain("--font-sans: 'Inter',");
    expect(Setting::get('branding.body_font'))->toBe('inter');

    file_put_contents($appCssPath, $originalApp);
    file_put_contents($publicCssPath, $originalPublic);
    file_put_contents($editorCssPath, $originalEditor);
});

it('saves system font choice without a named font in the css stack', function (): void {
    app()->detectEnvironment(fn () => 'local');

    $appCssPath = resource_path('css/app.css');
    $originalApp = file_get_contents($appCssPath);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.design')
        ->set('bodyFont', 'system')
        ->set('headingFont', 'system')
        ->set('sectionSpacing', 'medium')
        ->call('saveTypography')
        ->assertHasNoErrors();

    expect(file_get_contents($appCssPath))->toContain('--font-sans: ui-sans-serif,');

    file_put_contents($appCssPath, $originalApp);
});

it('validates that body font is one of the allowed choices', function (): void {
    app()->detectEnvironment(fn () => 'local');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.design')
        ->set('bodyFont', 'comic-sans')
        ->call('saveTypography')
        ->assertHasErrors(['bodyFont']);
});

it('validates that section spacing uses a valid size keyword', function (): void {
    app()->detectEnvironment(fn () => 'local');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.design')
        ->set('sectionSpacing', 'invalid')
        ->call('saveTypography')
        ->assertHasErrors(['sectionSpacing']);
});

// ── Design — Alt Rows ───────────────────────────────────────────────────────────

it('loads alt_rows_start from branding config on mount', function (): void {
    Setting::set('branding.alt_rows_enabled', '1');
    Setting::set('branding.alt_rows_start', 'odd');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.design')
        ->assertSet('altRowsEnabled', true)
        ->assertSet('altRowsStart', 'odd');
});

it('saves alt_rows_start to settings', function (): void {
    Setting::set('branding.alt_rows_enabled', '1');
    Setting::set('branding.alt_rows_start', 'even');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.design')
        ->set('altRowsStart', 'odd')
        ->call('saveAltRows')
        ->assertDispatched('notify', message: 'Alt row setting saved.');

    expect(Setting::get('branding.alt_rows_start'))->toBe('odd');
});

it('rejects invalid alt_rows_start values', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.design')
        ->set('altRowsStart', 'invalid')
        ->call('saveAltRows')
        ->assertHasErrors(['altRowsStart']);
});

// ── Advanced ────────────────────────────────────────────────────────────────────

it('writes RESPONSE_CACHE_DRIVER and RESPONSE_CACHE_LIFETIME to .env and dispatches a notification', function (): void {
    $artisan = Artisan::spy();
    $envPath = base_path('.env');
    $originalEnv = file_get_contents($envPath);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.settings.advanced')
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
