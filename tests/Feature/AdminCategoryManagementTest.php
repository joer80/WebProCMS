<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

it('redirects unauthenticated users from the category dashboard', function (): void {
    $this->get(route('dashboard.categories.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard.categories.create'))->assertRedirect(route('login'));
});

it('shows the categories dashboard to authenticated users', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.categories.index'))
        ->assertOk()
        ->assertSeeText('Categories');
});

it('lists all categories with their post counts', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Engineering']);
    Post::factory(3)->create(['category_id' => $category->id]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.categories.index')
        ->assertSeeText('Engineering')
        ->assertSeeText('3');
});

it('creates a new category and auto-generates the slug', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.categories.create')
        ->set('name', 'Product Updates')
        ->call('save');

    $category = Category::where('name', 'Product Updates')->first();
    expect($category)->not->toBeNull();
    expect($category->slug)->toBe('product-updates');
});

it('validates that category name is required', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.categories.create')
        ->call('save')
        ->assertHasErrors(['name']);
});

it('validates that category name is unique', function (): void {
    $user = User::factory()->create();
    Category::factory()->create(['name' => 'Engineering']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.categories.create')
        ->set('name', 'Engineering')
        ->call('save')
        ->assertHasErrors(['name']);
});

it('edits a category and updates the slug', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Old Name', 'slug' => 'old-name']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.categories.edit', ['category' => $category])
        ->assertSet('name', 'Old Name')
        ->set('name', 'New Name')
        ->call('save');

    expect($category->fresh()->name)->toBe('New Name');
    expect($category->fresh()->slug)->toBe('new-name');
});

it('allows editing a category with the same name (no unique conflict with self)', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Engineering']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.categories.edit', ['category' => $category])
        ->set('name', 'Engineering')
        ->call('save')
        ->assertHasNoErrors();
});

it('deletes a category from the dashboard', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.categories.index')
        ->call('deleteCategory', $category->id);

    expect(Category::find($category->id))->toBeNull();
});

it('sets posts category to null when the category is deleted', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->create(['category_id' => $category->id]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.categories.index')
        ->call('deleteCategory', $category->id);

    expect($post->fresh()->category_id)->toBeNull();
});
