<?php

use App\Models\Category;
use App\Models\Post;
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
        ->call('filterByCategory', $category->id)
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
