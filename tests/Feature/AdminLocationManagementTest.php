<?php

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('redirects unauthenticated users from the location dashboard', function (): void {
    $this->get(route('dashboard.locations.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard.locations.create'))->assertRedirect(route('login'));
});

it('shows the locations dashboard to authenticated users', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.locations.index'))
        ->assertOk()
        ->assertSeeText('Locations');
});

it('lists all locations', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Location::factory()->create(['name' => 'GetRows Nashville', 'city' => 'Nashville', 'state' => 'TN']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.index')
        ->assertSeeText('GetRows Nashville')
        ->assertSeeText('Nashville')
        ->assertSeeText('TN');
});

it('creates a new location without a photo', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.create')
        ->set('name', 'GetRows Denver')
        ->set('address', '1600 Glenarm Place')
        ->set('city', 'Denver')
        ->set('state', 'CO')
        ->set('state_full', 'Colorado')
        ->set('zip', '80202')
        ->set('phone', '(303) 555-0101')
        ->call('save');

    expect(Location::where('name', 'GetRows Denver')->exists())->toBeTrue();
});

it('uploads a photo when creating a location', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.create')
        ->set('name', 'GetRows Phoenix')
        ->set('address', '1 N Central Ave')
        ->set('city', 'Phoenix')
        ->set('state', 'AZ')
        ->set('state_full', 'Arizona')
        ->set('zip', '85004')
        ->set('phone', '(602) 555-0101')
        ->set('photo', UploadedFile::fake()->image('location.jpg'))
        ->call('save');

    $location = Location::where('name', 'GetRows Phoenix')->first();
    expect($location->photo)->not->toBeNull();
    Storage::disk('public')->assertExists($location->photo);
});

it('validates required fields on create', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.create')
        ->call('save')
        ->assertHasErrors(['name', 'address', 'city', 'state', 'zip', 'phone']);
});

it('validates state is exactly 2 characters on create', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.create')
        ->set('state', 'Colorado')
        ->call('save')
        ->assertHasErrors(['state']);
});

it('edits an existing location', function (): void {
    $user = User::factory()->create();
    $location = Location::factory()->create(['name' => 'GetRows Old Name', 'city' => 'Memphis', 'state' => 'TN']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.edit', ['location' => $location])
        ->assertSet('name', 'GetRows Old Name')
        ->assertSet('city', 'Memphis')
        ->set('name', 'GetRows New Name')
        ->set('city', 'Nashville')
        ->call('save');

    expect($location->fresh()->name)->toBe('GetRows New Name');
    expect($location->fresh()->city)->toBe('Nashville');
});

it('replaces the photo when a new one is uploaded on edit', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();

    $oldPhoto = UploadedFile::fake()->image('old.jpg')->store('locations', 'public');
    $location = Location::factory()->create(['photo' => $oldPhoto]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.edit', ['location' => $location])
        ->set('photo', UploadedFile::fake()->image('new.jpg'))
        ->call('save');

    Storage::disk('public')->assertMissing($oldPhoto);
    Storage::disk('public')->assertExists($location->fresh()->photo);
});

it('removes the photo from storage when removed on edit', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();

    $photo = UploadedFile::fake()->image('photo.jpg')->store('locations', 'public');
    $location = Location::factory()->create(['photo' => $photo]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.edit', ['location' => $location])
        ->call('removePhoto');

    Storage::disk('public')->assertMissing($photo);
    expect($location->fresh()->photo)->toBeNull();
});

it('uppercases state on save', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.create')
        ->set('name', 'GetRows Portland')
        ->set('address', '1 SW Broadway')
        ->set('city', 'Portland')
        ->set('state', 'or')
        ->set('state_full', 'Oregon')
        ->set('zip', '97201')
        ->set('phone', '(503) 555-0101')
        ->call('save');

    expect(Location::where('name', 'GetRows Portland')->first()->state)->toBe('OR');
});

it('auto-fills state_full when state abbreviation is set', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.create')
        ->set('state', 'TX')
        ->assertSet('state_full', 'Texas');
});

it('deletes a location and its photo from storage', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();

    $photo = UploadedFile::fake()->image('photo.jpg')->store('locations', 'public');
    $location = Location::factory()->create(['photo' => $photo]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.locations.index')
        ->call('deleteLocation', $location->id);

    expect(Location::find($location->id))->toBeNull();
    Storage::disk('public')->assertMissing($photo);
});
