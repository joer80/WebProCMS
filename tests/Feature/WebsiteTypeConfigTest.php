<?php

it('loads the flat navigation config with required keys', function (): void {
    $navConfig = config('navigation');

    expect($navConfig)->toBeArray()
        ->toHaveKey('menus')
        ->toHaveKey('show_auth_links')
        ->toHaveKey('footer_slugs')
        ->toHaveKey('show_account_in_footer');

    expect($navConfig['menus'])->toBeArray()->not->toBeEmpty();
});

it('has a main-navigation menu in the flat config', function (): void {
    $navConfig = config('navigation');
    $mainNav = collect($navConfig['menus'])->firstWhere('slug', 'main-navigation');

    expect($mainNav)->toBeArray()->toHaveKey('items');
    expect($mainNav['items'])->toBeArray()->not->toBeEmpty();
});

it('loads menu templates for each valid website type', function (string $type): void {
    $template = config("menu-templates.{$type}");

    expect($template)->toBeArray()
        ->toHaveKey('menus')
        ->toHaveKey('show_auth_links')
        ->toHaveKey('footer_slugs');

    expect($template['menus'])->toBeArray()->not->toBeEmpty();

    $mainNav = collect($template['menus'])->firstWhere('slug', 'main-navigation');
    expect($mainNav)->toBeArray()->toHaveKey('items');
    expect($mainNav['items'])->toBeArray()->not->toBeEmpty();
})->with(['saas', 'service', 'ecommerce', 'law', 'nonprofit', 'healthcare', 'custom']);

it('enables auth links in the saas and ecommerce templates', function (string $type): void {
    expect(config("menu-templates.{$type}.show_auth_links"))->toBeTrue();
})->with(['saas', 'ecommerce']);

it('disables auth links in the service, law, nonprofit, and healthcare templates', function (string $type): void {
    expect(config("menu-templates.{$type}.show_auth_links"))->toBeFalse();
})->with(['service', 'law', 'nonprofit', 'healthcare']);
