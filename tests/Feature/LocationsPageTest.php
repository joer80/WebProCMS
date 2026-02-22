<?php

use Livewire\Livewire;

it('renders the locations page', function (): void {
    $this->get(route('locations'))
        ->assertOk()
        ->assertSeeText('Our Locations');
});

it('shows all locations by default', function (): void {
    $this->get(route('locations'))
        ->assertOk()
        ->assertSeeText('GetRows Dallas')
        ->assertSeeText('GetRows Little Rock');
});

it('shows all 10 locations when no state filter is selected', function (): void {
    Livewire::test('pages::locations')
        ->assertSet('selectedState', '')
        ->assertSeeText('GetRows Dallas')
        ->assertSeeText('GetRows Houston')
        ->assertSeeText('GetRows Austin')
        ->assertSeeText('GetRows Little Rock')
        ->assertSeeText('GetRows Fayetteville')
        ->assertSeeText('GetRows Fort Smith')
        ->assertSeeText('GetRows Oklahoma City')
        ->assertSeeText('GetRows New Orleans')
        ->assertSeeText('GetRows Nashville')
        ->assertSeeText('GetRows St. Louis');
});

it('filters locations by state', function (): void {
    Livewire::test('pages::locations')
        ->call('filterByState', 'TX')
        ->assertSet('selectedState', 'TX')
        ->assertSeeText('GetRows Dallas')
        ->assertSeeText('GetRows Houston')
        ->assertSeeText('GetRows Austin')
        ->assertDontSeeText('GetRows Little Rock')
        ->assertDontSeeText('GetRows Oklahoma City');
});

it('filters locations to arkansas', function (): void {
    Livewire::test('pages::locations')
        ->call('filterByState', 'AR')
        ->assertSeeText('GetRows Little Rock')
        ->assertSeeText('GetRows Fayetteville')
        ->assertSeeText('GetRows Fort Smith')
        ->assertDontSeeText('GetRows Dallas');
});

it('clears the state filter to show all locations', function (): void {
    Livewire::test('pages::locations')
        ->call('filterByState', 'TX')
        ->assertDontSeeText('GetRows Nashville')
        ->call('clearFilter')
        ->assertSet('selectedState', '')
        ->assertSeeText('GetRows Nashville');
});

it('shows state filter buttons', function (): void {
    $this->get(route('locations'))
        ->assertOk()
        ->assertSeeText('TX')
        ->assertSeeText('AR');
});

it('shows location details on each card', function (): void {
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
