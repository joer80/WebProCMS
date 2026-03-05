<?php

use Spatie\ResponseCache\Middlewares\CacheResponse;

it('shows saas navigation links when website type is saas', function (): void {
    config(['features.website_type' => 'saas']);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Features')
        ->assertSeeText('Pricing')
        ->assertSeeText('Blog')
        ->assertSeeText('About');
});

it('shows service navigation links when website type is service', function (): void {
    config(['features.website_type' => 'service']);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Services')
        ->assertSeeText('Locations')
        ->assertSeeText('Blog')
        ->assertSeeText('Contact Us');
});

it('shows ecommerce navigation links when website type is ecommerce', function (): void {
    config(['features.website_type' => 'ecommerce']);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Products')
        ->assertSeeText('About Us')
        ->assertSeeText('Contact Us');
});

it('shows law navigation links when website type is law', function (): void {
    config(['features.website_type' => 'law']);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Practice Areas')
        ->assertSeeText('About Us')
        ->assertSeeText('Contact Us');
});

it('shows nonprofit navigation links when website type is nonprofit', function (): void {
    config(['features.website_type' => 'nonprofit']);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('About')
        ->assertSeeText('Donate')
        ->assertSeeText('Volunteer');
});

it('shows healthcare navigation links when website type is healthcare', function (): void {
    config(['features.website_type' => 'healthcare']);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Patients')
        ->assertSeeText('Employers')
        ->assertSeeText('Locations')
        ->assertSeeText('Careers');
});

it('all new page routes return a successful response', function (string $routeName): void {
    $this->withoutMiddleware(CacheResponse::class)
        ->get(route($routeName))
        ->assertOk();
})->with([
    'features',
    'pricing',
    'products',
    'practice-areas',
    'donate',
    'volunteer',
    'patients',
    'employers',
    'careers',
]);

it('loads the homepage for saas type', function (): void {
    config(['features.website_type' => 'saas']);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk();
});

it('loads the homepage for healthcare type', function (): void {
    config(['features.website_type' => 'healthcare']);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk();
});

it('loads the homepage for nonprofit type', function (): void {
    config(['features.website_type' => 'nonprofit']);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk();
});

it('loads the homepage for law type', function (): void {
    config(['features.website_type' => 'law']);

    $this->withoutMiddleware(CacheResponse::class)
        ->get(route('home'))
        ->assertOk();
});
