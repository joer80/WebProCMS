<?php

use App\Models\DesignRow;
use App\Models\SharedRow;
use App\Support\LayoutService;
use App\Support\VoltFileService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Templates')] class extends Component {
    #[Url]
    public string $tab = 'header';

    public string $bodyClasses = '';

    public string $phpTop = '';

    public string $pendingDeleteSlug = '';

    public function mount(): void
    {
        $config = (new LayoutService)->getConfig();
        $this->bodyClasses = $config['body_classes'] ?: 'bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] flex min-h-screen flex-col antialiased';
        $this->phpTop = $config['php_top'];
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    #[Computed]
    public function activeHeader(): ?string
    {
        return \App\Models\Setting::get('layout.active_header', '') ?: null;
    }

    #[Computed]
    public function activeFooter(): ?string
    {
        return \App\Models\Setting::get('layout.active_footer', '') ?: null;
    }

    #[Computed]
    public function availableHeaders(): \Illuminate\Support\Collection
    {
        return (new LayoutService)->getAvailableHeaders();
    }

    #[Computed]
    public function availableFooters(): \Illuminate\Support\Collection
    {
        return (new LayoutService)->getAvailableFooters();
    }

    #[Computed]
    public function sharedRows(): \Illuminate\Support\Collection
    {
        return SharedRow::query()->orderBy('name')->get();
    }

    public function activateHeader(int $rowId): void
    {
        $row = DesignRow::query()->findOrFail($rowId);
        (new LayoutService)->activateHeader($row);

        unset($this->activeHeader, $this->availableHeaders);

        $this->dispatch('notify', message: 'Header activated. Refresh the public site to see it.');
    }

    public function deactivateHeader(): void
    {
        (new LayoutService)->deactivateHeader();

        unset($this->activeHeader);

        $this->dispatch('notify', message: 'Header deactivated. Built-in header restored.');
    }

    public function activateFooter(int $rowId): void
    {
        $row = DesignRow::query()->findOrFail($rowId);
        (new LayoutService)->activateFooter($row);

        unset($this->activeFooter, $this->availableFooters);

        $this->dispatch('notify', message: 'Footer activated. Refresh the public site to see it.');
    }

    public function deactivateFooter(): void
    {
        (new LayoutService)->deactivateFooter();

        unset($this->activeFooter);

        $this->dispatch('notify', message: 'Footer deactivated. Built-in footer restored.');
    }

    public function saveLayoutSettings(): void
    {
        (new LayoutService)->writeConfig([
            'body_classes' => $this->bodyClasses,
            'php_top'      => $this->phpTop,
        ]);

        $this->dispatch('notify', message: 'Layout settings saved.');
    }

    /**
     * Scan all page files once and return a map of shared row slug → pages using it.
     *
     * @return array<string, list<array{name: string, url: string}>>
     */
    #[Computed]
    public function sharedRowPageMap(): array
    {
        $pagesDir = resource_path('views/pages');
        $files = array_merge(
            glob($pagesDir.'/⚡*.blade.php') ?: [],
            glob($pagesDir.'/***/⚡*.blade.php') ?: [],
        );

        $map = [];

        foreach ($files as $file) {
            $contents = (string) file_get_contents($file);
            $relativePath = str_replace(resource_path('views').'/', '', $file);
            $editUrl = route('dashboard.design-library.editor').'?file='.urlencode($relativePath);

            // Derive a human-readable page name from the file path.
            $name = preg_replace('#^pages/#', '', $relativePath);
            $name = str_replace(['.blade.php', '⚡', '-', '_'], ['', '', ' ', ' '], $name);
            $name = implode(' / ', array_map('ucwords', array_filter(explode('/', $name))));

            foreach ($this->sharedRows as $sharedRow) {
                if (str_contains($contents, "ROW:start:{$sharedRow->slug}")) {
                    $map[$sharedRow->slug][] = ['name' => $name, 'url' => $editUrl];
                }
            }
        }

        return $map;
    }

    /**
     * Create a wrapper file for an orphaned shared row and open it in the editor.
     */
    public function openSharedRowEditor(string $slug): void
    {
        $sharedRow = SharedRow::query()->where('slug', $slug)->firstOrFail();
        $filename = str_replace(':', '-', $slug);
        $wrapperDir = resource_path('views/shared-row-wrappers');

        if (! is_dir($wrapperDir)) {
            mkdir($wrapperDir, 0755, true);
        }

        $content = (new VoltFileService)->buildFileContent('<?php ' . '?' . '>', [[
            'slug' => $slug,
            'name' => $sharedRow->name,
            'blade' => "@include('shared-rows.{$filename}')",
            'shared' => true,
            'hidden' => false,
        ]]);

        file_put_contents("{$wrapperDir}/{$filename}.blade.php", $content);

        $this->redirect(
            route('dashboard.design-library.editor').'?file='.urlencode("shared-row-wrappers/{$filename}.blade.php"),
            navigate: true
        );
    }

    public function confirmDeleteSharedRow(string $slug): void
    {
        $this->pendingDeleteSlug = $slug;
        $this->dispatch('open-modal', name: 'confirm-delete-shared-row');
    }

    public function deleteSharedRow(): void
    {
        $slug = $this->pendingDeleteSlug;

        if (! $slug) {
            return;
        }

        $service = new VoltFileService;
        $pagesDir = resource_path('views/pages');
        $files = array_merge(
            glob($pagesDir.'/⚡*.blade.php') ?: [],
            glob($pagesDir.'/***/⚡*.blade.php') ?: [],
        );

        foreach ($files as $file) {
            $contents = (string) file_get_contents($file);

            if (! str_contains($contents, "ROW:start:{$slug}")) {
                continue;
            }

            $parsed = $service->parseFile($file);
            $parsed['rows'] = array_values(array_filter($parsed['rows'], fn (array $r) => $r['slug'] !== $slug));
            $service->writeFile($file, $service->buildFileContent($parsed['phpSection'], $parsed['rows']));
        }

        // Also remove the wrapper file if one was created.
        $filename = str_replace(':', '-', $slug);
        $wrapperFile = resource_path('views/shared-row-wrappers/'.$filename.'.blade.php');

        if (file_exists($wrapperFile)) {
            unlink($wrapperFile);
        }

        $service->deleteSharedRow($slug);

        $this->pendingDeleteSlug = '';
        unset($this->sharedRows, $this->sharedRowPageMap);
        $this->dispatch('notify', message: 'Shared row deleted.');
    }

    public function restoreHeaderPartial(): void
    {
        $name = \App\Models\Setting::get('layout.active_header', '') ?: null;

        if (! $name) {
            return;
        }

        $row = DesignRow::query()
            ->where('source_file', 'like', "%/{$name}.blade.php")
            ->first();

        if ($row) {
            (new LayoutService)->activateHeader($row);
            unset($this->activeHeader, $this->availableHeaders);
            $this->dispatch('notify', message: 'Header partial restored.');
        } else {
            (new LayoutService)->deactivateHeader();
            unset($this->activeHeader);
            $this->dispatch('notify', message: 'Header template not found in library — deactivated.');
        }
    }

    public function restoreFooterPartial(): void
    {
        $name = \App\Models\Setting::get('layout.active_footer', '') ?: null;

        if (! $name) {
            return;
        }

        $row = DesignRow::query()
            ->where('source_file', 'like', "%/{$name}.blade.php")
            ->first();

        if ($row) {
            (new LayoutService)->activateFooter($row);
            unset($this->activeFooter, $this->availableFooters);
            $this->dispatch('notify', message: 'Footer partial restored.');
        } else {
            (new LayoutService)->deactivateFooter();
            unset($this->activeFooter);
            $this->dispatch('notify', message: 'Footer template not found in library — deactivated.');
        }
    }

    public function headerEditorUrl(): ?string
    {
        if (! (\App\Models\Setting::get('layout.active_header', '') ?: null)) {
            return null;
        }

        if (! file_exists(resource_path('views/layouts/partials/header.blade.php'))) {
            return null;
        }

        return route('dashboard.design-library.editor').'?file='.urlencode('layouts/partials/header.blade.php');
    }

    public function footerEditorUrl(): ?string
    {
        if (! (\App\Models\Setting::get('layout.active_footer', '') ?: null)) {
            return null;
        }

        if (! file_exists(resource_path('views/layouts/partials/footer.blade.php'))) {
            return null;
        }

        return route('dashboard.design-library.editor').'?file='.urlencode('layouts/partials/footer.blade.php');
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Templates</flux:heading>
            <flux:text class="mt-1">Control the global header, footer, layout settings, and shared rows for your site.</flux:text>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 mb-6 border-b border-zinc-200 dark:border-zinc-700">
            @foreach ([
                'header'      => 'Header',
                'footer'      => 'Footer',
                'layout'      => 'Layout',
                'shared-rows' => 'Shared Rows',
            ] as $tabKey => $tabLabel)
                <button
                    wire:click="setTab('{{ $tabKey }}')"
                    class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px {{ $tab === $tabKey ? 'border-primary text-primary' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                >
                    {{ $tabLabel }}
                </button>
            @endforeach
        </div>

        {{-- Header tab --}}
        @if($tab === 'header')
            <div class="max-w-4xl space-y-6">
                {{-- Active header status --}}
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <flux:heading>Active Header</flux:heading>
                            @if($this->activeHeader)
                                <flux:text class="mt-1">
                                    Using <strong class="text-zinc-900 dark:text-zinc-100">{{ $this->activeHeader }}</strong>.
                                    Edit its content, nav menu, and classes in the editor.
                                </flux:text>
                            @else
                                <flux:text class="mt-1">Using the built-in default header. Select a template below to activate a design library header.</flux:text>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            @if($this->activeHeader)
                                @php
                                    $headerEditorUrl = $this->headerEditorUrl();
                                    $headerFileMissing = ! file_exists(resource_path('views/layouts/partials/header.blade.php'));
                                @endphp
                                @if($headerFileMissing)
                                    <flux:button wire:click="restoreHeaderPartial" variant="outline" icon="arrow-path">
                                        Restore Partial
                                    </flux:button>
                                @elseif($headerEditorUrl)
                                    <flux:button :href="$headerEditorUrl" variant="outline" icon="pencil-square" wire:navigate>
                                        Edit Header
                                    </flux:button>
                                @endif
                                <flux:button
                                    wire:click="deactivateHeader"
                                    wire:confirm="Revert to the built-in header?"
                                    variant="ghost"
                                    icon="x-mark"
                                >
                                    Deactivate
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Available headers grid --}}
                <div>
                    <flux:heading class="mb-4">Available Header Templates</flux:heading>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($this->availableHeaders as $header)
                            @php
                                $service = new \App\Support\LayoutService;
                                $isActive = $this->activeHeader === $service->templateName($header);
                            @endphp
                            <div class="rounded-lg border {{ $isActive ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-zinc-200 dark:border-zinc-700' }} p-4 flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                        {{ $header->name }}
                                        @if($isActive)
                                            <span class="text-xs font-medium px-1.5 py-0.5 rounded-full bg-primary/10 text-primary">Active</span>
                                        @endif
                                    </p>
                                    @if($header->description)
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $header->description }}</p>
                                    @endif
                                </div>
                                @if(! $isActive)
                                    <flux:button
                                        wire:click="activateHeader({{ $header->id }})"
                                        wire:confirm="Activate '{{ $header->name }}' as your site header? This will replace the current header."
                                        variant="outline"
                                        size="sm"
                                        class="shrink-0"
                                    >
                                        Activate
                                    </flux:button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <flux:callout variant="info">
                    <flux:callout.text>
                        After activating a header, click <strong>Edit Header</strong> to customise nav menus, logo text, CTA links, and classes.
                        Each nav section in the header has a <strong>Menu</strong> field — set it to the slug of any menu from your
                        <a href="{{ route('dashboard.menus') }}" class="underline hover:text-primary" wire:navigate>Menus</a> page.
                    </flux:callout.text>
                </flux:callout>
            </div>
        @endif

        {{-- Footer tab --}}
        @if($tab === 'footer')
            <div class="max-w-4xl space-y-6">
                {{-- Active footer status --}}
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <flux:heading>Active Footer</flux:heading>
                            @if($this->activeFooter)
                                <flux:text class="mt-1">
                                    Using <strong class="text-zinc-900 dark:text-zinc-100">{{ $this->activeFooter }}</strong>.
                                    Edit its content and classes in the editor.
                                </flux:text>
                            @else
                                <flux:text class="mt-1">Using the built-in default footer. Select a template below to activate a design library footer.</flux:text>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            @if($this->activeFooter)
                                @php
                                    $footerEditorUrl = $this->footerEditorUrl();
                                    $footerFileMissing = ! file_exists(resource_path('views/layouts/partials/footer.blade.php'));
                                @endphp
                                @if($footerFileMissing)
                                    <flux:button wire:click="restoreFooterPartial" variant="outline" icon="arrow-path">
                                        Restore Partial
                                    </flux:button>
                                @elseif($footerEditorUrl)
                                    <flux:button :href="$footerEditorUrl" variant="outline" icon="pencil-square" wire:navigate>
                                        Edit Footer
                                    </flux:button>
                                @endif
                                <flux:button
                                    wire:click="deactivateFooter"
                                    wire:confirm="Revert to the built-in footer?"
                                    variant="ghost"
                                    icon="x-mark"
                                >
                                    Deactivate
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Available footers grid --}}
                <div>
                    <flux:heading class="mb-4">Available Footer Templates</flux:heading>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($this->availableFooters as $footer)
                            @php
                                $service = new \App\Support\LayoutService;
                                $isActive = $this->activeFooter === $service->templateName($footer);
                            @endphp
                            <div class="rounded-lg border {{ $isActive ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-zinc-200 dark:border-zinc-700' }} p-4 flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                        {{ $footer->name }}
                                        @if($isActive)
                                            <span class="text-xs font-medium px-1.5 py-0.5 rounded-full bg-primary/10 text-primary">Active</span>
                                        @endif
                                    </p>
                                    @if($footer->description)
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $footer->description }}</p>
                                    @endif
                                </div>
                                @if(! $isActive)
                                    <flux:button
                                        wire:click="activateFooter({{ $footer->id }})"
                                        wire:confirm="Activate '{{ $footer->name }}' as your site footer? This will replace the current footer."
                                        variant="outline"
                                        size="sm"
                                        class="shrink-0"
                                    >
                                        Activate
                                    </flux:button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Layout tab --}}
        @if($tab === 'layout')
            <div class="max-w-2xl space-y-6">
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 space-y-5">
                    <flux:field>
                        <flux:label>Body Classes</flux:label>
                        <flux:textarea wire:model="bodyClasses" rows="3" class="font-mono text-xs" />
                        <flux:description>These classes replace all <code>&lt;body&gt;</code> tag classes on every public page.</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>PHP Top Code</flux:label>
                        <flux:textarea wire:model="phpTop" rows="6" class="font-mono text-xs"
                            placeholder="// PHP code that runs before the DOCTYPE on every public page&#10;// Example: header('X-Frame-Options: DENY');" />
                        <flux:description>Runs before <code>&lt;!DOCTYPE html&gt;</code> on every public page. Use with care — errors here will break the site.</flux:description>
                    </flux:field>

                    <div class="pt-1">
                        <flux:button wire:click="saveLayoutSettings" variant="primary">Save Layout Settings</flux:button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Shared Rows tab --}}
        @if($tab === 'shared-rows')
            <div class="max-w-3xl">
                @if($this->sharedRows->isEmpty())
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-8 text-center">
                        <flux:heading class="mb-2">No shared rows yet</flux:heading>
                        <flux:text>Shared rows are created in the page editor by right-clicking a row and selecting "Make Shared". They appear here once created.</flux:text>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($this->sharedRows as $sharedRow)
                            @php $pages = $this->sharedRowPageMap[$sharedRow->slug] ?? []; @endphp
                            <div x-data="{ open: false }" class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                                <div class="p-4 flex items-center justify-between gap-4">
                                    <div class="min-w-0 flex items-center gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $sharedRow->name }}</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-mono">{{ $sharedRow->slug }}</p>
                                        </div>
                                        @if(count($pages) > 0)
                                            <button type="button" @click="open = !open"
                                                class="shrink-0 text-xs font-medium px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600 transition-colors">
                                                {{ count($pages) }} {{ Str::plural('page', count($pages)) }}
                                            </button>
                                        @else
                                            <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-400 dark:text-zinc-500">
                                                0 pages
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if(count($pages) > 0)
                                            <flux:button :href="$pages[0]['url']" variant="outline" size="sm" icon="pencil-square" wire:navigate>
                                                Edit
                                            </flux:button>
                                        @else
                                            <flux:button variant="outline" size="sm" icon="pencil-square" wire:click="openSharedRowEditor('{{ $sharedRow->slug }}')">
                                                Edit
                                            </flux:button>
                                        @endif
                                        <flux:button variant="outline" size="sm" icon="trash" icon-variant="outline"
                                            wire:click="confirmDeleteSharedRow('{{ $sharedRow->slug }}')"
                                            class="text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300" />
                                    </div>
                                </div>
                                @if(count($pages) > 0)
                                    <div x-show="open" x-collapse class="border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 px-4 py-2 space-y-1">
                                        @foreach($pages as $page)
                                            <a href="{{ $page['url'] }}" wire:navigate
                                                class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 hover:text-primary dark:hover:text-primary py-1 transition-colors">
                                                <flux:icon name="document-text" class="size-3.5 shrink-0 text-zinc-400" />
                                                {{ $page['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </flux:main>

    <flux:modal name="confirm-delete-shared-row" class="w-full max-w-sm">
        <flux:heading size="lg">Delete shared row?</flux:heading>
        @if(count($this->sharedRowPageMap[$pendingDeleteSlug] ?? []) > 0)
            <flux:text class="mt-2">
                This row is used on {{ count($this->sharedRowPageMap[$pendingDeleteSlug]) }} {{ Str::plural('page', count($this->sharedRowPageMap[$pendingDeleteSlug])) }}. It will be removed from all of them and cannot be undone.
            </flux:text>
            <ul class="mt-3 space-y-1">
                @foreach($this->sharedRowPageMap[$pendingDeleteSlug] ?? [] as $page)
                    <li class="text-sm text-zinc-600 dark:text-zinc-400 flex items-center gap-2">
                        <flux:icon name="document-text" class="size-3.5 shrink-0 text-zinc-400" />
                        {{ $page['name'] }}
                    </li>
                @endforeach
            </ul>
        @else
            <flux:text class="mt-2">This shared row is not used on any pages. This cannot be undone.</flux:text>
        @endif
        <div class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:modal.close>
                <flux:button variant="danger" wire:click="deleteSharedRow">Delete</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>
</div>
