<?php

use App\Jobs\SeedDemoDataJob;
use App\Models\Category;
use App\Models\Location;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Spatie\ResponseCache\Facades\ResponseCache;

it('redirects unauthenticated users from the tools page', function (): void {
    $this->get(route('dashboard.tools'))->assertRedirect(route('login'));
});

it('shows the tools page to authenticated users', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.tools'))
        ->assertOk()
        ->assertSeeText('Public Cache');
});

it('clears the response cache and dispatches a notification', function (): void {
    ResponseCache::shouldReceive('clear')->once();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.tools')
        ->call('clearCache')
        ->assertDispatched('notify', message: 'Public cache cleared.');
});

it('deletes only seeded posts, locations, and categories and dispatches a notification', function (): void {
    $seededPost = Post::factory()->state(['is_seeded' => true])->create();
    $manualPost = Post::factory()->state(['is_seeded' => false])->create();
    $seededLocation = Location::factory()->state(['is_seeded' => true])->create();
    $manualLocation = Location::factory()->state(['is_seeded' => false])->create();
    $seededCategory = Category::factory()->state(['is_seeded' => true])->create();
    $manualCategory = Category::factory()->state(['is_seeded' => false])->create();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.tools')
        ->call('deleteSeededDemoData')
        ->assertDispatched('notify', message: 'Seeded demo data deleted.');

    expect(Post::find($seededPost->id))->toBeNull();
    expect(Post::find($manualPost->id))->not->toBeNull();
    expect(Location::find($seededLocation->id))->toBeNull();
    expect(Location::find($manualLocation->id))->not->toBeNull();
    expect(Category::find($seededCategory->id))->toBeNull();
    expect(Category::find($manualCategory->id))->not->toBeNull();
});

it('dispatches the seed demo data job and notifies', function (): void {
    Queue::fake();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.tools')
        ->call('seedDemoData')
        ->assertDispatched('notify', message: 'Seeding started — this may take a minute.');

    Queue::assertPushed(SeedDemoDataJob::class);
    expect(Setting::get('seeding_status'))->toBe('running');
});
