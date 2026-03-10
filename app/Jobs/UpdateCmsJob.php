<?php

namespace App\Jobs;

use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Symfony\Component\Process\Process;

class UpdateCmsJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 300;

    public int $timeout = 300;

    public function handle(): void
    {
        $log = [];

        $this->runProcess(['git', 'pull', 'origin', config('cms.git_branch', 'main')], $log);

        $this->runProcess(['composer', 'install', '--no-dev', '--no-interaction', '--optimize-autoloader'], $log);

        $this->runProcess([PHP_BINARY, 'artisan', 'migrate', '--force', '--no-interaction'], $log);

        $npm = $this->findNpm();
        $env = ['PATH' => dirname($npm).':'.(getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin')];
        $this->runProcess([$npm, 'run', 'build'], $log, $env);

        if (! app()->isLocal()) {
            $this->runProcess([PHP_BINARY, 'artisan', 'config:cache', '--no-interaction'], $log);
            $this->runProcess([PHP_BINARY, 'artisan', 'route:cache', '--no-interaction'], $log);
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
        Setting::set('update_status', 'failed');
        Setting::set('update_log', Setting::get('update_log', '')."\n\nFailed: ".$exception->getMessage());
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
