<?php

namespace App\Jobs;

use App\Models\Setting;
use Symfony\Component\Process\Process;

class UpdateCmsJob
{
    public function handle(): void
    {
        $log = [];

        $branch = config('cms.git_branch', 'main');
        $this->runProcess(['git', 'fetch', 'origin', $branch], $log);
        $this->runProcess(['git', 'merge', '--ff-only', 'origin/'.$branch], $log);

        $composer = $this->findComposer();
        $this->runProcess([$composer, 'install', '--no-dev', '--no-interaction', '--optimize-autoloader'], $log);

        $this->runProcess([PHP_BINARY, 'artisan', 'migrate', '--force', '--no-interaction'], $log);

        $this->runProcess([PHP_BINARY, 'artisan', 'design-library:index', '--no-interaction'], $log);

        $npm = $this->findNpm();
        $env = ['PATH' => dirname($npm).':'.(getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin')];
        $this->runProcess([$npm, 'run', 'build'], $log, $env);

        if (! app()->isLocal()) {
            $this->runProcess([PHP_BINARY, 'artisan', 'optimize', '--no-interaction'], $log);
            $this->runProcess([PHP_BINARY, 'artisan', 'responsecache:clear'], $log);
        }

        $latestVersion = Setting::get('update_latest_version', '');

        if ($latestVersion) {
            file_put_contents(base_path('VERSION'), $latestVersion.PHP_EOL);
        }

        Setting::set('update_status', 'complete');
        Setting::set('update_log', implode("\n", $log));
    }

    public function failed(\Throwable $exception): void
    {
        $mergeHint = str_contains($exception->getMessage(), 'ff-only') || str_contains($exception->getMessage(), 'merge')
            ? "\n\n--- How to resolve ---\nYour local branch has diverged from the upstream. The easiest way to resolve this is with GitHub Desktop:\n\n  1. Open GitHub Desktop and select this repository\n  2. Click Branch → Merge into Current Branch and select origin/".config('cms.git_branch', 'main')."\n  3. Resolve any conflicts using the built-in conflict editor\n  4. Commit the merge, then click Update Now again\n\nAlternatively, via command line:\n\n  cd ".base_path()."\n  git status\n  git merge origin/".config('cms.git_branch', 'main')."   # merge and resolve conflicts manually\n  # or: git reset --hard origin/".config('cms.git_branch', 'main')."  (destructive — discards local changes)\n\nAfter resolving, click Update Now again."
            : '';

        Setting::set('update_status', 'failed');
        Setting::set('update_log', Setting::get('update_log', '')."\n\nFailed: ".$exception->getMessage().$mergeHint);
    }

    private function runProcess(array $command, array &$log, array $env = []): void
    {
        $process = new Process($command, base_path(), $env ?: null);
        $process->setTimeout(180);
        $process->run();

        $log[] = '$ '.implode(' ', $command);

        if ($process->getOutput()) {
            $log[] = trim($process->getOutput());
        }

        if (! $process->isSuccessful()) {
            $error = trim($process->getErrorOutput()) ?: 'Process failed (exit '.$process->getExitCode().')';
            $log[] = 'ERROR: '.$error;

            throw new \RuntimeException($error);
        }
    }

    private function findComposer(): string
    {
        if ($configured = config('cms.composer_path')) {
            return $configured;
        }

        // proc_open (used by Process) bypasses open_basedir, so 'which' works
        // even when file_exists() cannot see paths outside the restriction.
        $which = new Process(['which', 'composer']);
        $which->setTimeout(10);
        $which->run();

        if ($which->isSuccessful() && ($path = trim($which->getOutput())) !== '') {
            return $path;
        }

        return 'composer';
    }

    private function findNpm(): string
    {
        if ($configured = config('cms.npm_path')) {
            return $configured;
        }

        $home = getenv('HOME') ?: '';

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
