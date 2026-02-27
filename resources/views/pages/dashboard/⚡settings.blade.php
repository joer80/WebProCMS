<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\ResponseCache\Facades\ResponseCache;

new #[Layout('layouts.app')] #[Title('Settings')] class extends Component {
    public string $locationsMode = 'single';

    public string $sessionDriver = 'file';

    public string $cacheStore = 'file';

    public string $fullPageCacheDriver = 'file';

    public int $fullPageCacheLifetime = 3600;

    public bool $redisAvailable = false;

    public bool $usingSqlite = false;

    public bool $envWritable = false;

    public function mount(): void
    {
        $this->locationsMode = Setting::get('locations_mode', 'single');
        $this->sessionDriver = config('session.driver', 'file');
        $this->cacheStore = config('cache.default', 'file');
        $this->fullPageCacheDriver = config('responsecache.cache_store', 'file');
        $this->fullPageCacheLifetime = config('responsecache.cache_lifetime_in_seconds', 3600);
        $this->redisAvailable = $this->checkRedisAvailable();
        $this->usingSqlite = config('database.default') === 'sqlite';
        $this->envWritable = is_writable(base_path('.env'));
    }

    public function saveLocationsMode(): void
    {
        $this->validate(['locationsMode' => ['required', 'in:single,multiple']]);

        Setting::set('locations_mode', $this->locationsMode);

        $this->dispatch('notify', message: 'Settings saved.');
    }

    public function saveSessionDriver(): void
    {
        if (! $this->envWritable) {
            return;
        }

        $this->validate(['sessionDriver' => ['required', 'in:file,database,redis']]);

        if ($this->sessionDriver === 'redis' && ! $this->checkRedisAvailable()) {
            $this->addError('sessionDriver', 'Redis is not available on this server.');

            return;
        }

        $this->writeEnvValue('SESSION_DRIVER', $this->sessionDriver, 'session');
        Artisan::call('config:clear');

        $this->dispatch('notify', message: 'Settings saved.');
    }

    public function saveCacheStore(): void
    {
        if (! $this->envWritable) {
            return;
        }

        $this->validate(['cacheStore' => ['required', 'in:file,database,redis']]);

        if ($this->cacheStore === 'redis' && ! $this->checkRedisAvailable()) {
            $this->addError('cacheStore', 'Redis is not available on this server.');

            return;
        }

        $this->writeEnvValue('CACHE_STORE', $this->cacheStore, 'cache');
        Artisan::call('config:clear');

        $this->dispatch('notify', message: 'Settings saved.');
    }

    public function saveFullPageCache(): void
    {
        if (! $this->envWritable) {
            return;
        }

        $this->validate([
            'fullPageCacheDriver' => ['required', 'in:file,database,redis'],
            'fullPageCacheLifetime' => ['required', 'integer', 'in:1800,3600,7200,14400,43200,86400'],
        ]);

        if ($this->fullPageCacheDriver === 'redis' && ! $this->checkRedisAvailable()) {
            $this->addError('fullPageCacheDriver', 'Redis is not available on this server.');

            return;
        }

        $this->writeEnvValue('RESPONSE_CACHE_DRIVER', $this->fullPageCacheDriver);
        $this->writeEnvValue('RESPONSE_CACHE_LIFETIME', (string) $this->fullPageCacheLifetime);
        Artisan::call('config:clear');

        $this->dispatch('notify', message: 'Settings saved.');
    }

    public function clearSessions(): void
    {
        match (config('session.driver')) {
            'database' => DB::table(config('session.table', 'sessions'))->truncate(),
            'redis' => $this->clearRedisSessionKeys(),
            default => collect(glob(config('session.files') . '/*'))->each(fn ($file) => @unlink($file)),
        };

        $this->dispatch('notify', message: 'Sessions cleared. All users have been logged out.');
    }

    public function clearApplicationCache(): void
    {
        Cache::flush();

        $this->dispatch('notify', message: 'Application cache cleared.');
    }

    public function clearFullPageCache(): void
    {
        ResponseCache::clear();

        $this->dispatch('notify', message: 'Full page cache cleared.');
    }

    protected function clearRedisSessionKeys(): void
    {
        $redis = Redis::connection(config('session.connection', 'default'));
        $prefix = config('database.redis.options.prefix', '');
        $pattern = $prefix . config('session.cookie', 'laravel_session') . ':*';
        $cursor = '0';

        do {
            [$cursor, $keys] = $redis->scan($cursor, ['match' => $pattern, 'count' => 100]);

            if (! empty($keys)) {
                $redis->del(...$keys);
            }
        } while ($cursor !== '0');
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

    protected function writeEnvValue(string $key, string $value, string $anchor = 'full-page-cache'): void
    {
        $path = base_path('.env');
        $contents = file_get_contents($path);

        if (preg_match('/^' . preg_quote($key, '/') . '=/m', $contents)) {
            $contents = preg_replace('/^' . preg_quote($key, '/') . '=.*/m', "{$key}={$value}", $contents);
        } else {
            $contents = preg_replace('/^# \[' . preg_quote($anchor, '/') . '\]/m', "# [{$anchor}]\n{$key}={$value}", $contents);
        }

        file_put_contents($path, $contents);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Settings</flux:heading>
            <flux:text class="mt-1">Configure your website preferences.</flux:text>
        </div>

        @if(! $envWritable)
            <div class="mb-6 max-w-2xl rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-800 dark:bg-amber-950">
                <flux:text class="text-amber-800 dark:text-amber-300">
                    The <code>.env</code> file is not writable. Environment-based settings cannot be saved until file permissions are corrected.
                </flux:text>
            </div>
        @endif

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
                        <flux:heading>Session Driver</flux:heading>
                        <flux:text class="mt-1">Where user sessions are stored. Redis is faster; avoid database with SQLite.</flux:text>
                        <flux:radio.group wire:model="sessionDriver" class="mt-4">
                            <flux:radio value="file" label="File" description="Store sessions on the filesystem. Simple and works everywhere." />
                            <flux:radio
                                value="database"
                                label="Database"
                                :description="$usingSqlite ? 'Not recommended — SQLite locks the entire database on each session write.' : 'Store sessions in the database.'"
                            />
                            <flux:radio
                                value="redis"
                                label="Redis"
                                :description="$redisAvailable ? 'Store sessions in Redis for fast, scalable session handling.' : 'Redis is not available on this server.'"
                                :disabled="! $redisAvailable"
                            />
                        </flux:radio.group>
                        @error('sessionDriver')
                            <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>
                    <div class="flex flex-col gap-2 shrink-0">
                        <flux:button wire:click="saveSessionDriver" variant="outline" :disabled="! $envWritable">Save</flux:button>
                        <flux:button wire:click="clearSessions" variant="ghost">Clear Sessions</flux:button>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Cache Store</flux:heading>
                        <flux:text class="mt-1">Where Laravel stores general application cache (rate limiting, config, etc.).</flux:text>
                        <flux:radio.group wire:model="cacheStore" class="mt-4">
                            <flux:radio value="file" label="File" description="Store cache on the filesystem. Simple and works everywhere." />
                            <flux:radio
                                value="database"
                                label="Database"
                                :description="$usingSqlite ? 'Not recommended — SQLite is slower for high-frequency cache reads and writes.' : 'Store cache in the database.'"
                            />
                            <flux:radio
                                value="redis"
                                label="Redis"
                                :description="$redisAvailable ? 'Store cache in Redis for the fastest possible read and write performance.' : 'Redis is not available on this server.'"
                                :disabled="! $redisAvailable"
                            />
                        </flux:radio.group>
                        @error('cacheStore')
                            <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>
                    <div class="flex flex-col gap-2 shrink-0">
                        <flux:button wire:click="saveCacheStore" variant="outline" :disabled="! $envWritable">Save</flux:button>
                        <flux:button wire:click="clearApplicationCache" variant="ghost">Clear Cache</flux:button>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Full Page Cache</flux:heading>
                        <flux:text class="mt-1">Configure full-page caching for unauthenticated visitors.</flux:text>

                        <div class="mt-4 space-y-5">
                            <div>
                                <flux:label>Cache driver</flux:label>
                                <flux:radio.group wire:model="fullPageCacheDriver" class="mt-2">
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

                            <div>
                                <flux:select wire:model="fullPageCacheLifetime" label="Cache lifetime">
                                    <flux:select.option value="1800">30 minutes</flux:select.option>
                                    <flux:select.option value="3600">1 hour</flux:select.option>
                                    <flux:select.option value="7200">2 hours</flux:select.option>
                                    <flux:select.option value="14400">4 hours</flux:select.option>
                                    <flux:select.option value="43200">12 hours</flux:select.option>
                                    <flux:select.option value="86400">24 hours</flux:select.option>
                                </flux:select>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 shrink-0">
                        <flux:button wire:click="saveFullPageCache" variant="outline" :disabled="! $envWritable">Save</flux:button>
                        <flux:button wire:click="clearFullPageCache" variant="ghost">Clear Cache</flux:button>
                    </div>
                </div>
            </div>
        </div>
    </flux:main>
</div>
