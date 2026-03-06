<?php

use App\Enums\Role;
use App\Enums\SnippetPlacement;
use App\Enums\SnippetType;
use App\Models\Snippet;
use App\Models\User;
use Livewire\Livewire;

it('redirects unauthenticated users from the snippets dashboard', function (): void {
    $this->get(route('dashboard.snippets.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard.snippets.create'))->assertRedirect(route('login'));
});

it('shows the snippets dashboard to manager users', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.snippets.index'))
        ->assertOk()
        ->assertSeeText('Snippets');
});

it('lists all snippets', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Snippet::factory()->create(['name' => 'Google Analytics', 'type' => SnippetType::Html]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.snippets.index')
        ->assertSeeText('Google Analytics');
});

it('creates a new html snippet', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.snippets.create')
        ->set('name', 'Meta Pixel')
        ->set('type', 'html')
        ->set('placement', 'head')
        ->set('content', '<!-- Meta Pixel -->')
        ->call('save');

    $snippet = Snippet::where('name', 'Meta Pixel')->first();
    expect($snippet)->not->toBeNull();
    expect($snippet->type)->toBe(SnippetType::Html);
    expect($snippet->placement)->toBe(SnippetPlacement::Head);
    expect($snippet->content)->toBe('<!-- Meta Pixel -->');
    expect($snippet->is_active)->toBeTrue();
    expect($snippet->page_path)->toBeNull();
});

it('creates a snippet scoped to a single page', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.snippets.create')
        ->set('name', 'Thank You Goal')
        ->set('type', 'js')
        ->set('placement', 'body_end')
        ->set('content', 'console.log("conversion");')
        ->set('pagePath', '/thank-you')
        ->call('save');

    $snippet = Snippet::where('name', 'Thank You Goal')->first();
    expect($snippet)->not->toBeNull();
    expect($snippet->page_path)->toBe('/thank-you');
});

it('validates required fields on create', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.snippets.create')
        ->call('save')
        ->assertHasErrors(['name']);
});

it('edits an existing snippet', function (): void {
    $user = User::factory()->create();
    $snippet = Snippet::factory()->create(['name' => 'Old Name', 'type' => SnippetType::Html]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.snippets.edit', ['snippet' => $snippet])
        ->assertSet('name', 'Old Name')
        ->set('name', 'New Name')
        ->set('content', '<!-- Updated -->')
        ->call('save');

    expect($snippet->fresh()->name)->toBe('New Name');
    expect($snippet->fresh()->content)->toBe('<!-- Updated -->');
});

it('toggles a snippet active status', function (): void {
    $user = User::factory()->create();
    $snippet = Snippet::factory()->create(['is_active' => true]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.snippets.index')
        ->call('toggleActive', $snippet->id);

    expect($snippet->fresh()->is_active)->toBeFalse();
});

it('deletes a snippet', function (): void {
    $user = User::factory()->create();
    $snippet = Snippet::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.snippets.index')
        ->call('deleteSnippet', $snippet->id);

    expect(Snippet::find($snippet->id))->toBeNull();
});

it('injects head snippet into public pages', function (): void {
    Snippet::factory()->create([
        'type' => SnippetType::Html,
        'placement' => SnippetPlacement::Head,
        'content' => '<!-- head-injection-test -->',
        'is_active' => true,
        'page_path' => null,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<!-- head-injection-test -->', false);
});

it('injects body_end snippet into public pages', function (): void {
    Snippet::factory()->create([
        'type' => SnippetType::Js,
        'placement' => SnippetPlacement::BodyEnd,
        'content' => '<!-- body-end-injection-test -->',
        'is_active' => true,
        'page_path' => null,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<!-- body-end-injection-test -->', false);
});

it('does not inject inactive snippets', function (): void {
    Snippet::factory()->create([
        'type' => SnippetType::Html,
        'placement' => SnippetPlacement::Head,
        'content' => '<!-- inactive-snippet-test -->',
        'is_active' => false,
        'page_path' => null,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('<!-- inactive-snippet-test -->', false);
});

it('only injects page-scoped snippets on the matching page', function (): void {
    Snippet::factory()->create([
        'type' => SnippetType::Html,
        'placement' => SnippetPlacement::Head,
        'content' => '<!-- page-scoped-snippet -->',
        'is_active' => true,
        'page_path' => '/about',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('<!-- page-scoped-snippet -->', false);

    $this->get(route('about'))
        ->assertOk()
        ->assertSee('<!-- page-scoped-snippet -->', false);
});

it('forPage scope matches paths with or without leading slash', function (): void {
    Snippet::query()->delete();

    Snippet::factory()->create([
        'is_active' => true,
        'page_path' => '/about',
    ]);

    expect(Snippet::forPage('about')->count())->toBe(1);
    expect(Snippet::forPage('/about')->count())->toBe(1);
});

it('forPage scope returns global snippets for any page', function (): void {
    Snippet::query()->delete();

    Snippet::factory()->create(['is_active' => true, 'page_path' => null]);
    Snippet::factory()->create(['is_active' => true, 'page_path' => '/about']);

    expect(Snippet::forPage('home')->count())->toBe(1);
    expect(Snippet::forPage('about')->count())->toBe(2);
});

it('has seeded google analytics snippet', function (): void {
    $snippet = Snippet::where('name', 'Google Analytics')->first();

    expect($snippet)->not->toBeNull();
    expect($snippet->type)->toBe(SnippetType::Html);
    expect($snippet->placement)->toBe(SnippetPlacement::Head);
    expect($snippet->content)->toContain('Google Analytics');
    expect($snippet->page_path)->toBeNull();
    expect($snippet->is_active)->toBeTrue();
});
