<?php

use App\Jobs\RebuildAssets;

it('is a unique job with a 30 second window', function (): void {
    $job = new RebuildAssets;

    expect($job->uniqueFor)->toBe(30);
});

it('implements ShouldQueue and ShouldBeUnique', function (): void {
    expect(RebuildAssets::class)
        ->toImplement(\Illuminate\Contracts\Queue\ShouldQueue::class)
        ->toImplement(\Illuminate\Contracts\Queue\ShouldBeUnique::class);
});
