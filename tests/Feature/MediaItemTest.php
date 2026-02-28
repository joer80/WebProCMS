<?php

use App\Models\MediaCategory;
use App\Models\MediaItem;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('moves an image to a different category', function (): void {
    $user = User::factory()->create();
    $source = MediaCategory::factory()->create();
    $target = MediaCategory::factory()->create();
    $item = MediaItem::factory()->create(['media_category_id' => $source->id]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->call('moveToCategory', $target->id, $item->id);

    expect($item->fresh()->media_category_id)->toBe($target->id);
});

it('moves all selected images to a category', function (): void {
    $user = User::factory()->create();
    $source = MediaCategory::factory()->create();
    $target = MediaCategory::factory()->create();
    $items = MediaItem::factory(3)->create(['media_category_id' => $source->id]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->set('selectedImageIds', $items->pluck('id')->toArray())
        ->call('moveToCategory', $target->id);

    foreach ($items as $item) {
        expect($item->fresh()->media_category_id)->toBe($target->id);
    }
});

it('reorders two images by swapping sort_order', function (): void {
    $user = User::factory()->create();
    $category = MediaCategory::factory()->create();
    $item1 = MediaItem::factory()->create(['media_category_id' => $category->id, 'sort_order' => 1]);
    $item2 = MediaItem::factory()->create(['media_category_id' => $category->id, 'sort_order' => 2]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->call('reorderImages', $item1->id, $item2->id);

    expect($item1->fresh()->sort_order)->toBe(2);
    expect($item2->fresh()->sort_order)->toBe(1);
});

it('does not reorder images from different categories', function (): void {
    $user = User::factory()->create();
    $cat1 = MediaCategory::factory()->create();
    $cat2 = MediaCategory::factory()->create();
    $item1 = MediaItem::factory()->create(['media_category_id' => $cat1->id, 'sort_order' => 1]);
    $item2 = MediaItem::factory()->create(['media_category_id' => $cat2->id, 'sort_order' => 5]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->call('reorderImages', $item1->id, $item2->id);

    expect($item1->fresh()->sort_order)->toBe(1);
    expect($item2->fresh()->sort_order)->toBe(5);
});

it('deletes an image and removes the file from storage', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('media/test.jpg', 'fake-image');

    $user = User::factory()->create();
    $item = MediaItem::factory()->create(['path' => 'media/test.jpg']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->call('deleteImage', $item->id);

    expect(MediaItem::find($item->id))->toBeNull();
    Storage::disk('public')->assertMissing('media/test.jpg');
});

it('bulk deletes selected images', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $items = MediaItem::factory(3)->create()->each(function (MediaItem $item): void {
        Storage::disk('public')->put($item->path, 'fake');
    });

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->set('selectedImageIds', $items->pluck('id')->toArray())
        ->call('deleteSelected');

    foreach ($items as $item) {
        expect(MediaItem::find($item->id))->toBeNull();
    }
});

it('saves alt text for an image', function (): void {
    $user = User::factory()->create();
    $item = MediaItem::factory()->create(['alt' => '']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->call('startEditingAlt', $item->id, '')
        ->set('editingAltValue', 'A beautiful sunset')
        ->call('saveAlt', $item->id);

    expect($item->fresh()->alt)->toBe('A beautiful sunset');
});

it('filters images by selected category', function (): void {
    $user = User::factory()->create();
    $cat1 = MediaCategory::factory()->create();
    $cat2 = MediaCategory::factory()->create();
    MediaItem::factory(2)->create(['media_category_id' => $cat1->id]);
    MediaItem::factory(3)->create(['media_category_id' => $cat2->id]);

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.media-library.index')
        ->call('selectCategory', $cat1->id);

    expect($component->get('selectedCategoryId'))->toBe($cat1->id);
    expect($component->instance()->images->count())->toBe(2);
});
