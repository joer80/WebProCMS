<?php

use App\Jobs\IndexDesignLibraryJob;
use App\Jobs\SeedDemoDataJob;
use App\Jobs\UpdateCmsJob;
use App\Models\Category;
use App\Models\Location;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\ResponseCache\Facades\ResponseCache;

new #[Layout('layouts.app')] #[Title('Tools')] class extends Component {
    #[Computed]
    public function seedingStatus(): string
    {
        return Setting::get('seeding_status', 'idle');
    }

    #[Computed]
    public function updateStatus(): string
    {
        return Setting::get('update_status', 'idle');
    }

    #[Computed]
    public function currentVersion(): string
    {
        $path = base_path('VERSION');

        return file_exists($path) ? trim(file_get_contents($path)) : 'Unknown';
    }

    #[Computed]
    public function latestVersion(): string
    {
        return Setting::get('update_latest_version', '');
    }

    #[Computed]
    public function updateNotes(): string
    {
        return Setting::get('update_notes', '');
    }

    #[Computed]
    public function updateLog(): string
    {
        return Setting::get('update_log', '');
    }

    #[Computed]
    public function updateAvailable(): bool
    {
        return $this->latestVersion !== ''
            && version_compare($this->latestVersion, $this->currentVersion, '>');
    }

    public function checkForUpdates(): void
    {
        $url = config('cms.releases_api_url');

        if (! $url) {
            $this->dispatch('notify', message: 'CMS_RELEASES_API_URL is not configured.');

            return;
        }

        $response = Http::timeout(10)->get($url);

        if (! $response->successful()) {
            $this->dispatch('notify', message: 'Could not reach the releases API.');

            return;
        }

        $data = $response->json();

        // Support GitHub releases format (tag_name) or custom format (version)
        $version = $data['version'] ?? ltrim($data['tag_name'] ?? '', 'v');
        $notes = $data['notes'] ?? $data['body'] ?? '';

        if (! $version) {
            $this->dispatch('notify', message: 'Invalid response from releases API.');

            return;
        }

        Setting::set('update_latest_version', $version);
        Setting::set('update_notes', $notes);

        unset($this->latestVersion, $this->updateNotes, $this->updateAvailable);

        $this->dispatch('notify', message: version_compare($version, $this->currentVersion, '>')
            ? "Update available: v{$version}"
            : 'You are on the latest version.');
    }

    public function startUpdate(): void
    {
        Setting::set('update_status', 'running');
        Setting::set('update_log', '');
        UpdateCmsJob::dispatch();

        unset($this->updateStatus, $this->updateLog);

        $this->dispatch('notify', message: 'Update started — this may take a minute.');
    }

    public function clearCache(): void
    {
        ResponseCache::clear();

        $this->dispatch('notify', message: 'Public cache cleared.');
    }

    public function deleteSeededDemoData(): void
    {
        Post::query()->where('is_seeded', true)->get()->each->delete();
        Location::query()->where('is_seeded', true)->get()->each->delete();
        Category::query()->where('is_seeded', true)->get()->each->delete();

        $this->dispatch('notify', message: 'Seeded demo data deleted.');
    }

    public function seedDemoData(): void
    {
        Setting::set('seeding_status', 'running');
        SeedDemoDataJob::dispatch();

        $this->dispatch('notify', message: 'Seeding started — this may take a minute.');
    }

    public function syncDesignLibrary(): void
    {
        IndexDesignLibraryJob::dispatchSync();

        $this->dispatch('notify', message: 'Design Library synced successfully.');
    }


}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Tools</flux:heading>
            <flux:text class="mt-1">Utilities for managing your website.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <flux:heading>Public Cache</flux:heading>
                        <flux:text class="mt-1">Force all public pages to regenerate for visitors. Use this after changes that aren't reflected yet.</flux:text>
                        <flux:text class="mt-3 text-xs text-zinc-400 dark:text-zinc-500">
                            Pages are cached for {{ \Carbon\CarbonInterval::seconds(config('responsecache.cache_lifetime_in_seconds'))->cascade()->forHumans() }}.
                        </flux:text>
                    </div>
                    <flux:button wire:click="clearCache" variant="outline" class="shrink-0">
                        Clear Cache
                    </flux:button>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6" {{ $this->seedingStatus === 'running' ? 'wire:poll.3s' : '' }}>
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <flux:heading>Seed Demo Data</flux:heading>
                        <flux:text class="mt-1">Populate the site with demo blog posts, categories, and 5 locations. Safe to run multiple times — nothing will be duplicated.</flux:text>
                        @if ($this->seedingStatus === 'running')
                            <flux:text class="mt-2 text-sm text-amber-600 dark:text-amber-400">Seeding in progress — this may take a minute...</flux:text>
                        @elseif ($this->seedingStatus === 'complete')
                            <flux:text class="mt-2 text-sm text-green-600 dark:text-green-500">Last seed completed successfully.</flux:text>
                        @elseif ($this->seedingStatus === 'failed')
                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">Last seed failed. Check your logs for details.</flux:text>
                        @endif
                    </div>
                    <flux:button wire:click="seedDemoData" variant="outline" class="shrink-0" :disabled="$this->seedingStatus === 'running'">
                        {{ $this->seedingStatus === 'running' ? 'Seeding...' : 'Seed Data' }}
                    </flux:button>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <flux:heading>Sync Design Library</flux:heading>
                        <flux:text class="mt-1">Re-index all template files from <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">resources/design-library/</code> into the database. Run this after adding or modifying library files locally.</flux:text>
                        @if (! app()->isLocal())
                            <flux:text class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                                ⚠ You are not in a local environment. Library file changes should be made locally and committed to git before running on production.
                            </flux:text>
                        @endif
                    </div>
                    <flux:button wire:click="syncDesignLibrary" variant="outline" class="shrink-0">
                        Sync Library
                    </flux:button>
                </div>
            </div>


            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6" {{ $this->updateStatus === 'running' ? 'wire:poll.3s' : '' }}>
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1 min-w-0">
                        <flux:heading>CMS Update</flux:heading>
                        <flux:text class="mt-1">
                            Current version: <strong>v{{ $this->currentVersion }}</strong>
                            @if ($this->latestVersion)
                                &nbsp;&mdash;&nbsp;Latest: <strong>v{{ $this->latestVersion }}</strong>
                            @endif
                        </flux:text>

                        @if ($this->updateStatus === 'running')
                            <flux:text class="mt-2 text-sm text-amber-600 dark:text-amber-400">Update in progress — this may take a minute...</flux:text>
                        @elseif ($this->updateStatus === 'complete')
                            <flux:text class="mt-2 text-sm text-green-600 dark:text-green-500">Update completed successfully.</flux:text>
                        @elseif ($this->updateStatus === 'failed')
                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">Update failed. See the log below for details.</flux:text>
                        @elseif ($this->updateAvailable)
                            <flux:text class="mt-2 text-sm text-amber-600 dark:text-amber-400">A new version is available.</flux:text>
                            @if ($this->updateNotes)
                                <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $this->updateNotes }}</flux:text>
                            @endif
                        @endif

                        @if ($this->updateLog && in_array($this->updateStatus, ['complete', 'failed']))
                            <pre class="mt-3 text-xs bg-zinc-100 dark:bg-zinc-800 rounded p-3 overflow-x-auto whitespace-pre-wrap break-all">{{ $this->updateLog }}</pre>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2 shrink-0">
                        <flux:button wire:click="checkForUpdates" variant="outline" :disabled="$this->updateStatus === 'running'">
                            Check for Updates
                        </flux:button>
                        @if ($this->updateAvailable && $this->updateStatus !== 'running')
                            <flux:button
                                wire:click="startUpdate"
                                wire:confirm="This will pull the latest code and run migrations. Make sure you have a database backup. Continue?"
                                variant="primary"
                            >
                                Update Now
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <flux:heading>Delete Seeded Demo Data</flux:heading>
                        <flux:text class="mt-1">Remove all demo blog posts, locations, and categories created by the seeder. Content you have added manually will not be affected.</flux:text>
                    </div>
                    <flux:button
                        wire:click="deleteSeededDemoData"
                        wire:confirm="This will permanently delete all seeded demo data. Are you sure?"
                        variant="outline"
                        class="shrink-0"
                    >
                        Delete Demo Data
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:main>
</div>
