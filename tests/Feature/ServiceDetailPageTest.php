<?php

use Livewire\Livewire;

it('renders the services component', function (): void {
    Livewire::test('pages::services')
        ->assertOk();
});
