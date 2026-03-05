<?php

it('renders the content editor service detail page', function (): void {
    $response = $this->get(route('services.content-editor'));

    $response->assertOk();
    $response->assertSeeText('Visual Content Editor');
});

it('loads the services page successfully', function (): void {
    $this->get(route('services'))
        ->assertOk();
});
