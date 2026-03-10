<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Symfony\Component\Process\Process;

class RebuildAssets implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 30;

    public function handle(): void
    {
        $npm = config('cms.npm_path', 'npm');

        $nodeBinDir = dirname($npm);
        $systemPath = getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin';
        $env = ['PATH' => $nodeBinDir.':'.$systemPath];

        $process = new Process([$npm, 'run', 'build:public'], base_path(), $env);

        $process->setTimeout(120);
        $process->run();
    }
}
