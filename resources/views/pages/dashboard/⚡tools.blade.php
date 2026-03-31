<?php

use App\Enums\FormType;
use App\Jobs\IndexDesignLibraryJob;
use App\Jobs\SeedDemoDataJob;
use App\Jobs\UpdateCmsJob;
use App\Models\Category;
use App\Models\ContentItem;
use App\Models\ContentTypeDefinition;
use App\Models\Event;
use App\Models\Form;
use App\Models\Location;
use App\Models\Post;
use App\Models\Setting;
use App\Support\ContentTypePageGenerator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\ResponseCache\Facades\ResponseCache;

new #[Layout('layouts.app')] #[Title('Tools')] class extends Component
{
    /** @var array<string, bool> */
    public array $seedWith = [
        'blog' => true,
        'events' => true,
        'locations' => true,
        'content_types' => true,
        'forms' => true,
        'navigation' => true,
    ];

    /** @var array<string, bool> */
    public array $deleteWith = [
        'blog' => true,
        'events' => true,
        'locations' => true,
        'content_types' => true,
        'forms' => true,
        'navigation' => true,
        'pages' => true,
    ];

    public function mount(): void
    {
        if (Setting::get('update_status') === 'complete') {
            Setting::set('update_status', 'idle');
            Setting::set('update_log', '');
        }
    }

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

    public function resetUpdateStatus(): void
    {
        Setting::set('update_status', 'idle');
        Setting::set('update_log', '');

        unset($this->updateStatus, $this->updateLog);

        $this->dispatch('notify', message: 'Update status reset.');
    }

    public function startUpdate(): void
    {
        Setting::set('update_status', 'running');
        Setting::set('update_log', '');

        defer(function () {
            $job = new UpdateCmsJob;
            try {
                $job->handle();
            } catch (\Throwable $e) {
                $job->failed($e);
            }
        });

        unset($this->updateStatus, $this->updateLog);

        $this->dispatch('notify', message: 'Update started — this may take a minute.');
    }

    public function clearCache(): void
    {
        ResponseCache::clear();

        $this->dispatch('notify', message: 'Public cache cleared.');
    }

    public function optimizeClear(): void
    {
        defer(function () {
            foreach (['config:clear', 'route:clear', 'view:clear', 'event:clear', 'cache:clear'] as $command) {
                try {
                    Artisan::call($command);
                } catch (\Throwable) {
                    // ignore missing files or other non-fatal errors per command
                }
            }
        });

        $this->dispatch('notify', message: 'Application caches cleared (config, route, view, events).');
    }

    public function deleteSeededDemoData(): void
    {
        if ($this->deleteWith['blog'] ?? false) {
            Post::query()->where('is_seeded', true)->get()->each->delete();
            Category::query()->where('is_seeded', true)->get()->each->delete();
        }

        if ($this->deleteWith['events'] ?? false) {
            Event::query()->where('is_seeded', true)->get()->each->delete();
        }

        if ($this->deleteWith['locations'] ?? false) {
            Location::query()->where('is_seeded', true)->get()->each->delete();
        }

        // Delete demo forms (Employment Application, Photo Contest) but keep the Contact Form.
        if ($this->deleteWith['forms'] ?? false) {
            Form::query()
                ->where('is_seeded', true)
                ->where('type', '!=', FormType::Contact->value)
                ->get()->each->delete();
        }

        // Remove seeded navigation items (About, Blog) but leave the base menu (Home, Contact) intact.
        if ($this->deleteWith['navigation'] ?? false) {
            $seededRoutes = Setting::get('navigation.seeded_routes', []);

            if (! empty($seededRoutes)) {
                $menus = array_map(function (array $menu) use ($seededRoutes): array {
                    $menu['items'] = array_values(
                        array_filter($menu['items'], fn (array $item) => ! in_array($item['route'] ?? '', $seededRoutes))
                    );

                    return $menu;
                }, Setting::get('navigation.menus', []));

                Setting::set('navigation.menus', $menus);
                Setting::set('navigation.seeded_routes', []);
            }
        }

        if ($this->deleteWith['content_types'] ?? false) {
            $generator = app(ContentTypePageGenerator::class);

            ContentTypeDefinition::query()->where('is_seeded', true)->each(function (ContentTypeDefinition $type) use ($generator): void {
                ContentItem::query()->where('type_slug', $type->slug)->delete();
                $generator->remove($type->slug);
                $type->delete();
            });
        }

        if ($this->deleteWith['pages'] ?? false) {
            $this->removeUnchangedSeededPages();
        }

        $this->dispatch('notify', message: 'Seeded demo data deleted.');
    }

    /**
     * Delete seeded client pages whose content has not changed since seeding,
     * and remove their routes from routes/web.php.
     */
    private function removeUnchangedSeededPages(): void
    {
        /** @var array<string, array{hash: string, route_check: string|null}> $pages */
        $pages = Setting::get('seeded_client_pages', []);

        if (empty($pages)) {
            return;
        }

        $routesPath = base_path('routes/web.php');
        $routesContents = file_get_contents($routesPath);

        foreach ($pages as $path => $meta) {
            // Never remove the homepage or contact page — core pages every site needs.
            if (in_array($meta['route_check'], ["'pages::home'", "'pages::contact'"])) {
                continue;
            }

            if (! file_exists($path)) {
                continue;
            }

            if (md5_file($path) !== $meta['hash']) {
                continue;
            }

            unlink($path);

            if (! empty($meta['route_check'])) {
                $routesContents = preg_replace(
                    '/^\s*Route::livewire\([^)]*'.preg_quote($meta['route_check'], '/').'[^)]*\)[^;]*;\n?/m',
                    '',
                    $routesContents
                );
            }
        }

        file_put_contents($routesPath, $routesContents);
        Setting::set('seeded_client_pages', []);
    }

    public function seedDemoData(): void
    {
        Setting::set('seeding_status', 'running');

        $categories = array_keys(array_filter($this->seedWith));

        defer(function () use ($categories) {
            $job = new SeedDemoDataJob($categories);
            try {
                $job->handle();
            } catch (\Throwable $e) {
                $job->failed($e);
            }
        });

        $this->dispatch('notify', message: 'Seeding started — this may take a minute.');
    }

    public function syncDesignLibrary(): void
    {
        IndexDesignLibraryJob::dispatchSync();

        $this->dispatch('notify', message: 'Design Library synced successfully.');
    }

    public string $artisanCommand = '';

    public string $artisanOutput = '';

    /** @var list<string> */
    private array $blockedCommands = [
        'migrate:fresh',
        'migrate:reset',
        'db:wipe',
        'key:generate',
        'down',
    ];

    public function runArtisan(): void
    {
        $input = trim(preg_replace('/^php\s+artisan\s+/i', '', $this->artisanCommand));

        if ($input === '') {
            $this->dispatch('notify', message: 'Please enter a command.');

            return;
        }

        preg_match_all('/[^\s"\']+|"[^"]*"|\'[^\']*\'/', $input, $matches);
        $tokens = array_map(fn ($t) => trim($t, '"\''), $matches[0]);
        $commandName = array_shift($tokens);

        if (in_array($commandName, $this->blockedCommands)) {
            $this->artisanOutput = "Command '{$commandName}' is not permitted.";

            return;
        }

        $parameters = [];
        foreach ($tokens as $token) {
            if (str_starts_with($token, '--')) {
                [$key, $value] = array_pad(explode('=', $token, 2), 2, true);
                $parameters[$key] = $value;
            } else {
                $parameters[] = $token;
            }
        }

        try {
            $exitCode = Artisan::call($commandName, $parameters);
            $output = trim(Artisan::output());
            $this->artisanOutput = ($output !== '' ? $output : '(No output)')."\n\nExit code: {$exitCode}";
        } catch (\Throwable $e) {
            $this->artisanOutput = 'Error: '.$e->getMessage();
        }
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

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <flux:heading>Application Cache</flux:heading>
                        <flux:text class="mt-1">Clear all compiled application caches — config, routes, views, and events. Run this after deploying changes or if the app is serving stale config.</flux:text>
                    </div>
                    <flux:button wire:click="optimizeClear" variant="outline" class="shrink-0">
                        Clear App Cache
                    </flux:button>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6" {{ $this->seedingStatus === 'running' ? 'wire:poll.3s' : '' }}>
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1 min-w-0">
                        <flux:heading>Seed Demo Data</flux:heading>
                        <flux:text class="mt-1">Populate the site with sample content. Safe to run multiple times — nothing will be duplicated.</flux:text>
                        <div class="mt-3 grid grid-cols-2 gap-x-8 gap-y-2">
                            <flux:checkbox wire:model="seedWith.blog" label="Blog posts &amp; categories" />
                            <flux:checkbox wire:model="seedWith.events" label="Events" />
                            <flux:checkbox wire:model="seedWith.locations" label="Locations" />
                            <flux:checkbox wire:model="seedWith.content_types" label="Content types" />
                            <flux:checkbox wire:model="seedWith.forms" label="Demo forms" />
                            <flux:checkbox wire:model="seedWith.navigation" label="Navigation items" />
                        </div>
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
                    <div class="flex-1 min-w-0">
                        <flux:heading>Delete Seeded Demo Data</flux:heading>
                        <flux:text class="mt-1">Remove seeded demo content. Content you have added manually will not be affected.</flux:text>
                        <div class="mt-3 grid grid-cols-2 gap-x-8 gap-y-2">
                            <flux:checkbox wire:model="deleteWith.blog" label="Blog posts &amp; categories" />
                            <flux:checkbox wire:model="deleteWith.events" label="Events" />
                            <flux:checkbox wire:model="deleteWith.locations" label="Locations" />
                            <flux:checkbox wire:model="deleteWith.content_types" label="Content types" />
                            <flux:checkbox wire:model="deleteWith.forms" label="Demo forms" />
                            <flux:checkbox wire:model="deleteWith.navigation" label="Navigation items" />
                            <flux:checkbox wire:model="deleteWith.pages" label="Seeded pages" />
                        </div>
                    </div>
                    <flux:button
                        wire:click="deleteSeededDemoData"
                        wire:confirm="This will permanently delete the selected seeded demo data. Are you sure?"
                        variant="outline"
                        class="shrink-0"
                    >
                        Delete Demo Data
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
                            <flux:text class="mt-2 text-sm text-amber-600 dark:text-amber-400">Update in progress — this may take a minute... <button wire:click="resetUpdateStatus" class="underline">Reset</button></flux:text>
                        @elseif ($this->updateStatus === 'complete')
                            <flux:text class="mt-2 text-sm text-green-600 dark:text-green-500">Update completed successfully.</flux:text>
                        @elseif ($this->updateStatus === 'failed')
                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">Update failed. See the log below for details. <button wire:click="resetUpdateStatus" class="underline">Dismiss</button></flux:text>
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
                <flux:heading>Artisan Console</flux:heading>
                <flux:text class="mt-1">Run an Artisan command on the server. Destructive commands (migrate:fresh, db:wipe, key:generate) are blocked.</flux:text>
                <div class="mt-4 flex gap-2">
                    <flux:input
                        wire:model="artisanCommand"
                        wire:keydown.enter="runArtisan"
                        placeholder="e.g. migrate --force"
                        class="font-mono"
                    />
                    <flux:button wire:click="runArtisan" variant="outline" class="shrink-0">Run</flux:button>
                </div>
                @if ($artisanOutput)
                    <pre class="mt-3 text-xs bg-zinc-100 dark:bg-zinc-800 rounded p-3 overflow-x-auto whitespace-pre-wrap break-all">{{ $artisanOutput }}</pre>
                @endif
            </div>
        </div>
    </flux:main>
</div>
