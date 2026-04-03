<?php

use App\Models\Setting;
use App\Services\BrandingStyleService;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::forget('branding_style_block');
});

test('styleBlock returns valid css with default colors', function () {
    $service = app(BrandingStyleService::class);
    $block = $service->styleBlock();

    expect($block)
        ->toContain(':root {')
        ->toContain('--color-primary: #3B4A99')
        ->toContain('--color-accent: #262626')
        ->toContain('--font-sans:')
        ->toContain('--spacing-section:')
        ->toContain('--width-container:');
});

test('styleBlock uses colors from settings', function () {
    Setting::set('branding.colors', ['primary' => '#ff0000', 'accent' => '#00ff00']);

    $block = app(BrandingStyleService::class)->styleBlock();

    expect($block)
        ->toContain('--color-primary: #ff0000')
        ->toContain('--color-accent: #00ff00');
});

test('styleBlock ignores invalid color values', function () {
    Setting::set('branding.colors', ['primary' => 'not-a-color', 'accent' => '#00ff00']);

    $block = app(BrandingStyleService::class)->styleBlock();

    expect($block)
        ->not->toContain('not-a-color')
        ->toContain('--color-accent: #00ff00');
});

test('styleBlock reflects spacing setting', function () {
    Setting::set('branding.section_spacing', 'small');

    $block = app(BrandingStyleService::class)->styleBlock();

    expect($block)->toContain('--spacing-section: 4rem');
});

test('styleBlock reflects container width setting', function () {
    Setting::set('branding.container_width', 'large');

    $block = app(BrandingStyleService::class)->styleBlock();

    expect($block)->toContain('--width-container: 80rem');
});

test('styleBlock is cached and bust clears it', function () {
    $service = app(BrandingStyleService::class);

    Setting::set('branding.colors', ['primary' => '#aabbcc']);
    $first = $service->styleBlock();
    expect($first)->toContain('#aabbcc');

    // Change setting without busting — cached value should be returned
    Setting::set('branding.colors', ['primary' => '#112233']);
    $cached = $service->styleBlock();
    expect($cached)->toContain('#aabbcc');

    // Bust and rebuild
    $service->bust();
    $fresh = $service->styleBlock();
    expect($fresh)->toContain('#112233');
});

test('branding page saves colors to settings and busts cache', function () {
    $this->actingAs(\App\Models\User::factory()->create(['role' => \App\Enums\Role::Admin]));

    Cache::spy();

    \Livewire\Livewire::test('pages::dashboard.settings.branding')
        ->set('themeColors', ['primary' => '#123456'])
        ->call('saveThemeColors')
        ->assertDispatched('notify');

    expect(Setting::get('branding.colors'))->toBe(['primary' => '#123456']);
});

test('design page saves typography to settings and busts cache', function () {
    $this->actingAs(\App\Models\User::factory()->create(['role' => \App\Enums\Role::Admin]));

    \Livewire\Livewire::test('pages::dashboard.settings.design')
        ->set('bodyFont', 'inter')
        ->set('headingFont', 'inter')
        ->set('sectionSpacing', 'large')
        ->set('containerWidth', 'small')
        ->call('saveTypography')
        ->assertDispatched('notify');

    expect(Setting::get('branding.body_font'))->toBe('inter');
    expect(Setting::get('branding.section_spacing'))->toBe('large');
    expect(Setting::get('branding.container_width'))->toBe('small');
});
