<?php

use App\Models\Setting;
use Spatie\ResponseCache\Middlewares\CacheResponse;

it('shows navigation links from the flat navigation config', function (): void {
    Setting::set('navigation.menus', [
        [
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Our Blog', 'route' => 'blog.index', 'active' => true],
                ['label' => 'About Us', 'route' => 'about', 'active' => true],
                ['label' => 'Contact', 'route' => 'contact', 'active' => true],
            ],
        ],
    ]);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Our Blog')
        ->assertSeeText('About Us')
        ->assertSeeText('Contact');
});

it('does not show inactive navigation items', function (): void {
    Setting::set('navigation.menus', [
        [
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [
                ['label' => 'Visible', 'url' => '#', 'active' => true],
                ['label' => 'Hidden', 'url' => '#', 'active' => false],
            ],
        ],
    ]);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Visible')
        ->assertDontSeeText('Hidden');
});

it('shows footer menus from the flat navigation config', function (): void {
    Setting::set('navigation.footer_slugs', ['footer-links']);
    Setting::set('navigation.menus', [
        [
            'slug' => 'main-navigation',
            'label' => 'Main Navigation',
            'items' => [],
        ],
        [
            'slug' => 'footer-links',
            'label' => 'Company',
            'items' => [
                ['label' => 'Footer Link One', 'url' => '#', 'active' => true],
            ],
        ],
    ]);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Footer Link One');
});

it('loads the homepage', function (): void {
    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk();
});

it('loads the about page', function (): void {
    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('about'))
        ->assertOk();
});

it('loads the blog index', function (): void {
    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('blog.index'))
        ->assertOk();
});
