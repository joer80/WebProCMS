<?php

use Livewire\Livewire;

it('renders the content editor service detail component', function (): void {
    Livewire::test('pages::services.content-editor')
        ->assertOk();
});

it('renders the services component', function (): void {
    Livewire::test('pages::services')
        ->assertOk();
});
