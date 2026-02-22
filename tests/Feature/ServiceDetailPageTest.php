<?php

it('renders the instant query editor service detail page', function (): void {
    $response = $this->get(route('services.instant-query-editor'));

    $response->assertOk();
    $response->assertSeeText('Instant Query Editor');
});

it('links to the instant query editor page from the services page', function (): void {
    $response = $this->get(route('services'));

    $response->assertOk();
    $response->assertSee(route('services.instant-query-editor'));
    $response->assertSeeText('Learn more');
});
