<?php

namespace App\Console\Commands;

use App\Jobs\IndexDesignLibraryJob;
use Illuminate\Console\Command;

class IndexDesignLibraryCommand extends Command
{
    protected $signature = 'design-library:index';

    protected $description = 'Index design library template files into the database';

    public function handle(): void
    {
        $this->info('Indexing design library...');
        IndexDesignLibraryJob::dispatchSync();
        $this->info('Done.');
    }
}
