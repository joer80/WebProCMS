<?php

it('renders the content editor service detail page', function (): void {
    $response = $this->get(route('services.content-editor'));

    $response->assertOk();
    $response->assertSeeText('Visual Content Editor');
});

it('links to the content editor page from the services page', function (): void {
    $response = $this->get(route('services'));

    $response->assertOk();
    $response->assertSee(route('services.content-editor'));
    $response->assertSeeText('Learn more');
});
