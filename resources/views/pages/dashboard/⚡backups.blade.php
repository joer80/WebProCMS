<?php

use App\Jobs\CreateBackupJob;
use App\Jobs\ImportBackupJob;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Backups')] class extends Component {
    use WithFileUploads;

    public $uploadedFile = null;

    #[Computed]
    public function backups(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists('backups')) {
            return [];
        }

        return collect($disk->files('backups'))
            ->filter(fn ($f) => pathinfo($f, PATHINFO_EXTENSION) === 'zip')
            ->map(fn ($file) => [
                'filename' => basename($file),
                'size' => $disk->size($file),
                'modified' => $disk->lastModified($file),
            ])
            ->sortByDesc('modified')
            ->values()
            ->all();
    }

    #[Computed]
    public function backupStatus(): string
    {
        return Setting::get('backup_status', 'idle');
    }

    #[Computed]
    public function backupError(): string
    {
        return Setting::get('backup_error', '');
    }

    public function createBackup(): void
    {
        Setting::set('backup_status', 'running');
        Setting::set('backup_error', '');

        defer(function (): void {
            $job = new CreateBackupJob;
            try {
                $job->handle();
            } catch (\Throwable $e) {
                $job->failed($e);
            }
        });

        unset($this->backupStatus, $this->backupError, $this->backups);

        $this->dispatch('notify', message: 'Backup started — it will appear in the list below shortly.');
    }

    public function deleteBackup(string $filename): void
    {
        $filename = basename($filename);
        Storage::disk('local')->delete('backups/'.$filename);

        unset($this->backups);

        $this->dispatch('notify', message: 'Backup deleted.');
    }

    public function importBackup(): void
    {
        $this->validate([
            'uploadedFile' => ['required', 'file', 'max:512000', 'mimes:zip'],
        ]);

        $storedPath = $this->uploadedFile->storeAs(
            'backups/imports',
            'import-'.now()->format('Y-m-d-H-i-s').'.zip',
            'local'
        );

        $fullPath = storage_path('app/private/'.$storedPath);

        Setting::set('backup_status', 'importing');
        Setting::set('backup_error', '');

        defer(function () use ($fullPath): void {
            $job = new ImportBackupJob($fullPath);
            try {
                $job->handle();
            } catch (\Throwable $e) {
                $job->failed($e);
            }
        });

        $this->uploadedFile = null;
        unset($this->backupStatus, $this->backupError);

        $this->dispatch('notify', message: 'Restore started — the application will use the restored data once complete.');
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}; ?>

<div {{ in_array($this->backupStatus, ['running', 'importing']) ? 'wire:poll.3s' : '' }}>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Backups</flux:heading>
            <flux:text class="mt-1">Create and manage backups of your database and uploaded media files.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">

            {{-- Create Backup --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1 min-w-0">
                        <flux:heading>Create Backup</flux:heading>
                        <flux:text class="mt-1">Creates a <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">.zip</code> archive containing the database and all uploaded media files.</flux:text>

                        @if ($this->backupStatus === 'running')
                            <flux:text class="mt-2 text-sm text-amber-600 dark:text-amber-400">Backup in progress — please wait...</flux:text>
                        @elseif ($this->backupStatus === 'failed' && $this->backupError)
                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">Last backup failed: {{ $this->backupError }}</flux:text>
                        @endif
                    </div>
                    <flux:button
                        wire:click="createBackup"
                        variant="outline"
                        class="shrink-0"
                        :disabled="in_array($this->backupStatus, ['running', 'importing'])"
                    >
                        {{ $this->backupStatus === 'running' ? 'Backing up...' : 'Create Backup' }}
                    </flux:button>
                </div>
            </div>

            {{-- Backup Files --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading>Backup Files</flux:heading>
                <flux:text class="mt-1">Backups stored on this server. Download a file to save it to your computer.</flux:text>

                @if (count($this->backups) > 0)
                    <div class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($this->backups as $backup)
                            <div class="flex items-center justify-between gap-4 py-3" wire:key="{{ $backup['filename'] }}">
                                <div class="min-w-0">
                                    <p class="text-sm font-mono text-zinc-800 dark:text-zinc-200 truncate">{{ $backup['filename'] }}</p>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">
                                        {{ $this->formatBytes($backup['size']) }}
                                        &middot;
                                        {{ \Carbon\Carbon::createFromTimestamp($backup['modified'])->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        icon="arrow-down-tray"
                                        :href="route('dashboard.backups.download', ['filename' => $backup['filename']])"
                                    >
                                        Download
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        icon="trash"
                                        wire:click="deleteBackup('{{ $backup['filename'] }}')"
                                        wire:confirm="Permanently delete this backup file?"
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <flux:text class="mt-4 text-sm text-zinc-400 dark:text-zinc-500">No backups yet. Create one above.</flux:text>
                @endif
            </div>

            {{-- Restore / Import --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex-1 min-w-0">
                    <flux:heading>Restore from Backup</flux:heading>
                    <flux:text class="mt-1">
                        Upload a backup <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">.zip</code> to restore the database and media files. This will <strong>overwrite all current data</strong> — use with caution.
                    </flux:text>

                    @if ($this->backupStatus === 'importing')
                        <flux:text class="mt-3 text-sm text-amber-600 dark:text-amber-400">Restore in progress — please wait...</flux:text>
                    @elseif ($this->backupStatus === 'failed' && $this->backupError)
                        <flux:text class="mt-3 text-sm text-red-600 dark:text-red-400">Last restore failed: {{ $this->backupError }}</flux:text>
                    @endif

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-start">
                        <div class="flex-1">
                            <flux:input
                                type="file"
                                wire:model="uploadedFile"
                                accept=".zip"
                            />
                            @error('uploadedFile')
                                <flux:text class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </div>
                        <flux:button
                            wire:click="importBackup"
                            wire:confirm="This will overwrite the current database and media files with the uploaded backup. Are you absolutely sure?"
                            variant="outline"
                            class="shrink-0"
                            :disabled="! $uploadedFile || in_array($this->backupStatus, ['running', 'importing'])"
                        >
                            {{ $this->backupStatus === 'importing' ? 'Restoring...' : 'Restore' }}
                        </flux:button>
                    </div>
                </div>
            </div>

        </div>
    </flux:main>
</div>
