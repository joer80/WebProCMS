<?php

namespace App\Console\Commands;

use App\Jobs\UpdateCmsJob;
use Illuminate\Console\Command;

class RunCmsUpdateCommand extends Command
{
    protected $signature = 'cms:update';

    protected $description = 'Run the CMS update process (git pull, composer, migrate, build)';

    public function handle(): void
    {
        $job = new UpdateCmsJob;

        try {
            $job->handle();
        } catch (\Throwable $e) {
            $job->failed($e);
        }
    }
}
