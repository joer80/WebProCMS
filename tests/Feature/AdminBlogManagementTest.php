<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('redirects unauthenticated users from the blog dashboard', function (): void {
    $this->get(route('dashboard.blog.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard.blog.create'))->assertRedirect(route('login'));
});

it('shows the blog dashboard to authenticated users', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.blog.index'))
        ->assertOk()
        ->assertSeeText('Blog Posts');
});

it('lists all posts (including drafts) in the dashboard', function (): void {
    $user = User::factory()->create();
    Post::factory()->published()->create(['title' => 'Published Post']);
    Post::factory()->draft()->create(['title' => 'Draft Post']);

    $this->actingAs($user)
        ->get(route('dashboard.blog.index'))
        ->assertOk()
        ->assertSeeText('Published Post')
        ->assertSeeText('Draft Post');
});

it('creates a new post', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('title', 'My New Post')
        ->set('content', 'Post body goes here.')
        ->set('status', 'published')
        ->call('save');

    expect(Post::where('title', 'My New Post')->exists())->toBeTrue();
});

it('auto-generates a slug when creating a post', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('title', 'Hello World Post')
        ->set('content', 'Content here.')
        ->set('status', 'draft')
        ->call('save');

    expect(Post::where('slug', 'hello-world-post')->exists())->toBeTrue();
});

it('validates required fields when creating a post', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->call('save')
        ->assertHasErrors(['title', 'content']);
});

it('edits an existing post', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create(['title' => 'Original Title']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->assertSet('title', 'Original Title')
        ->set('title', 'Updated Title')
        ->call('save');

    expect($post->fresh()->title)->toBe('Updated Title');
});

it('sets published_at when a post is first published', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->set('status', 'published')
        ->call('save');

    expect($post->fresh()->published_at)->not->toBeNull();
});

it('deletes a post from the dashboard', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.index')
        ->call('deletePost', $post->id);

    expect(Post::find($post->id))->toBeNull();
});

it('defaults to published status when creating a post', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->assertSet('status', 'published');
});

it('can assign a category when creating a post', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('title', 'Categorized Post')
        ->set('content', 'Content.')
        ->set('status', 'draft')
        ->set('categoryId', $category->id)
        ->call('save');

    $post = Post::where('title', 'Categorized Post')->first();
    expect($post->category_id)->toBe($category->id);
});

it('can create a new category inline while creating a post', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('newCategoryName', 'Inline Category')
        ->call('createCategory');

    $category = Category::where('name', 'Inline Category')->first();
    expect($category)->not->toBeNull();
});

it('auto-selects the new category after inline creation', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('newCategoryName', 'Quick Category')
        ->call('createCategory')
        ->assertSet('categoryId', Category::where('name', 'Quick Category')->value('id'));
});

it('uploads a featured image when creating a post', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('title', 'Post With Image')
        ->set('content', 'Content.')
        ->set('featuredImage', UploadedFile::fake()->image('hero.jpg'))
        ->call('save');

    $post = Post::where('title', 'Post With Image')->first();
    expect($post->featured_image)->not->toBeNull();
    Storage::disk('public')->assertExists($post->featured_image);
});

it('deletes the stored image when a post is deleted', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();
    $path = UploadedFile::fake()->image('hero.jpg')->store('posts', 'public');
    $post = Post::factory()->create(['featured_image' => $path]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.index')
        ->call('deletePost', $post->id);

    Storage::disk('public')->assertMissing($path);
});

it('replaces the featured image when a new one is uploaded on edit', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();
    $oldPath = UploadedFile::fake()->image('old.jpg')->store('posts', 'public');
    $post = Post::factory()->create(['featured_image' => $oldPath]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->set('featuredImage', UploadedFile::fake()->image('new.jpg'))
        ->call('save');

    Storage::disk('public')->assertMissing($oldPath);
    expect($post->fresh()->featured_image)->not->toBe($oldPath);
});
