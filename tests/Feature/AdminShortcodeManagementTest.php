<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\Shortcode;
use App\Models\User;
use App\Support\ShortcodeProcessor;
use Livewire\Livewire;

it('redirects unauthenticated users from the shortcodes dashboard', function (): void {
    $this->get(route('dashboard.shortcodes.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard.shortcodes.create'))->assertRedirect(route('login'));
});

it('shows the shortcodes dashboard to manager users', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.shortcodes.index'))
        ->assertOk()
        ->assertSeeText('Shortcodes');
});

it('lists all shortcodes', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Shortcode::factory()->singleText()->create(['name' => 'Phone', 'tag' => 'phone', 'content' => '903-733-2962']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.shortcodes.index')
        ->assertSeeText('Phone');
});

it('creates a new shortcode', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.shortcodes.create')
        ->set('name', 'Phone Number')
        ->set('tag', 'phone')
        ->set('type', 'single_text')
        ->set('content', '903-733-2962')
        ->call('save');

    $shortcode = Shortcode::where('tag', 'phone')->first();
    expect($shortcode)->not->toBeNull();
    expect($shortcode->name)->toBe('Phone Number');
    expect($shortcode->content)->toBe('903-733-2962');
    expect($shortcode->is_active)->toBeTrue();
});

it('auto-fills tag from name', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.shortcodes.create')
        ->set('name', 'Phone Number')
        ->assertSet('tag', 'phone_number');
});

it('validates that tag is unique', function (): void {
    $user = User::factory()->create();
    Shortcode::factory()->create(['tag' => 'phone']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.shortcodes.create')
        ->set('name', 'Phone')
        ->set('tag', 'phone')
        ->call('save')
        ->assertHasErrors(['tag']);
});

it('validates required fields on create', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.shortcodes.create')
        ->call('save')
        ->assertHasErrors(['name', 'tag']);
});

it('edits an existing shortcode', function (): void {
    $user = User::factory()->create();
    $shortcode = Shortcode::factory()->singleText()->create(['name' => 'Old Name', 'tag' => 'old_tag']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.shortcodes.edit', ['shortcode' => $shortcode])
        ->assertSet('name', 'Old Name')
        ->set('name', 'New Name')
        ->set('content', 'Updated content')
        ->call('save');

    expect($shortcode->fresh()->name)->toBe('New Name');
    expect($shortcode->fresh()->content)->toBe('Updated content');
});

it('allows editing a shortcode with the same tag (no unique conflict with self)', function (): void {
    $user = User::factory()->create();
    $shortcode = Shortcode::factory()->singleText()->create(['tag' => 'phone']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.shortcodes.edit', ['shortcode' => $shortcode])
        ->set('tag', 'phone')
        ->call('save')
        ->assertHasNoErrors(['tag']);
});

it('toggles a shortcode active status', function (): void {
    $user = User::factory()->create();
    $shortcode = Shortcode::factory()->create(['is_active' => true]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.shortcodes.index')
        ->call('toggleActive', $shortcode->id);

    expect($shortcode->fresh()->is_active)->toBeFalse();
});

it('deletes a shortcode', function (): void {
    $user = User::factory()->create();
    $shortcode = Shortcode::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.shortcodes.index')
        ->call('deleteShortcode', $shortcode->id);

    expect(Shortcode::find($shortcode->id))->toBeNull();
});

it('replaces single text shortcodes in blog post content', function (): void {
    Shortcode::factory()->singleText()->create(['tag' => 'phone', 'content' => '903-733-2962', 'is_active' => true]);

    $result = ShortcodeProcessor::process('Call us at [[phone]] today.');

    expect($result)->toBe('Call us at 903-733-2962 today.');
});

it('ignores inactive shortcodes', function (): void {
    Shortcode::factory()->singleText()->inactive()->create(['tag' => 'phone', 'content' => '903-733-2962']);

    $result = ShortcodeProcessor::process('Call us at [[phone]] today.');

    expect($result)->toContain('[[phone]]');
});

it('escapes html special characters in plain text content', function (): void {
    Shortcode::factory()->singleText()->create(['tag' => 'name', 'content' => '<script>alert("xss")</script>', 'is_active' => true]);

    $result = ShortcodeProcessor::process('Hello [[name]]!');

    expect($result)->toContain('&lt;script&gt;');
    expect($result)->not->toContain('<script>');
});

it('renders rich text shortcodes as raw html', function (): void {
    Shortcode::factory()->richText()->create(['tag' => 'cta', 'content' => '<strong>Click here</strong>', 'is_active' => true]);

    $result = ShortcodeProcessor::process('[[cta]]');

    expect($result)->toBe('<strong>Click here</strong>');
});

it('evaluates php code shortcodes', function (): void {
    Shortcode::factory()->phpCode()->create(['tag' => 'year', 'content' => "echo date('Y');", 'is_active' => true]);

    $result = ShortcodeProcessor::process('Copyright [[year]]');

    expect($result)->toContain((string) date('Y'));
});

it('leaves unknown shortcodes escaped in the output', function (): void {
    $result = ShortcodeProcessor::process('Hello [[unknown]]!');

    expect($result)->toContain('[[unknown]]');
});

it('shows processed shortcodes on the public blog post page', function (): void {
    Shortcode::factory()->singleText()->create(['tag' => 'phone', 'content' => '903-733-2962', 'is_active' => true]);
    $post = Post::factory()->published()->create(['content' => 'Call us at [[phone]]']);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertSee('903-733-2962');
});
