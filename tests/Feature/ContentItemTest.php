<?php

use App\Enums\Role;
use App\Models\ContentItem;
use App\Models\ContentTypeDefinition;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function makeContentType(array $fields = []): ContentTypeDefinition
{
    return ContentTypeDefinition::create([
        'name' => 'Articles',
        'slug' => 'articles',
        'singular' => 'Article',
        'icon' => 'document-text',
        'fields' => $fields ?: [
            ['label' => 'Title', 'name' => 'title', 'type' => 'text', 'options' => '', 'required' => true],
            ['label' => 'Body', 'name' => 'body', 'type' => 'richtext', 'options' => '', 'required' => false],
        ],
    ]);
}

it('redirects unauthenticated users from the content items dashboard', function (): void {
    $this->get(route('dashboard.content.index', 'articles'))->assertRedirect(route('login'));
});

it('shows the content items index to manager users', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();
    makeContentType();

    $this->actingAs($user)
        ->get(route('dashboard.content.index', 'articles'))
        ->assertOk()
        ->assertSeeText('Articles');
});

it('returns 404 for unknown type slug on index', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.content.index', 'nonexistent'))
        ->assertNotFound();
});

it('lists content items on the index page', function (): void {
    $user = User::factory()->create();
    $type = makeContentType();
    ContentItem::create(['type_slug' => 'articles', 'data' => ['title' => 'My Article'], 'status' => 'published']);
    ContentItem::create(['type_slug' => 'articles', 'data' => ['title' => 'Draft Article'], 'status' => 'draft']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.index', ['typeSlug' => 'articles'])
        ->assertSeeText('My Article')
        ->assertSeeText('Draft Article');
});

it('creates a new content item', function (): void {
    $user = User::factory()->create();
    makeContentType();

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.create', ['typeSlug' => 'articles'])
        ->set('formData.title', 'Hello World')
        ->set('formData.body', 'Some content here.')
        ->set('status', 'published')
        ->call('save');

    $item = ContentItem::where('type_slug', 'articles')->first();
    expect($item)->not->toBeNull();
    expect($item->data['title'])->toBe('Hello World');
    expect($item->status)->toBe('published');
});

it('derives title on save from first text field', function (): void {
    $user = User::factory()->create();
    makeContentType();

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.create', ['typeSlug' => 'articles'])
        ->set('formData.title', 'My Great Article')
        ->call('save');

    $item = ContentItem::where('type_slug', 'articles')->first();
    expect($item->title)->toBe('My Great Article');
});

it('sets published_at when first published', function (): void {
    $user = User::factory()->create();
    makeContentType();

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.create', ['typeSlug' => 'articles'])
        ->set('formData.title', 'Going Live')
        ->set('status', 'published')
        ->call('save');

    $item = ContentItem::where('type_slug', 'articles')->first();
    expect($item->published_at)->not->toBeNull();
});

it('does not set published_at for draft items', function (): void {
    $user = User::factory()->create();
    makeContentType();

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.create', ['typeSlug' => 'articles'])
        ->set('formData.title', 'Draft Item')
        ->set('status', 'draft')
        ->call('save');

    $item = ContentItem::where('type_slug', 'articles')->first();
    expect($item->published_at)->toBeNull();
});

it('redirects to edit page after save on create', function (): void {
    $user = User::factory()->create();
    makeContentType();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.content.create', ['typeSlug' => 'articles'])
        ->set('formData.title', 'Redirect Test')
        ->call('save');

    $item = ContentItem::where('type_slug', 'articles')->firstOrFail();
    $component->assertRedirect(route('dashboard.content.edit', ['articles', $item->id]));
});

it('redirects to index when saveAndExit is called on create', function (): void {
    $user = User::factory()->create();
    makeContentType();

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.create', ['typeSlug' => 'articles'])
        ->set('formData.title', 'Exit Test')
        ->call('saveAndExit')
        ->assertRedirect(route('dashboard.content.index', 'articles'));
});

it('loads existing item data when editing', function (): void {
    $user = User::factory()->create();
    makeContentType();
    $item = ContentItem::create(['type_slug' => 'articles', 'data' => ['title' => 'Existing', 'body' => 'Content'], 'status' => 'draft']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.edit', ['typeSlug' => 'articles', 'itemId' => $item->id])
        ->assertSet('formData.title', 'Existing')
        ->assertSet('status', 'draft');
});

it('updates an existing content item', function (): void {
    $user = User::factory()->create();
    makeContentType();
    $item = ContentItem::create(['type_slug' => 'articles', 'data' => ['title' => 'Old Title'], 'status' => 'draft']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.edit', ['typeSlug' => 'articles', 'itemId' => $item->id])
        ->set('formData.title', 'Updated Title')
        ->call('save');

    expect($item->fresh()->data['title'])->toBe('Updated Title');
});

it('saves no redirect on edit save', function (): void {
    $user = User::factory()->create();
    makeContentType();
    $item = ContentItem::create(['type_slug' => 'articles', 'data' => ['title' => 'Stay'], 'status' => 'draft']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.edit', ['typeSlug' => 'articles', 'itemId' => $item->id])
        ->call('save')
        ->assertNoRedirect();
});

it('saveAndExit on edit redirects to index', function (): void {
    $user = User::factory()->create();
    makeContentType();
    $item = ContentItem::create(['type_slug' => 'articles', 'data' => ['title' => 'Exit'], 'status' => 'draft']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.edit', ['typeSlug' => 'articles', 'itemId' => $item->id])
        ->call('saveAndExit')
        ->assertRedirect(route('dashboard.content.index', 'articles'));
});

it('deletes a content item from the index page', function (): void {
    $user = User::factory()->create();
    makeContentType();
    $item = ContentItem::create(['type_slug' => 'articles', 'data' => [], 'status' => 'draft']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.index', ['typeSlug' => 'articles'])
        ->call('deleteItem', $item->id);

    expect(ContentItem::find($item->id))->toBeNull();
});

it('deletes a content item from the edit page', function (): void {
    $user = User::factory()->create();
    makeContentType();
    $item = ContentItem::create(['type_slug' => 'articles', 'data' => [], 'status' => 'draft']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.edit', ['typeSlug' => 'articles', 'itemId' => $item->id])
        ->call('delete')
        ->assertRedirect(route('dashboard.content.index', 'articles'));

    expect(ContentItem::find($item->id))->toBeNull();
});

it('displayTitle returns Untitled when title is null', function (): void {
    $item = new ContentItem(['type_slug' => 'articles', 'data' => [], 'status' => 'draft']);
    $item->title = null;

    expect($item->displayTitle())->toBe('Untitled');
});

it('displayTitle returns the title when set', function (): void {
    $item = new ContentItem(['type_slug' => 'articles', 'data' => [], 'status' => 'draft']);
    $item->title = 'My Title';

    expect($item->displayTitle())->toBe('My Title');
});

it('published scope returns only published items', function (): void {
    makeContentType();
    ContentItem::create(['type_slug' => 'articles', 'data' => [], 'status' => 'published']);
    ContentItem::create(['type_slug' => 'articles', 'data' => [], 'status' => 'draft']);

    expect(ContentItem::published()->count())->toBe(1);
});

it('uploads an image field when creating an item', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();
    makeContentType([
        ['label' => 'Photo', 'name' => 'photo', 'type' => 'image', 'options' => '', 'required' => false],
    ]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.content.create', ['typeSlug' => 'articles'])
        ->set('imageUploads.photo', UploadedFile::fake()->image('photo.jpg'))
        ->call('uploadImage', 'photo');

    // formData.photo should be set to a storage path
    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.content.create', ['typeSlug' => 'articles'])
        ->set('imageUploads.photo', UploadedFile::fake()->image('test.jpg'))
        ->call('uploadImage', 'photo');

    $path = $component->get('formData.photo');
    expect($path)->not->toBeEmpty();
    Storage::disk('public')->assertExists($path);
});
