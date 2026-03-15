<?php

namespace App\Jobs;

use App\Models\Setting;
use Symfony\Component\Process\Process;
use ZipArchive;

class ImportBackupJob
{
    public function __construct(
        private string $filePath,
    ) {}

    public function handle(): void
    {
        $zip = new ZipArchive;

        if ($zip->open($this->filePath) !== true) {
            throw new \RuntimeException('Could not open backup zip file.');
        }

        $driver = config('database.default');

        $this->restoreDatabase($zip, $driver);
        $this->restoreMedia($zip);

        $zip->close();
        $this->cleanup();

        Setting::set('backup_status', 'idle');
        Setting::set('backup_error', '');
    }

    public function failed(\Throwable $exception): void
    {
        $this->cleanup();

        Setting::set('backup_status', 'failed');
        Setting::set('backup_error', $exception->getMessage());
    }

    private function restoreDatabase(ZipArchive $zip, string $driver): void
    {
        if ($driver === 'sqlite') {
            $contents = $zip->getFromName('db/database.sqlite');

            if ($contents === false) {
                throw new \RuntimeException('Backup does not contain db/database.sqlite. Was this backup created with a SQLite database?');
            }

            $dbPath = config('database.connections.sqlite.database');
            file_put_contents($dbPath, $contents);
        } elseif ($driver === 'mysql') {
            $contents = $zip->getFromName('db/database.sql');

            if ($contents === false) {
                throw new \RuntimeException('Backup does not contain db/database.sql. Was this backup created with a MySQL database?');
            }

            $this->importMysql($contents);
        } else {
            throw new \RuntimeException("Unsupported database driver: {$driver}");
        }
    }

    private function importMysql(string $sql): void
    {
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $process = new Process([
            'mysql',
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            "--password={$password}",
            $database,
        ]);

        $process->setTimeout(300);
        $process->setInput($sql);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException('mysql import failed: '.trim($process->getErrorOutput()));
        }
    }

    private function restoreMedia(ZipArchive $zip): void
    {
        $mediaRoot = storage_path('app/public');

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (! str_starts_with($name, 'media/')) {
                continue;
            }

            $relativePath = substr($name, strlen('media/'));

            if ($relativePath === '' || str_ends_with($name, '/')) {
                continue;
            }

            $destPath = $mediaRoot.DIRECTORY_SEPARATOR.$relativePath;
            $destDir = dirname($destPath);

            if (! is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            file_put_contents($destPath, $zip->getFromIndex($i));
        }
    }

    private function cleanup(): void
    {
        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }
}
