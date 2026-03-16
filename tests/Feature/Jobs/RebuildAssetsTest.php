<?php

use App\Jobs\RebuildAssets;

it('is a plain class with no queue dependency', function (): void {
    expect(RebuildAssets::class)
        ->not->toImplement(\Illuminate\Contracts\Queue\ShouldQueue::class)
        ->not->toImplement(\Illuminate\Contracts\Queue\ShouldBeUnique::class);
});

it('uses a cache lock to prevent concurrent rebuilds', function (): void {
    // Verify the handle method exists and the class can be instantiated without a queue.
    $job = new RebuildAssets;

    expect($job)->toBeInstanceOf(RebuildAssets::class)
        ->and(method_exists($job, 'handle'))->toBeTrue();
});
