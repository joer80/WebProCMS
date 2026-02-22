<?php

use App\Models\Location;
use Livewire\Livewire;

it('renders the locations page', function (): void {
    $this->get(route('locations'))
        ->assertOk()
        ->assertSeeText('Our Locations');
});

it('shows all locations by default', function (): void {
    Location::factory()->create(['name' => 'GetRows Dallas', 'state' => 'TX']);
    Location::factory()->create(['name' => 'GetRows Little Rock', 'state' => 'AR']);

    $this->get(route('locations'))
        ->assertOk()
        ->assertSeeText('GetRows Dallas')
        ->assertSeeText('GetRows Little Rock');
});

it('shows all locations when no state filter is selected', function (): void {
    Location::factory()->create(['name' => 'GetRows Dallas', 'state' => 'TX']);
    Location::factory()->create(['name' => 'GetRows Houston', 'state' => 'TX']);
    Location::factory()->create(['name' => 'GetRows Little Rock', 'state' => 'AR']);
    Location::factory()->create(['name' => 'GetRows Fayetteville', 'state' => 'AR']);
    Location::factory()->create(['name' => 'GetRows Oklahoma City', 'state' => 'OK']);

    Livewire::test('pages::locations')
        ->assertSet('selectedState', '')
        ->assertSeeText('GetRows Dallas')
        ->assertSeeText('GetRows Houston')
        ->assertSeeText('GetRows Little Rock')
        ->assertSeeText('GetRows Fayetteville')
        ->assertSeeText('GetRows Oklahoma City');
});

it('filters locations by state', function (): void {
    Location::factory()->create(['name' => 'GetRows Dallas', 'state' => 'TX']);
    Location::factory()->create(['name' => 'GetRows Houston', 'state' => 'TX']);
    Location::factory()->create(['name' => 'GetRows Little Rock', 'state' => 'AR']);
    Location::factory()->create(['name' => 'GetRows Oklahoma City', 'state' => 'OK']);

    Livewire::test('pages::locations')
        ->call('filterByState', 'TX')
        ->assertSet('selectedState', 'TX')
        ->assertSeeText('GetRows Dallas')
        ->assertSeeText('GetRows Houston')
        ->assertDontSeeText('GetRows Little Rock')
        ->assertDontSeeText('GetRows Oklahoma City');
});

it('filters locations to arkansas', function (): void {
    Location::factory()->create(['name' => 'GetRows Little Rock', 'state' => 'AR']);
    Location::factory()->create(['name' => 'GetRows Fayetteville', 'state' => 'AR']);
    Location::factory()->create(['name' => 'GetRows Dallas', 'state' => 'TX']);

    Livewire::test('pages::locations')
        ->call('filterByState', 'AR')
        ->assertSeeText('GetRows Little Rock')
        ->assertSeeText('GetRows Fayetteville')
        ->assertDontSeeText('GetRows Dallas');
});

it('clears the state filter to show all locations', function (): void {
    Location::factory()->create(['name' => 'GetRows Dallas', 'state' => 'TX']);
    Location::factory()->create(['name' => 'GetRows Oklahoma City', 'state' => 'OK']);

    Livewire::test('pages::locations')
        ->call('filterByState', 'TX')
        ->assertDontSeeText('GetRows Oklahoma City')
        ->call('clearFilter')
        ->assertSet('selectedState', '')
        ->assertSeeText('GetRows Oklahoma City');
});

it('shows state filter buttons for available states', function (): void {
    Location::factory()->create(['state' => 'TX']);
    Location::factory()->create(['state' => 'AR']);
    Location::factory()->create(['state' => 'OK']);

    $this->get(route('locations'))
        ->assertOk()
        ->assertSeeText('TX')
        ->assertSeeText('AR')
        ->assertSeeText('OK');
});

it('shows location details on each card', function (): void {
    Location::factory()->create([
        'address' => '1234 Commerce Street',
        'city' => 'Dallas',
        'state' => 'TX',
        'zip' => '75201',
        'phone' => '(214) 555-0101',
    ]);

    $this->get(route('locations'))
        ->assertOk()
        ->assertSeeText('(214) 555-0101')
        ->assertSeeText('1234 Commerce Street')
        ->assertSeeText('Get Directions');
});

it('is linked from the navigation', function (): void {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('locations'))
        ->assertSeeText('Locations');
});
