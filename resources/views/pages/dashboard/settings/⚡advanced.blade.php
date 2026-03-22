<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\ResponseCache\Facades\ResponseCache;

new #[Layout('layouts.app')] #[Title('Advanced Settings')] class extends Component {
    public string $mailMailer = 'log';

    public string $mailHost = '';

    public string $mailPort = '';

    public string $mailUsername = '';

    public string $mailPassword = '';

    public string $mailFromAddress = '';

    public string $mailFromName = '';

    public string $queueConnection = 'database';

    public string $sessionDriver = 'file';

    public string $cacheStore = 'file';

    public string $fullPageCacheDriver = 'file';

    public int $fullPageCacheLifetime = 3600;

    public string $redisHost = '';

    public string $redisPort = '';

    public string $redisPassword = '';

    public string $redisClient = 'phpredis';

    public string $memcachedHost = '';

    public bool $redisAvailable = false;

    public bool $usingSqlite = false;

    public bool $envWritable = false;

    public bool $rebuildAssetsLocally = false;

    public string $aiProvider = 'claude';

    public string $aiClaudeKey = '';

    public string $aiOpenaiKey = '';

    public function mount(): void
    {
        $this->envWritable = is_writable(base_path('.env'));
        $this->redisAvailable = $this->checkRedisAvailable();
        $this->usingSqlite = config('database.default') === 'sqlite';

        $this->mailMailer = config('mail.default', 'log');
        $this->mailHost = config('mail.mailers.smtp.host', '');
        $this->mailPort = (string) config('mail.mailers.smtp.port', '');
        $this->mailUsername = config('mail.mailers.smtp.username') ?? '';
        $this->mailPassword = config('mail.mailers.smtp.password') ?? '';
        $this->mailFromAddress = config('mail.from.address', '');
        $this->mailFromName = config('mail.from.name', '');

        $this->queueConnection = config('queue.default', 'database');

        $this->sessionDriver = config('session.driver', 'file');
        $this->cacheStore = config('cache.default', 'file');
        $this->fullPageCacheDriver = config('responsecache.cache_store', 'file');
        $this->fullPageCacheLifetime = config('responsecache.cache_lifetime_in_seconds', 3600);

        $this->redisClient = config('database.redis.client', 'phpredis');
        $this->redisHost = config('database.redis.default.host', '127.0.0.1');
        $this->redisPort = (string) config('database.redis.default.port', '6379');
        $this->redisPassword = config('database.redis.default.password') ?? '';

        $this->memcachedHost = config('cache.stores.memcached.servers.0.host', '127.0.0.1');

        $this->rebuildAssetsLocally = (bool) config('cms.rebuild_assets_locally');

        $this->aiProvider = \App\Models\Setting::get('ai.provider', 'claude');
        $this->aiClaudeKey = \App\Models\Setting::get('ai.claude_key', '');
        $this->aiOpenaiKey = \App\Models\Setting::get('ai.openai_key', '');
    }

    public function saveMailSettings(): void
    {
        if (! $this->envWritable) {
            return;
        }

        $this->validate([
            'mailMailer' => ['required', 'in:log,smtp,ses,postmark,resend,mailgun,sendmail'],
            'mailHost' => ['nullable', 'string', 'max:255'],
            'mailPort' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mailUsername' => ['nullable', 'string', 'max:255'],
            'mailPassword' => ['nullable', 'string', 'max:255'],
            'mailFromAddress' => ['nullable', 'email', 'max:255'],
            'mailFromName' => ['nullable', 'string', 'max:255'],
        ]);

        $this->writeEnvValue('MAIL_MAILER', $this->mailMailer, 'mail');
        $this->writeEnvValue('MAIL_HOST', $this->mailHost ?: '127.0.0.1', 'mail');
        $this->writeEnvValue('MAIL_PORT', $this->mailPort ?: '2525', 'mail');
        $this->writeEnvValue('MAIL_USERNAME', $this->mailUsername ?: 'null', 'mail');
        $this->writeEnvValue('MAIL_PASSWORD', $this->mailPassword ?: 'null', 'mail');
        $this->writeEnvValue('MAIL_FROM_ADDRESS', $this->mailFromAddress ?: 'hello@example.com', 'mail');
        $this->writeEnvValue('MAIL_FROM_NAME', $this->mailFromName ?: '${APP_NAME}', 'mail');

        if (app()->isProduction()) {
            Artisan::call('config:cache');
            Artisan::call('queue:restart');
        }

        $this->dispatch('notify', message: 'Mail settings saved.');
    }

    public function saveQueueConnection(): void
    {
        if (! $this->envWritable) {
            return;
        }

        $this->validate(['queueConnection' => ['required', 'in:sync,database,redis,beanstalkd,sqs']]);

        if ($this->queueConnection === 'redis' && ! $this->checkRedisAvailable()) {
            $this->addError('queueConnection', 'Redis is not available on this server.');

            return;
        }

        $this->writeEnvValue('QUEUE_CONNECTION', $this->queueConnection, 'queue');

        if (app()->isProduction()) {
            Artisan::call('config:cache');
            Artisan::call('queue:restart');
        }

        $this->dispatch('notify', message: 'Queue connection saved.');
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

        if (app()->isProduction()) {
            Artisan::call('config:cache');
            Artisan::call('queue:restart');
        }

        $this->dispatch('notify', message: 'Settings saved.');
    }

    public function saveCacheStore(): void
    {
        if (! $this->envWritable) {
            return;
        }

        $this->validate(['cacheStore' => ['required', 'in:file,database,redis,memcached']]);

        if ($this->cacheStore === 'redis' && ! $this->checkRedisAvailable()) {
            $this->addError('cacheStore', 'Redis is not available on this server.');

            return;
        }

        $this->writeEnvValue('CACHE_STORE', $this->cacheStore, 'cache');

        if (app()->isProduction()) {
            Artisan::call('config:cache');
            Artisan::call('queue:restart');
        }

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

        if (app()->isProduction()) {
            Artisan::call('config:cache');
            Artisan::call('queue:restart');
        }

        $this->dispatch('notify', message: 'Settings saved.');
    }

    public function saveRedisSettings(): void
    {
        if (! $this->envWritable) {
            return;
        }

        $this->validate([
            'redisClient' => ['required', 'in:phpredis,predis'],
            'redisHost' => ['required', 'string', 'max:255'],
            'redisPort' => ['required', 'integer', 'min:1', 'max:65535'],
            'redisPassword' => ['nullable', 'string', 'max:255'],
        ]);

        $this->writeEnvValue('REDIS_CLIENT', $this->redisClient, 'redis');
        $this->writeEnvValue('REDIS_HOST', $this->redisHost, 'redis');
        $this->writeEnvValue('REDIS_PORT', $this->redisPort, 'redis');
        $this->writeEnvValue('REDIS_PASSWORD', $this->redisPassword ?: 'null', 'redis');

        $this->redisAvailable = $this->checkRedisAvailable();

        if (app()->isProduction()) {
            Artisan::call('config:cache');
            Artisan::call('queue:restart');
        }

        $this->dispatch('notify', message: 'Redis settings saved.');
    }

    public function saveMemcachedSettings(): void
    {
        if (! $this->envWritable) {
            return;
        }

        $this->validate([
            'memcachedHost' => ['required', 'string', 'max:255'],
        ]);

        $this->writeEnvValue('MEMCACHED_HOST', $this->memcachedHost, 'memcached');

        if (app()->isProduction()) {
            Artisan::call('config:cache');
            Artisan::call('queue:restart');
        }

        $this->dispatch('notify', message: 'Memcached settings saved.');
    }

    public function saveAiSettings(): void
    {
        $this->validate([
            'aiProvider' => ['required', 'in:claude,openai'],
            'aiClaudeKey' => ['nullable', 'string', 'max:500'],
            'aiOpenaiKey' => ['nullable', 'string', 'max:500'],
        ]);

        \App\Models\Setting::set('ai.provider', $this->aiProvider);
        \App\Models\Setting::set('ai.claude_key', $this->aiClaudeKey);
        \App\Models\Setting::set('ai.openai_key', $this->aiOpenaiKey);

        $this->dispatch('notify', message: 'AI settings saved.');
    }

    public function saveRebuildAssetsLocally(): void
    {
        if (! $this->envWritable) {
            return;
        }

        $this->writeEnvValue('REBUILD_ASSETS_LOCALLY', $this->rebuildAssetsLocally ? 'true' : 'false', 'rebuild-assets');

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
            <flux:heading size="xl">Advanced Settings</flux:heading>
            <flux:text class="mt-1">Mail, queue, session, cache, and connection settings.</flux:text>
        </div>

        @if(! $envWritable)
            <div class="mb-6 max-w-2xl rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-800 dark:bg-amber-950">
                <flux:text class="text-amber-800 dark:text-amber-300">
                    The <code>.env</code> file is not writable. Environment-based settings cannot be saved until file permissions are corrected.
                </flux:text>
            </div>
        @endif

        <div class="max-w-2xl space-y-4">

            {{-- AI Integration --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>AI Integration</flux:heading>
                        <flux:text class="mt-1">Connect your Claude or ChatGPT account to enable AI content generation in the page editor. Your API key is used when generating content and billed directly to your account.</flux:text>
                        <div x-show="$wire.aiProvider === 'claude'" class="mt-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                            <p><strong class="font-medium text-zinc-800 dark:text-zinc-200">Claude (Anthropic):</strong> Sign in at <span class="font-mono text-xs">console.anthropic.com</span>, go to <strong>Settings → API Keys</strong>, and create a new key. Note: the Anthropic API is separate from Claude.ai subscriptions — you'll need to add a payment method.</p>
                        </div>
                        <div x-show="$wire.aiProvider === 'openai'" class="mt-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                            <p><strong class="font-medium text-zinc-800 dark:text-zinc-200">ChatGPT (OpenAI):</strong> Sign in at <span class="font-mono text-xs">platform.openai.com</span>, go to <strong>Dashboard → API Keys</strong>, and create a new key.</p>
                        </div>
                        <div class="mt-4 space-y-4">
                            <flux:radio.group wire:model="aiProvider" label="Provider">
                                <flux:radio value="claude" label="Claude (Anthropic)" description="Uses claude-haiku-4-5 for fast, high-quality generation." />
                                <flux:radio value="openai" label="ChatGPT (OpenAI)" description="Uses gpt-4o-mini for fast, high-quality generation." />
                            </flux:radio.group>
                            <div x-show="$wire.aiProvider === 'claude'">
                                <flux:input wire:model="aiClaudeKey" label="Claude API Key" type="password" placeholder="sk-ant-..." />
                                <flux:text class="mt-1 text-xs">Get your key at console.anthropic.com</flux:text>
                            </div>
                            <div x-show="$wire.aiProvider === 'openai'">
                                <flux:input wire:model="aiOpenaiKey" label="OpenAI API Key" type="password" placeholder="sk-..." />
                                <flux:text class="mt-1 text-xs">Get your key at platform.openai.com</flux:text>
                            </div>
                        </div>
                    </div>
                    <flux:button wire:click="saveAiSettings" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>

            {{-- Mail --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Mail</flux:heading>
                        <flux:text class="mt-1">Outgoing email transport and sender defaults.</flux:text>
                        <div class="mt-4 space-y-4">
                            <flux:select wire:model="mailMailer" label="Mailer">
                                <flux:select.option value="log">Log (development)</flux:select.option>
                                <flux:select.option value="smtp">SMTP</flux:select.option>
                                <flux:select.option value="ses">Amazon SES</flux:select.option>
                                <flux:select.option value="postmark">Postmark</flux:select.option>
                                <flux:select.option value="resend">Resend</flux:select.option>
                                <flux:select.option value="mailgun">Mailgun</flux:select.option>
                                <flux:select.option value="sendmail">Sendmail</flux:select.option>
                            </flux:select>
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input wire:model="mailHost" label="Host" placeholder="smtp.mailprovider.com" />
                                <flux:input wire:model="mailPort" label="Port" placeholder="587" type="number" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input wire:model="mailUsername" label="Username" placeholder="your@email.com" />
                                <flux:input wire:model="mailPassword" label="Password" type="password" placeholder="••••••••" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input wire:model="mailFromAddress" label="From address" placeholder="hello@domain.com" />
                                <flux:input wire:model="mailFromName" label="From name" placeholder="My App" />
                            </div>
                        </div>
                    </div>
                    <flux:button wire:click="saveMailSettings" variant="outline" :disabled="! $envWritable" class="shrink-0">Save</flux:button>
                </div>
            </div>

            {{-- Queue --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Queue Connection</flux:heading>
                        <flux:text class="mt-1">Driver used for background job processing.</flux:text>
                        <flux:radio.group wire:model="queueConnection" class="mt-4">
                            <flux:radio value="sync" label="Sync" description="Jobs run immediately in the current process. No worker needed." />
                            <flux:radio
                                value="database"
                                label="Database"
                                :description="$usingSqlite ? 'Not recommended — SQLite locks the entire database on each job write.' : 'Store jobs in the database. Run php artisan queue:work to process them.'"
                            />
                            <flux:radio
                                value="redis"
                                label="Redis"
                                :description="$redisAvailable ? 'Store jobs in Redis for fast, scalable queue processing.' : 'Redis is not available on this server.'"
                                :disabled="! $redisAvailable"
                            />
                        </flux:radio.group>
                        @error('queueConnection')
                            <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>
                    <flux:button wire:click="saveQueueConnection" variant="outline" :disabled="! $envWritable" class="shrink-0">Save</flux:button>
                </div>
            </div>

            {{-- Session --}}
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

            {{-- Cache Store --}}
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
                            <flux:radio value="memcached" label="Memcached" description="Store cache in Memcached. Configure the host below." />
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

            {{-- Full Page Cache --}}
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

            {{-- Redis --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Redis</flux:heading>
                        <flux:text class="mt-1">
                            Connection settings for Redis.
                            @if($redisAvailable)
                                <span class="text-green-600 dark:text-green-400">Redis is reachable.</span>
                            @else
                                <span class="text-zinc-500">Redis is not currently reachable.</span>
                            @endif
                        </flux:text>
                        <div class="mt-4 space-y-4">
                            <flux:select wire:model="redisClient" label="Client">
                                <flux:select.option value="phpredis">phpredis (recommended)</flux:select.option>
                                <flux:select.option value="predis">predis</flux:select.option>
                            </flux:select>
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input wire:model="redisHost" label="Host" placeholder="127.0.0.1" />
                                <flux:input wire:model="redisPort" label="Port" placeholder="6379" type="number" />
                            </div>
                            <flux:input wire:model="redisPassword" label="Password" type="password" placeholder="Leave blank if no password" />
                        </div>
                    </div>
                    <flux:button wire:click="saveRedisSettings" variant="outline" :disabled="! $envWritable" class="shrink-0">Save</flux:button>
                </div>
            </div>

            {{-- Memcached --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Memcached</flux:heading>
                        <flux:text class="mt-1">Connection settings for Memcached.</flux:text>
                        <div class="mt-4">
                            <flux:input wire:model="memcachedHost" label="Host" placeholder="127.0.0.1" />
                        </div>
                    </div>
                    <flux:button wire:click="saveMemcachedSettings" variant="outline" :disabled="! $envWritable" class="shrink-0">Save</flux:button>
                </div>
            </div>

            {{-- Asset Rebuilding --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Asset Rebuilding</flux:heading>
                        <flux:text class="mt-1">When enabled, CSS is automatically recompiled on page save in non-production environments — so you don't need to run <code>composer run dev</code> while editing.</flux:text>
                        <div class="mt-4">
                            <flux:switch wire:model="rebuildAssetsLocally" label="Rebuild assets locally on page save" :disabled="! $envWritable" />
                        </div>
                    </div>
                    <flux:button wire:click="saveRebuildAssetsLocally" variant="outline" :disabled="! $envWritable" class="shrink-0">Save</flux:button>
                </div>
            </div>

        </div>
    </flux:main>
</div>
