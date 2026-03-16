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

        $fullPath = '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:'.(getenv('PATH') ?: '');

        // PHP_BINARY is the FPM binary (or empty) in web context — resolve the CLI binary.
        $phpCli = $this->findPhpCli($fullPath);

        $composer = $this->resolveComposer($fullPath);

        // Invoke via PHP CLI with open_basedir disabled when composer is an absolute
        // path — on managed hosts (e.g. RunCloud) the phar lives outside the allowed
        // open_basedir paths and Phar::mapPhar() fails when called from php-fpm.
        $composerCmd = str_starts_with($composer, '/')
            ? [$phpCli, '-d', 'open_basedir=', $composer]
            : [$composer];

        // Composer requires HOME or COMPOSER_HOME — FPM may not set HOME, so fall
        // back to a writable temp dir (which is always within open_basedir on managed hosts).
        $composerEnv = ['PATH' => $fullPath];
        if ($home = getenv('HOME')) {
            $composerEnv['HOME'] = $home;
        } else {
            $composerEnv['COMPOSER_HOME'] = sys_get_temp_dir().'/composer';
            @mkdir($composerEnv['COMPOSER_HOME'], 0755, true);
        }

        $this->runProcess([...$composerCmd, 'install', '--no-dev', '--no-interaction', '--optimize-autoloader'], $log, $composerEnv);

        $this->runProcess([$phpCli, 'artisan', 'migrate', '--force', '--no-interaction'], $log);

        $this->runProcess([$phpCli, 'artisan', 'design-library:index', '--no-interaction'], $log);

        $npm = $this->findNpm();
        // Merge current env so Node has HOME, USER, TMPDIR, etc.
        // Lift the virtual-address-space soft limit before spawning Node — PHP-FPM
        // workers inherit a low rlimit_as that causes malloc to fail for tiny
        // allocations inside Node's startup sequence.
        $npmPath = dirname($npm).':'.(getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin');
        $npmEnv = array_merge(getenv() ?: [], [
            'PATH' => $npmPath,
            'NODE_OPTIONS' => '--disable-wasm-trap-handler',
        ]);
        $npmEscaped = escapeshellarg($npm);
        $this->runProcess(
            ['bash', '-c', 'ulimit -v unlimited 2>/dev/null; ulimit -s unlimited 2>/dev/null; '.$npmEscaped.' run build'],
            $log,
            $npmEnv
        );

        if (! app()->isLocal()) {
            $this->runProcess([$phpCli, 'artisan', 'optimize', '--no-interaction'], $log);
            $this->runProcess([$phpCli, 'artisan', 'responsecache:clear'], $log);
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

    private function findPhpCli(string $fullPath): string
    {
        // PHP_BINARY in FPM context may be the FPM binary (e.g. php8.2-fpm) or empty.
        // Use `which` to find the real CLI binary from PATH instead.
        foreach (['php', 'php8.4', 'php8.3', 'php8.2', 'php8.1', 'php8.0'] as $candidate) {
            $process = new Process(['which', $candidate], base_path(), ['PATH' => $fullPath]);
            $process->run();

            if ($process->isSuccessful() && ($path = trim($process->getOutput())) !== '') {
                return $path;
            }
        }

        return PHP_BINARY ?: 'php';
    }

    private function resolveComposer(string $fullPath): string
    {
        if ($configured = config('cms.composer_path')) {
            return $configured;
        }

        $process = new Process(['which', 'composer'], base_path(), ['PATH' => $fullPath]);
        $process->run();

        if ($process->isSuccessful() && ($path = trim($process->getOutput())) !== '') {
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
