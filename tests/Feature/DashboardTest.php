<?php

use App\Models\Category;
use App\Models\Location;
use App\Models\Post;
use App\Models\Shortcode;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

it('shows the authenticated user name', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create(['name' => 'Jane Doe']);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSeeText('Jane Doe');
});

it('shows total post count', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Post::factory()->published()->count(3)->create();
    Post::factory()->draft()->count(2)->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSeeText('5');
});

it('shows published and draft post sub-counts', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Post::factory()->published()->count(4)->create();
    Post::factory()->draft()->count(2)->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard');

    expect($component->get('postStats'))->toBe([
        'total' => 6,
        'published' => 4,
        'unpublished' => 2,
        'unlisted' => 0,
    ]);
});

it('combines unpublished and draft statuses in the draft count', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Post::factory()->draft()->count(2)->create();
    Post::factory()->unpublished()->count(1)->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard');

    expect($component->get('postStats')['unpublished'])->toBe(3);
});

it('shows the location count', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Location::factory()->count(3)->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSet('locationCount', 3);
});

it('shows the active shortcode count', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Shortcode::factory()->create(['is_active' => true]);
    Shortcode::factory()->create(['is_active' => true]);
    Shortcode::factory()->create(['is_active' => false]);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSet('activeShortcodeCount', 2);
});

it('shows the category count', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Category::factory()->count(4)->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSet('categoryCount', 4);
});

it('shows up to 5 recent posts', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Post::factory()->count(7)->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSet('recentPosts', fn ($posts) => $posts->count() === 5);
});

it('shows recent post titles in the dashboard', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Post::factory()->published()->create(['title' => 'My Latest Post']);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSeeText('My Latest Post');
});

it('shows the drafts section when unpublished posts exist', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Post::factory()->unpublished()->create(['title' => 'My Draft Post']);

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertSeeText('Drafts Needing Attention')
        ->assertSeeText('My Draft Post');
});

it('hides the drafts section when there are no unpublished posts', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Post::factory()->published()->count(2)->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard')
        ->assertDontSeeText('Drafts Needing Attention');
});
