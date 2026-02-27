<?php

it('displays business contact info from config on the contact page', function (): void {
    config([
        'business.email' => 'hello@example.com',
        'business.phone' => '+1 (800) 555-0100',
        'business.address_street' => '123 Main St',
        'business.address_city_state_zip' => 'Austin, TX 78701',
        'business.hours' => 'Mon–Fri, 9am–5pm',
    ]);

    $this->get(route('contact'))
        ->assertSuccessful()
        ->assertSee('hello@example.com')
        ->assertSee('+1 (800) 555-0100')
        ->assertSee('123 Main St')
        ->assertSee('Austin, TX 78701')
        ->assertSee('Mon–Fri, 9am–5pm');
});
