<?php

namespace App\Jobs;

use App\Models\Setting;
use Symfony\Component\Process\Process;
use ZipArchive;

class CreateBackupJob
{
    public function handle(): void
    {
        $backupsDir = storage_path('app/private/backups');

        if (! is_dir($backupsDir)) {
            mkdir($backupsDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d-H-i-s');
        $zipPath = "{$backupsDir}/backup-{$timestamp}.zip";

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Could not create zip file at {$zipPath}");
        }

        $this->addDatabase($zip);
        $this->addMedia($zip);

        $zip->close();

        Setting::set('backup_status', 'idle');
        Setting::set('backup_error', '');
    }

    public function failed(\Throwable $exception): void
    {
        Setting::set('backup_status', 'failed');
        Setting::set('backup_error', $exception->getMessage());
    }

    private function addDatabase(ZipArchive $zip): void
    {
        $driver = config('database.default');

        if ($driver === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');

            if (! file_exists($dbPath)) {
                throw new \RuntimeException("SQLite database file not found: {$dbPath}");
            }

            $zip->addFile($dbPath, 'db/database.sqlite');
        } elseif ($driver === 'mysql') {
            $sql = $this->dumpMysql();
            $zip->addFromString('db/database.sql', $sql);
        } else {
            throw new \RuntimeException("Unsupported database driver: {$driver}");
        }
    }

    private function dumpMysql(): string
    {
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $process = new Process([
            'mysqldump',
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            "--password={$password}",
            '--single-transaction',
            '--routines',
            '--triggers',
            $database,
        ]);

        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException('mysqldump failed: '.trim($process->getErrorOutput()));
        }

        return $process->getOutput();
    }

    private function addMedia(ZipArchive $zip): void
    {
        $mediaRoot = storage_path('app/public');

        if (! is_dir($mediaRoot)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($mediaRoot, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $relativePath = 'media/'.ltrim(str_replace($mediaRoot, '', $file->getPathname()), '/\\');
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }
}
