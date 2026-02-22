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
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Post::factory()->published()->create(['title' => 'Published Post']);
    Post::factory()->draft()->create(['title' => 'Draft Post']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.index')
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

it('save on create redirects to the edit page for the new post', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('title', 'Redirect Test Post')
        ->set('content', 'Content.')
        ->call('save');

    $post = Post::where('title', 'Redirect Test Post')->firstOrFail();
    $component->assertRedirect(route('dashboard.blog.edit', $post));
});

it('save on edit stays on the edit page', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create(['title' => 'Stay On Page']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->set('title', 'Stayed')
        ->call('save')
        ->assertNoRedirect();

    expect($post->fresh()->title)->toBe('Stayed');
});

it('saveAndExit redirects to the blog index', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->call('saveAndExit')
        ->assertRedirect(route('dashboard.blog.index'));
});

it('saveAndView redirects to the public post page', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->published()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->call('saveAndView')
        ->assertRedirect(route('blog.show', $post->slug));
});

it('saveAndAddNew redirects to the create page', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->call('saveAndAddNew')
        ->assertRedirect(route('dashboard.blog.create'));
});

it('saveAndNext redirects to the next post edit page', function (): void {
    $user = User::factory()->create();
    $first = Post::factory()->draft()->create();
    $second = Post::factory()->draft()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $first])
        ->call('saveAndNext')
        ->assertRedirect(route('dashboard.blog.edit', $second));
});

it('saveAndNext redirects to the blog index when there is no next post', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->draft()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->call('saveAndNext')
        ->assertRedirect(route('dashboard.blog.index'));
});

it('saves the layout when creating a post', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('title', 'Layout Test Post')
        ->set('content', 'Content.')
        ->set('layout', 'image-right')
        ->call('save');

    expect(Post::where('title', 'Layout Test Post')->value('layout'))->toBe('image-right');
});

it('saves the layout when editing a post', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['layout' => 'image-top']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->assertSet('layout', 'image-top')
        ->set('layout', 'image-right')
        ->call('save');

    expect($post->fresh()->layout)->toBe('image-right');
});

it('can add a cta button when creating a post', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->call('addCtaButton')
        ->assertSet('ctaButtons', [['text' => '', 'url' => '', 'newTab' => false]]);
});

it('cannot add more than two cta buttons', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->call('addCtaButton')
        ->call('addCtaButton')
        ->call('addCtaButton')
        ->assertCount('ctaButtons', 2);
});

it('can remove a cta button', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->call('addCtaButton')
        ->call('addCtaButton')
        ->call('removeCtaButton', 0)
        ->assertCount('ctaButtons', 1);
});

it('saves cta buttons when creating a post', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('title', 'Post With CTA')
        ->set('content', 'Content.')
        ->call('addCtaButton')
        ->set('ctaButtons.0.text', 'Get Started')
        ->set('ctaButtons.0.url', 'https://example.com')
        ->set('ctaButtons.0.newTab', true)
        ->call('save');

    $post = Post::where('title', 'Post With CTA')->first();
    expect($post->cta_buttons)->toBe([
        ['text' => 'Get Started', 'url' => 'https://example.com', 'target' => '_blank'],
    ]);
});

it('filters out incomplete cta buttons when saving', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('title', 'Incomplete CTA Post')
        ->set('content', 'Content.')
        ->call('addCtaButton')
        ->set('ctaButtons.0.text', 'No URL Button')
        ->call('save');

    $post = Post::where('title', 'Incomplete CTA Post')->first();
    expect($post->cta_buttons)->toBeNull();
});

it('loads existing cta buttons when editing a post', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'cta_buttons' => [
            ['text' => 'Learn More', 'url' => 'https://example.com', 'target' => '_self'],
        ],
    ]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->assertSet('ctaButtons', [
            ['text' => 'Learn More', 'url' => 'https://example.com', 'newTab' => false],
        ]);
});

it('updates cta buttons when editing a post', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['cta_buttons' => null]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->call('addCtaButton')
        ->set('ctaButtons.0.text', 'Sign Up')
        ->set('ctaButtons.0.url', 'https://example.com/signup')
        ->set('ctaButtons.0.newTab', false)
        ->call('save');

    expect($post->fresh()->cta_buttons)->toBe([
        ['text' => 'Sign Up', 'url' => 'https://example.com/signup', 'target' => '_self'],
    ]);
});

it('saves featured image alt text when creating a post', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.create')
        ->set('title', 'Alt Text Post')
        ->set('content', 'Content.')
        ->set('featuredImageAlt', 'A descriptive alt text')
        ->call('save');

    expect(Post::where('title', 'Alt Text Post')->value('featured_image_alt'))->toBe('A descriptive alt text');
});

it('saves featured image alt text when editing a post', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['featured_image_alt' => null]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->set('featuredImageAlt', 'Updated alt text')
        ->call('save');

    expect($post->fresh()->featured_image_alt)->toBe('Updated alt text');
});

it('loads existing featured image alt text when editing a post', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['featured_image_alt' => 'Existing alt text']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.blog.edit', ['post' => $post])
        ->assertSet('featuredImageAlt', 'Existing alt text');
});
