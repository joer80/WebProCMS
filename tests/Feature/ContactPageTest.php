<?php

it('loads the contact page successfully', function (): void {
    $this->get(route('contact'))
        ->assertSuccessful();
});
