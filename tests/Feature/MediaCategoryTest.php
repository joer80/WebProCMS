<?php

use App\Enums\Role;
use App\Models\MediaCategory;
use App\Models\MediaItem;
use App\Models\User;
use Livewire\Livewire;

it('redirects unauthenticated users from the media library', function (): void {
    $this->get(route('dashboard.media-library.index'))->assertRedirect(route('login'));
});

it('shows the media library to manager users', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.media-library.index'))
        ->assertOk()
        ->assertSeeText('Media Library');
});

it('seeds an uncategorized default category via migration', function (): void {
    expect(MediaCategory::where('is_default', true)->exists())->toBeTrue();
    expect(MediaCategory::where('slug', 'uncategorized')->first()->name)->toBe('Uncategorized');
});

it('creates a new category', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->set('newCategoryName', 'Team Photos')
        ->call('createCategory');

    expect(MediaCategory::where('name', 'Team Photos')->exists())->toBeTrue();
});

it('auto-generates a slug when creating a category', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->set('newCategoryName', 'Product Screenshots')
        ->call('createCategory');

    expect(MediaCategory::where('slug', 'product-screenshots')->exists())->toBeTrue();
});

it('validates category name is required', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->call('createCategory')
        ->assertHasErrors(['newCategoryName']);
});

it('deletes a category and moves its images to uncategorized', function (): void {
    $user = User::factory()->create();
    $category = MediaCategory::factory()->create();
    $item = MediaItem::factory()->create(['media_category_id' => $category->id, 'path' => 'media/test.jpg']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->call('deleteCategory', $category->id);

    expect(MediaCategory::find($category->id))->toBeNull();

    $defaultCategory = MediaCategory::where('is_default', true)->first();
    expect($item->fresh()->media_category_id)->toBe($defaultCategory->id);
});

it('cannot delete the default uncategorized category', function (): void {
    $user = User::factory()->create();
    $defaultCategory = MediaCategory::where('is_default', true)->first();

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->call('deleteCategory', $defaultCategory->id);

    expect(MediaCategory::find($defaultCategory->id))->not->toBeNull();
});

it('reorders two categories by swapping sort_order', function (): void {
    $user = User::factory()->create();
    $cat1 = MediaCategory::factory()->create(['sort_order' => 1]);
    $cat2 = MediaCategory::factory()->create(['sort_order' => 2]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->call('reorderCategories', $cat1->id, $cat2->id);

    expect($cat1->fresh()->sort_order)->toBe(2);
    expect($cat2->fresh()->sort_order)->toBe(1);
});
