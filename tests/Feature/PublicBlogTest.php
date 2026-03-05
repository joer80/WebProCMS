<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('renders the blog index page', function (): void {
    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSeeText('Blog');
});

it('lists published posts on the index page', function (): void {
    $post = Post::factory()->published()->create(['title' => 'My Published Post']);
    Post::factory()->draft()->create(['title' => 'My Draft Post']);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSeeText('My Published Post')
        ->assertDontSeeText('My Draft Post');
});

it('filters posts by search term', function (): void {
    Post::factory()->published()->create(['title' => 'Laravel Tips and Tricks']);
    Post::factory()->published()->create(['title' => 'Tailwind CSS Guide']);

    Livewire::test('pages::blog.index')
        ->set('search', 'Laravel')
        ->assertSeeText('Laravel Tips and Tricks')
        ->assertDontSeeText('Tailwind CSS Guide');
});

it('filters posts by category', function (): void {
    $category = Category::factory()->create(['name' => 'Engineering']);
    $other = Category::factory()->create(['name' => 'Design']);

    $engineeringPost = Post::factory()->published()->create([
        'title' => 'Engineering Post',
        'category_id' => $category->id,
    ]);

    $designPost = Post::factory()->published()->create([
        'title' => 'Design Post',
        'category_id' => $other->id,
    ]);

    Livewire::test('pages::blog.index')
        ->call('filterByCategory', $category->slug)
        ->assertSeeText('Engineering Post')
        ->assertDontSeeText('Design Post');
});

it('shows category filter buttons for categories with published posts', function (): void {
    $category = Category::factory()->create(['name' => 'Engineering']);
    Post::factory()->published()->create(['category_id' => $category->id]);

    Livewire::test('pages::blog.index')
        ->assertSeeText('Engineering');
});

it('renders a published post', function (): void {
    $post = Post::factory()->published()->create([
        'title' => 'Hello World',
        'content' => 'This is the post body.',
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertSeeText('Hello World')
        ->assertSeeText('This is the post body.');
});

it('returns 404 for draft posts on the show page', function (): void {
    $post = Post::factory()->draft()->create();

    $this->get(route('blog.show', $post->slug))
        ->assertNotFound();
});

it('returns 404 for unpublished posts on the show page', function (): void {
    $post = Post::factory()->unpublished()->create();

    $this->get(route('blog.show', $post->slug))
        ->assertNotFound();
});

it('renders an unlisted post via direct URL', function (): void {
    $post = Post::factory()->unlisted()->create([
        'title' => 'Unlisted Post',
        'content' => 'Unlisted content.',
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertSeeText('Unlisted Post');
});

it('does not show unlisted posts in the blog index listing', function (): void {
    Post::factory()->unlisted()->create(['title' => 'Unlisted Post']);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertDontSeeText('Unlisted Post');
});

it('returns 404 for a non-existent slug', function (): void {
    $this->get(route('blog.show', 'no-such-post'))
        ->assertNotFound();
});

it('is linked from the public navigation', function (): void {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('blog.index'))
        ->assertSeeText('Blog');
});

it('loads the homepage with published posts present', function (): void {
    Post::factory()->published()->create(['title' => 'Oldest Post', 'published_at' => now()->subDays(10)]);
    Post::factory()->published()->create(['title' => 'Middle Post', 'published_at' => now()->subDays(5)]);
    Post::factory()->published()->create(['title' => 'Newest Post', 'published_at' => now()->subDay()]);
    Post::factory()->published()->create(['title' => 'Fourth Post', 'published_at' => now()]);

    $this->get(route('home'))
        ->assertOk();
});

it('does not show draft posts in the homepage blog section', function (): void {
    Post::factory()->draft()->create(['title' => 'Unpublished Post']);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSeeText('Unpublished Post');
});

it('hides the blog section on the homepage when there are no published posts', function (): void {
    $this->get(route('home'))
        ->assertOk()
        ->assertDontSeeText('From the blog');
});

it('loads the homepage with a post belonging to a category', function (): void {
    $category = Category::factory()->create(['name' => 'Engineering']);
    Post::factory()->published()->create(['title' => 'A Post', 'category_id' => $category->id]);

    $this->get(route('home'))
        ->assertOk();
});

it('shows next post link on the show page when a newer post exists', function (): void {
    $first = Post::factory()->published()->create(['title' => 'First Post', 'published_at' => now()->subDay()]);
    $second = Post::factory()->published()->create(['title' => 'Second Post', 'published_at' => now()]);

    $this->get(route('blog.show', $first->slug))
        ->assertOk()
        ->assertSee(route('blog.show', $second->slug))
        ->assertSeeText('Second Post');
});

it('shows no next post link on the show page when there is no newer post', function (): void {
    $post = Post::factory()->published()->create(['title' => 'Only Post', 'published_at' => now()]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertDontSee(route('blog.show', $post->slug).'">');
});

it('renders cta buttons on the post show page', function (): void {
    $post = Post::factory()->published()->create([
        'cta_buttons' => [
            ['text' => 'Get Started', 'url' => 'https://example.com', 'target' => '_blank'],
            ['text' => 'Learn More', 'url' => 'https://example.com/learn', 'target' => '_self'],
        ],
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertSee('https://example.com')
        ->assertSeeText('Get Started')
        ->assertSee('target="_blank"', false)
        ->assertSeeText('Learn More');
});

it('does not render cta buttons when none are set', function (): void {
    $post = Post::factory()->published()->create(['cta_buttons' => null]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertDontSee('rel="noopener noreferrer"');
});

it('uses the featured image alt text on the show page', function (): void {
    $post = Post::factory()->published()->create([
        'title' => 'My Post',
        'featured_image_alt' => 'Custom alt text for the image',
        'featured_image' => 'posts/some-image.jpg',
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertSee('alt="Custom alt text for the image"', false);
});

it('falls back to the post title as alt text when no alt text is set', function (): void {
    $post = Post::factory()->published()->create([
        'title' => 'My Post Title',
        'featured_image_alt' => null,
        'featured_image' => 'posts/some-image.jpg',
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertSee('alt="My Post Title"', false);
});

it('renders gallery images on the post show page', function (): void {
    Storage::fake('public');
    $path1 = UploadedFile::fake()->image('photo1.jpg')->store('posts', 'public');
    $path2 = UploadedFile::fake()->image('photo2.jpg')->store('posts', 'public');
    $post = Post::factory()->published()->create([
        'gallery_images' => [$path1, $path2],
        'gallery_columns' => 3,
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertSee(Storage::disk('public')->url($path1))
        ->assertSee(Storage::disk('public')->url($path2));
});

it('does not render the gallery section when no gallery images are set', function (): void {
    $post = Post::factory()->published()->create(['gallery_images' => null]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertDontSee('Gallery photo');
});

it('uses configured gallery columns on the post show page', function (): void {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('photo.jpg')->store('posts', 'public');
    $post = Post::factory()->published()->create([
        'gallery_images' => [$path],
        'gallery_columns' => 2,
    ]);

    $this->get(route('blog.show', $post->slug))
        ->assertOk()
        ->assertSee('repeat(2, minmax(0, 1fr))', false);
});
