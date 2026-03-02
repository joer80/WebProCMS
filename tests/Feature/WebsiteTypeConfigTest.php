<?php

it('defaults to saas when WEBSITE_TYPE is not set', function (): void {
    expect(config('features.website_type'))->toBe('saas');
});

it('reads the website type from config', function (): void {
    config(['features.website_type' => 'healthcare']);

    expect(config('features.website_type'))->toBe('healthcare');
});

it('loads navigation config for each valid type', function (string $type): void {
    $navConfig = config("navigation.{$type}");

    expect($navConfig)->toBeArray()
        ->toHaveKey('menus')
        ->toHaveKey('show_auth_links')
        ->toHaveKey('footer_slugs');

    expect($navConfig['menus'])->toBeArray()->not->toBeEmpty();

    $mainNav = collect($navConfig['menus'])->firstWhere('slug', 'main-navigation');
    expect($mainNav)->toBeArray()->toHaveKey('items');
    expect($mainNav['items'])->toBeArray()->not->toBeEmpty();
})->with(['saas', 'service', 'ecommerce', 'law', 'nonprofit', 'healthcare', 'custom']);

it('has route defined for every nav item in every type', function (string $type): void {
    $navConfig = config("navigation.{$type}");

    foreach ($navConfig['menus'] as $menu) {
        foreach ($menu['items'] as $item) {
            if (isset($item['route']) && ($item['active'] ?? true)) {
                expect(route($item['route']))->toBeString()->not->toBeEmpty();
            }
        }
    }
})->with(['saas', 'service', 'ecommerce', 'law', 'nonprofit', 'healthcare', 'custom']);

it('enables auth links for saas type', function (): void {
    expect(config('navigation.saas.show_auth_links'))->toBeTrue();
});

it('enables auth links for ecommerce type', function (): void {
    expect(config('navigation.ecommerce.show_auth_links'))->toBeTrue();
});

it('disables auth links for service type', function (): void {
    expect(config('navigation.service.show_auth_links'))->toBeFalse();
});

it('disables auth links for law type', function (): void {
    expect(config('navigation.law.show_auth_links'))->toBeFalse();
});

it('disables auth links for nonprofit type', function (): void {
    expect(config('navigation.nonprofit.show_auth_links'))->toBeFalse();
});

it('disables auth links for healthcare type', function (): void {
    expect(config('navigation.healthcare.show_auth_links'))->toBeFalse();
});
