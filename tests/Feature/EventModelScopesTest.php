<?php

use App\Models\Event;

it('upcoming scope returns future events', function (): void {
    Event::factory()->past()->published()->create();
    $upcoming = Event::factory()->upcoming()->published()->create();

    $results = Event::query()->published()->upcoming()->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($upcoming->id);
});

it('past scope returns past events', function (): void {
    $past = Event::factory()->past()->published()->create();
    Event::factory()->upcoming()->published()->create();

    $results = Event::query()->published()->past()->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($past->id);
});

it('published scope returns only published events', function (): void {
    Event::factory()->draft()->create();
    $published = Event::factory()->published()->create();

    $results = Event::query()->published()->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($published->id);
});

it('accessible scope returns published and unlisted', function (): void {
    Event::factory()->draft()->create();
    Event::factory()->published()->create();
    Event::factory()->unlisted()->create();

    expect(Event::query()->accessible()->count())->toBe(2);
});

it('parent scope returns events without parent', function (): void {
    $parent = Event::factory()->published()->create();
    Event::factory()->published()->create(['parent_event_id' => $parent->id]);

    expect(Event::query()->parent()->count())->toBe(1)
        ->and(Event::query()->parent()->first()->id)->toBe($parent->id);
});
