<?php

use App\Enums\Role;
use App\Models\ContentItem;
use App\Models\ContentTypeDefinition;
use App\Models\User;
use App\Support\ContentTypePageGenerator;
use Livewire\Livewire;

it('redirects unauthenticated users from the content types dashboard', function (): void {
    $this->get(route('dashboard.content-types.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard.content-types.create'))->assertRedirect(route('login'));
});

it('shows the content types index to manager users', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.content-types.index'))
        ->assertOk()
        ->assertSeeText('Content Types');
});

it('creates a new content type', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.create')
        ->set('name', 'Meeting Notes')
        ->set('slug', 'meeting-notes')
        ->set('singular', 'Meeting Note')
        ->set('icon', 'document')
        ->call('addField')
        ->set('fields.0.label', 'Title')
        ->set('fields.0.name', 'title')
        ->set('fields.0.type', 'text')
        ->call('save');

    $type = ContentTypeDefinition::where('slug', 'meeting-notes')->first();
    expect($type)->not->toBeNull();
    expect($type->name)->toBe('Meeting Notes');
    expect($type->singular)->toBe('Meeting Note');
    expect($type->fields)->toHaveCount(1);
});

it('auto-derives slug and singular from name', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.create')
        ->set('name', 'Press Releases')
        ->assertSet('slug', 'press-releases')
        ->assertSet('singular', 'Press Releases');
});

it('saves correct field name derived from label', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.create')
        ->set('name', 'Events')
        ->set('slug', 'events')
        ->set('singular', 'Event')
        ->call('addField')
        ->set('fields.0.label', 'Published Date')
        ->set('fields.0.name', 'published_date')
        ->set('fields.0.type', 'date')
        ->call('save');

    $type = ContentTypeDefinition::where('slug', 'events')->first();
    expect($type->fields[0]['name'])->toBe('published_date');
});

it('validates required fields when creating a content type', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.create')
        ->call('save')
        ->assertHasErrors(['name', 'slug', 'singular']);
});

it('validates that slug is unique when creating a content type', function (): void {
    $user = User::factory()->create();
    ContentTypeDefinition::create([
        'name' => 'Events',
        'slug' => 'events',
        'singular' => 'Event',
        'icon' => 'document',
        'fields' => [],
    ]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.create')
        ->set('name', 'Events')
        ->set('slug', 'events')
        ->set('singular', 'Event')
        ->call('save')
        ->assertHasErrors(['slug']);
});

it('can add and remove fields when creating a content type', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.create')
        ->call('addField')
        ->call('addField')
        ->assertCount('fields', 2)
        ->call('removeField', 0)
        ->assertCount('fields', 1);
});

it('can move fields up and down', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.create')
        ->call('addField')
        ->set('fields.0.label', 'First')
        ->set('fields.0.name', 'first')
        ->call('addField')
        ->set('fields.1.label', 'Second')
        ->set('fields.1.name', 'second');

    $component->call('moveFieldDown', 0);
    expect($component->get('fields.0.name'))->toBe('second');
    expect($component->get('fields.1.name'))->toBe('first');

    $component->call('moveFieldUp', 1);
    expect($component->get('fields.0.name'))->toBe('first');
    expect($component->get('fields.1.name'))->toBe('second');
});

