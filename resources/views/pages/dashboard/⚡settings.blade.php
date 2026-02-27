<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Settings')] class extends Component {
    public string $locationsMode = 'single';

    public string $fullPageCacheDriver = 'file';

    public bool $redisAvailable = false;

    public function mount(): void
    {
        $this->locationsMode = Setting::get('locations_mode', 'single');
        $this->fullPageCacheDriver = Setting::get('full_page_cache_driver', 'file');
        $this->redisAvailable = $this->checkRedisAvailable();
    }

    public function saveLocationsMode(): void
    {
        $this->validate(['locationsMode' => ['required', 'in:single,multiple']]);

        Setting::set('locations_mode', $this->locationsMode);

        $this->dispatch('notify', message: 'Settings saved.');
    }

    public function saveFullPageCacheDriver(): void
    {
        $this->validate(['fullPageCacheDriver' => ['required', 'in:file,redis']]);

        if ($this->fullPageCacheDriver === 'redis' && ! $this->checkRedisAvailable()) {
            $this->addError('fullPageCacheDriver', 'Redis is not available on this server.');

            return;
        }

        Setting::set('full_page_cache_driver', $this->fullPageCacheDriver);

        if ($this->fullPageCacheDriver === 'redis') {
            Cache::store('file')->forever('full_page_cache_driver', 'redis');
        } else {
            Cache::store('file')->forget('full_page_cache_driver');
        }

        $this->dispatch('notify', message: 'Settings saved.');
    }

    protected function checkRedisAvailable(): bool
    {
        try {
            Redis::connection()->ping();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Settings</flux:heading>
            <flux:text class="mt-1">Configure your website preferences.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Locations</flux:heading>
                        <flux:text class="mt-1">Choose whether your site has a single location or multiple locations.</flux:text>
                        <flux:radio.group wire:model="locationsMode" class="mt-4">
                            <flux:radio value="single" label="Single location" description="Your site has one primary location." />
                            <flux:radio value="multiple" label="Multiple locations" description="Your site has several locations to display." />
                        </flux:radio.group>
                    </div>
                    <flux:button wire:click="saveLocationsMode" variant="outline" class="shrink-0">
                        Save
                    </flux:button>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Full Page Cache</flux:heading>
                        <flux:text class="mt-1">Choose the cache driver used for full-page caching. Redis is faster but requires a running Redis server.</flux:text>
                        <flux:radio.group wire:model="fullPageCacheDriver" class="mt-4">
                            <flux:radio value="file" label="File" description="Store cached responses on the filesystem. No additional services required." />
                            <flux:radio
                                value="redis"
                                label="Redis"
                                :description="$redisAvailable ? 'Store cached responses in Redis for faster performance.' : 'Redis is not available on this server.'"
                                :disabled="! $redisAvailable"
                            />
                        </flux:radio.group>
                        @error('fullPageCacheDriver')
                            <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>
                    <flux:button wire:click="saveFullPageCacheDriver" variant="outline" class="shrink-0">
                        Save
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:main>
</div>
