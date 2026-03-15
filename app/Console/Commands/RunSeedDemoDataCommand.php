<?php

namespace App\Console\Commands;

use App\Jobs\SeedDemoDataJob;
use Illuminate\Console\Command;

class RunSeedDemoDataCommand extends Command
{
    protected $signature = 'cms:seed-demo';

    protected $description = 'Seed the site with demo posts, categories, and locations';

    public function handle(): void
    {
        $job = new SeedDemoDataJob;

        try {
            $job->handle();
        } catch (\Throwable $e) {
            $job->failed($e);
        }
    }
}
