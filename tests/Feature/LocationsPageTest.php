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

it('shows all 5 locations when no state filter is selected', function (): void {
    Livewire::test('pages::locations')
        ->assertSet('selectedState', '')
        ->assertSeeText('GetRows Dallas')
        ->assertSeeText('GetRows Houston')
        ->assertSeeText('GetRows Little Rock')
        ->assertSeeText('GetRows Fayetteville')
        ->assertSeeText('GetRows Oklahoma City');
});

it('filters locations by state', function (): void {
    Livewire::test('pages::locations')
        ->call('filterByState', 'TX')
        ->assertSet('selectedState', 'TX')
        ->assertSeeText('GetRows Dallas')
        ->assertSeeText('GetRows Houston')
        ->assertDontSeeText('GetRows Little Rock')
        ->assertDontSeeText('GetRows Oklahoma City');
});

it('filters locations to arkansas', function (): void {
    Livewire::test('pages::locations')
        ->call('filterByState', 'AR')
        ->assertSeeText('GetRows Little Rock')
        ->assertSeeText('GetRows Fayetteville')
        ->assertDontSeeText('GetRows Dallas');
});

it('clears the state filter to show all locations', function (): void {
    Livewire::test('pages::locations')
        ->call('filterByState', 'TX')
        ->assertDontSeeText('GetRows Oklahoma City')
        ->call('clearFilter')
        ->assertSet('selectedState', '')
        ->assertSeeText('GetRows Oklahoma City');
});

it('shows state filter buttons', function (): void {
    $this->get(route('locations'))
        ->assertOk()
        ->assertSeeText('TX')
        ->assertSeeText('AR')
        ->assertSeeText('OK');
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
