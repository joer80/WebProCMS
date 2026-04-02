<?php

use App\Models\Event;
use Livewire\Livewire;

it('renders the events index page', function (): void {
    $this->get(route('events.index'))
        ->assertOk()
        ->assertSeeText('Events');
});

it('lists published upcoming events on the index page', function (): void {
    Event::factory()->published()->upcoming()->create(['title' => 'Published Upcoming Event']);
    Event::factory()->draft()->upcoming()->create(['title' => 'Draft Event']);

    $this->get(route('events.index'))
        ->assertOk()
        ->assertSeeText('Published Upcoming Event');
});

it('does not show draft events on the index page', function (): void {
    Event::factory()->draft()->create(['title' => 'Hidden Draft Event']);

    Livewire::test('pages::events.index')
        ->assertDontSeeText('Hidden Draft Event');
});

it('filters events by search term on title', function (): void {
    Event::factory()->published()->upcoming()->create(['title' => 'Annual Conference 2025']);
    Event::factory()->published()->upcoming()->create(['title' => 'Community Meetup']);

    Livewire::test('pages::events.index')
        ->set('eventSearch', 'Annual Conference')
        ->assertSeeText('Annual Conference 2025')
        ->assertDontSeeText('Community Meetup');
});

it('filters events by search term on venue name', function (): void {
    Event::factory()->published()->upcoming()->create([
        'title' => 'Tech Summit',
        'venue_name' => 'Downtown Arena',
    ]);
    Event::factory()->published()->upcoming()->create([
        'title' => 'Art Show',
        'venue_name' => 'Gallery Space',
    ]);

    Livewire::test('pages::events.index')
        ->set('eventSearch', 'Downtown Arena')
        ->assertSeeText('Tech Summit')
        ->assertDontSeeText('Art Show');
});

it('shows the event show page for an accessible event', function (): void {
    $event = Event::factory()->published()->create(['title' => 'My Public Event']);

    $this->get(route('events.show', $event->slug))
        ->assertOk()
        ->assertSeeText('My Public Event');
});

it('returns 404 for draft events on the show page', function (): void {
    $event = Event::factory()->draft()->create();

    $this->get(route('events.show', $event->slug))
        ->assertNotFound();
});

it('shows unlisted events via direct link', function (): void {
    $event = Event::factory()->unlisted()->create(['title' => 'Unlisted Event']);

    $this->get(route('events.show', $event->slug))
        ->assertOk()
        ->assertSeeText('Unlisted Event');
});

it('shows only parent events on the index', function (): void {
    $parent = Event::factory()->published()->upcoming()->create(['title' => 'Parent Event']);
    Event::factory()->published()->upcoming()->create([
        'title' => 'Child Event',
        'parent_event_id' => $parent->id,
    ]);

    Livewire::test('pages::events.index')
        ->assertSeeText('Parent Event')
        ->assertDontSeeText('Child Event');
});
