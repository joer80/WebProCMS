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
        $npm = $this->findNpm();
        $nodeBinDir = dirname($npm);
        $systemPath = getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin';
        $env = ['PATH' => $nodeBinDir.':'.$systemPath];

        $process = new Process([$npm, 'run', 'build:public'], base_path(), $env);
        $process->setTimeout(120);
        $process->run();
    }

    private function findNpm(): string
    {
        if ($configured = config('cms.npm_path')) {
            return $configured;
        }

        $home = getenv('HOME') ?: (function_exists('posix_getuid') ? (posix_getpwuid(posix_getuid())['dir'] ?? '') : '');

        foreach ([
            $home.'/Library/Application Support/Herd/config/nvm/versions/node/*/bin/npm',
            $home.'/.nvm/versions/node/*/bin/npm',
        ] as $pattern) {
            $matches = glob($pattern);
            if (! empty($matches)) {
                return end($matches);
            }
        }

        return 'npm';
    }
}
