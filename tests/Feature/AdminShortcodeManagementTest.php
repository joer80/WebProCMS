<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\Setting;
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

it('processRaw replaces shortcodes without html-escaping plain text', function (): void {
    Shortcode::factory()->singleText()->create(['tag' => 'phone', 'content' => '903-733-2962', 'is_active' => true]);

    $result = ShortcodeProcessor::processRaw('Call us at [[phone]] today.');

    expect($result)->toBe('Call us at 903-733-2962 today.');
});

it('processRaw returns rich text shortcode content unescaped', function (): void {
    Shortcode::factory()->richText()->create(['tag' => 'cta', 'content' => '<strong>Click here</strong>', 'is_active' => true]);

    $result = ShortcodeProcessor::processRaw('[[cta]]');

    expect($result)->toBe('<strong>Click here</strong>');
});

it('processRaw leaves unknown shortcodes as-is', function (): void {
    $result = ShortcodeProcessor::processRaw('Hello [[unknown]]!');

    expect($result)->toBe('Hello [[unknown]]!');
});

it('content helper expands shortcodes in text fields', function (): void {
    Shortcode::factory()->singleText()->create(['tag' => 'phone', 'content' => '903-733-2962', 'is_active' => true]);

    \App\Models\ContentOverride::create([
        'row_slug' => 'test-row:abc123',
        'page_slug' => 'home',
        'key' => 'headline',
        'type' => 'text',
        'value' => 'Call us at [[phone]]',
    ]);

    $result = content('test-row:abc123', 'headline', '', 'text');

    expect($result)->toBe('Call us at 903-733-2962');
});

it('content helper does not expand shortcodes for non-text types', function (): void {
    Shortcode::factory()->singleText()->create(['tag' => 'phone', 'content' => '903-733-2962', 'is_active' => true]);

    \App\Models\ContentOverride::create([
        'row_slug' => 'test-row:abc123',
        'page_slug' => 'home',
        'key' => 'section_classes',
        'type' => 'classes',
        'value' => 'py-section [[phone]]',
    ]);

    $result = content('test-row:abc123', 'section_classes', '', 'classes');

    expect($result)->toBe('py-section [[phone]]');
});

it('resolves system shortcodes from config', function (): void {
    Setting::set('business.phone', '555-0100');

    expect(ShortcodeProcessor::process('Call [[business_phone]]'))->toBe('Call 555-0100');
    expect(ShortcodeProcessor::processRaw('Call [[business_phone]]'))->toBe('Call 555-0100');
});

it('db shortcode takes priority over system shortcode with same tag', function (): void {
    Setting::set('business.phone', '555-0100');
    Shortcode::factory()->singleText()->create(['tag' => 'business_phone', 'content' => '555-9999', 'is_active' => true]);

    expect(ShortcodeProcessor::process('[[business_phone]]'))->toBe('555-9999');
});

it('resolves business_address as combined street and city', function (): void {
    Setting::set('business.address_street', '100 Main St');
    Setting::set('business.address_city_state_zip', 'Austin, TX 78701');

    expect(ShortcodeProcessor::processRaw('[[business_address]]'))->toBe('100 Main St, Austin, TX 78701');
});

it('shows system shortcodes on the shortcodes index page', function (): void {
    Setting::set('business.phone', '555-0100');
    $user = User::factory()->create();

    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    Livewire::actingAs($user)
        ->test('pages::dashboard.shortcodes.index')
        ->assertSeeText('Built-in Shortcodes')
        ->assertSeeText('Phone')
        ->assertSee('[[business_phone]]')
        ->assertSeeText('555-0100');
});
