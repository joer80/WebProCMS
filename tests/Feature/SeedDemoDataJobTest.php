<?php

use App\Jobs\SeedDemoDataJob;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;

it('sets seeding_status to complete when the job handles successfully', function (): void {
    Artisan::shouldReceive('call')->andReturn(0);

    (new SeedDemoDataJob)->handle();

    expect(Setting::get('seeding_status'))->toBe('complete');
});

it('sets seeding_status to failed when the job fails', function (): void {
    $job = new SeedDemoDataJob;

    $job->failed(new RuntimeException('Something went wrong.'));

    expect(Setting::get('seeding_status'))->toBe('failed');
});
