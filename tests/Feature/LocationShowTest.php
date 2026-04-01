<?php

use App\Models\Location;

it('shows a location detail page', function (): void {
    $location = Location::factory()->create([
        'name' => 'Seattle Office',
        'address' => '123 Pine St',
        'city' => 'Seattle',
        'state' => 'WA',
        'zip' => '98101',
        'phone' => '(206) 555-0100',
    ]);

    $this->get(route('locations.show', $location->id))
        ->assertOk()
        ->assertSeeText('Seattle Office')
        ->assertSeeText('123 Pine St');
});

it('returns 404 for a non-existent location', function (): void {
    $this->get(route('locations.show', 99999))
        ->assertNotFound();
});
