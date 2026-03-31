<?php

use App\Enums\Role;
use App\Models\Event;
use App\Models\User;
use Livewire\Livewire;

it('redirects unauthenticated users from the events dashboard', function (): void {
    $this->get(route('dashboard.events.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard.events.create'))->assertRedirect(route('login'));
});

it('shows the events dashboard to manager users', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.events.index'))
        ->assertOk()
        ->assertSeeText('Events');
});

it('creates a new event', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.events.create')
        ->set('title', 'My New Event')
        ->set('status', 'published')
        ->set('startDate', now()->addDays(5)->format('Y-m-d\TH:i'))
        ->call('save');

    expect(Event::where('title', 'My New Event')->exists())->toBeTrue();
});

it('validates that start_date is required when creating an event', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.events.create')
        ->set('title', 'No Date Event')
        ->call('save')
        ->assertHasErrors(['startDate']);
});

it('validates that title is required when creating an event', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.events.create')
        ->set('startDate', now()->addDays(5)->format('Y-m-d\TH:i'))
        ->call('save')
        ->assertHasErrors(['title']);
});

it('edits an existing event', function (): void {
    $user = User::factory()->create();
    $event = Event::factory()->draft()->create(['title' => 'Original Title']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.events.edit', ['event' => $event])
        ->assertSet('title', 'Original Title')
        ->set('title', 'Updated Title')
        ->call('save');

    expect($event->fresh()->title)->toBe('Updated Title');
});

it('deletes an event from the dashboard', function (): void {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.events.index')
        ->call('deleteEvent', $event->id);

    expect(Event::find($event->id))->toBeNull();
});

it('deletes child events when a parent event is deleted', function (): void {
    $user = User::factory()->create();
    $parent = Event::factory()->create();
    $child = Event::factory()->create(['parent_event_id' => $parent->id]);

    Livewire::actingAs($user)
        ->test('pages::dashboard.events.index')
        ->call('deleteEvent', $parent->id);

    expect(Event::find($parent->id))->toBeNull();
    expect(Event::find($child->id))->toBeNull();
});

it('lists all events in the dashboard including drafts', function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();

    $user = User::factory()->create();
    Event::factory()->published()->create(['title' => 'Published Event']);
    Event::factory()->draft()->create(['title' => 'Draft Event']);

    Livewire::actingAs($user)
        ->test('pages::dashboard.events.index')
        ->assertSeeText('Published Event')
        ->assertSeeText('Draft Event');
});

it('redirects to edit after creating an event', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.events.create')
        ->set('title', 'Redirect Test Event')
        ->set('startDate', now()->addDays(5)->format('Y-m-d\TH:i'))
        ->call('save');

    $event = Event::where('title', 'Redirect Test Event')->firstOrFail();
    $component->assertRedirect(route('dashboard.events.edit', $event));
});
