<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\ResponseCache\Facades\ResponseCache;

new #[Layout('layouts.app')] #[Title('Tools')] class extends Component {
    public function clearCache(): void
    {
        ResponseCache::clear();

        $this->dispatch('notify', message: 'Public cache cleared.');
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
        </div>
    </flux:main>
</div>
