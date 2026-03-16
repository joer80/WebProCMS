<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;

class RebuildAssets
{
    public function handle(): void
    {
        // Prevent concurrent rebuilds — skip if another rebuild started within the last 30 seconds.
        Cache::lock('rebuild-assets', 30)->get(function () {
            $npm = $this->findNpm();
            $nodeBinDir = dirname($npm);
            $systemPath = getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin';
            $env = ['PATH' => $nodeBinDir.':'.$systemPath];

            $process = new Process([$npm, 'run', 'build:public'], base_path(), $env);
            $process->setTimeout(120);
            $process->run();
        });
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
