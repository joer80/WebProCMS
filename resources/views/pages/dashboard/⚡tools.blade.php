<?php

use App\Jobs\IndexDesignLibraryJob;
use App\Jobs\SeedDemoDataJob;
use App\Models\Category;
use App\Models\Location;
use App\Models\Post;
use App\Models\Setting;
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

    public function locationCount(): int
    {
        return Setting::get('locations_mode', 'single') === 'multiple' ? 5 : 1;
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
        IndexDesignLibraryJob::dispatch();

        $this->dispatch('notify', message: 'Design Library sync started.');
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
                        <flux:text class="mt-1">Populate the site with demo blog posts, categories, and {{ $this->locationCount() === 1 ? '1 location' : $this->locationCount() . ' locations' }}. Safe to run multiple times — nothing will be duplicated.</flux:text>
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