it('loads existing content type when editing', function (): void {
    $user = User::factory()->create();
    $type = ContentTypeDefinition::create([
        'name' => 'Testimonials',
        'slug' => 'testimonials',
        'singular' => 'Testimonial',
        'icon' => 'chat-bubble-left',
        'fields' => [
            ['label' => 'Quote', 'name' => 'quote', 'type' => 'richtext', 'options' => '', 'required' => true],
        ],
    ]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.edit', ['contentTypeId' => $type->id])
        ->assertSet('name', 'Testimonials')
        ->assertSet('slug', 'testimonials')
        ->assertSet('singular', 'Testimonial');
});

it('updates an existing content type', function (): void {
    $user = User::factory()->create();
    $type = ContentTypeDefinition::create([
        'name' => 'Old Name',
        'slug' => 'old-name',
        'singular' => 'Old',
        'icon' => 'document',
        'fields' => [],
    ]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.edit', ['contentTypeId' => $type->id])
        ->set('name', 'New Name')
        ->set('slug', 'new-name')
        ->set('singular', 'New')
        ->call('save');

    expect($type->fresh()->name)->toBe('New Name');
    expect($type->fresh()->slug)->toBe('new-name');
});

it('deletes a content type and all its items from the index page', function (): void {
    $user = User::factory()->create();
    $type = ContentTypeDefinition::create([
        'name' => 'Events',
        'slug' => 'events',
        'singular' => 'Event',
        'icon' => 'document',
        'fields' => [],
    ]);
    ContentItem::create(['type_slug' => 'events', 'data' => [], 'status' => 'draft']);
    ContentItem::create(['type_slug' => 'events', 'data' => [], 'status' => 'published']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.index')
        ->call('deleteType', $type->id);

    expect(ContentTypeDefinition::find($type->id))->toBeNull();
    expect(ContentItem::where('type_slug', 'events')->count())->toBe(0);
});

it('deletes a content type and all its items from the edit page', function (): void {
    $user = User::factory()->create();
    $type = ContentTypeDefinition::create([
        'name' => 'Events',
        'slug' => 'events',
        'singular' => 'Event',
        'icon' => 'document',
        'fields' => [],
    ]);
    ContentItem::create(['type_slug' => 'events', 'data' => [], 'status' => 'draft']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content-types.edit', ['contentTypeId' => $type->id])
        ->call('delete')
        ->assertRedirect(route('dashboard.content-types.index'));

    expect(ContentTypeDefinition::find($type->id))->toBeNull();
    expect(ContentItem::where('type_slug', 'events')->count())->toBe(0);
});

it('removes routes and view files when a content type is deleted', function (): void {
    $slug = 'test-cleanup-type';
    $routesPath = base_path('routes/web.php');
    $viewDir = resource_path("views/pages/{$slug}");

    // Inject fake routes and create fake view files
    $indexLine = "\n    Route::livewire('{$slug}', 'pages::{$slug}.index')->name('{$slug}.index');";
    $showLine = "\n    Route::livewire('{$slug}/{id}', 'pages::{$slug}.show')->name('{$slug}.show');";
    file_put_contents($routesPath, file_get_contents($routesPath).$indexLine.$showLine);

    mkdir($viewDir, 0755, true);
    file_put_contents("{$viewDir}/⚡index.blade.php", '<?php // index ?>');
    file_put_contents("{$viewDir}/⚡show.blade.php", '<?php // show ?>');

    app(ContentTypePageGenerator::class)->remove($slug);

    expect(file_get_contents($routesPath))->not->toContain("'{$slug}.index'");
    expect(is_dir($viewDir))->toBeFalse();
})->after(function (): void {
    $slug = 'test-cleanup-type';
    $routesPath = base_path('routes/web.php');
    $viewDir = resource_path("views/pages/{$slug}");

    // Remove injected lines if still present (e.g. test failed before remove() ran)
    $indexLine = "\n    Route::livewire('{$slug}', 'pages::{$slug}.index')->name('{$slug}.index');";
    $showLine = "\n    Route::livewire('{$slug}/{id}', 'pages::{$slug}.show')->name('{$slug}.show');";
    file_put_contents($routesPath, str_replace([$indexLine, $showLine], '', file_get_contents($routesPath)));

    if (is_dir($viewDir)) {
        \Illuminate\Support\Facades\File::deleteDirectory($viewDir);
    }
});

it('returns content types ordered by sort_order then name', function (): void {
    ContentTypeDefinition::create(['name' => 'Zebra', 'slug' => 'zebra', 'singular' => 'Zebra', 'icon' => 'document', 'fields' => [], 'sort_order' => 1]);
    ContentTypeDefinition::create(['name' => 'Alpha', 'slug' => 'alpha', 'singular' => 'Alpha', 'icon' => 'document', 'fields' => [], 'sort_order' => 0]);
    ContentTypeDefinition::create(['name' => 'Beta', 'slug' => 'beta', 'singular' => 'Beta', 'icon' => 'document', 'fields' => [], 'sort_order' => 0]);

    $ordered = ContentTypeDefinition::allOrdered()->pluck('slug')->toArray();
    expect($ordered[0])->toBe('alpha');
    expect($ordered[1])->toBe('beta');
    expect($ordered[2])->toBe('zebra');
});
