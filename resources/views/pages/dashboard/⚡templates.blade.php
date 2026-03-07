<?php

use App\Models\DesignRow;
use App\Models\SharedRow;
use App\Support\LayoutService;
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
        return config('layout.active_header');
    }

    #[Computed]
    public function activeFooter(): ?string
    {
        return config('layout.active_footer');
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
     * Find the editor URL for a shared row by locating a page file that contains it.
     */
    public function sharedRowEditorUrl(string $slug): ?string
    {
        $pagesDir = resource_path('views/pages');
        $files = array_merge(
            glob($pagesDir.'/⚡*.blade.php') ?: [],
            glob($pagesDir.'/***/⚡*.blade.php') ?: [],
        );

        foreach ($files as $file) {
            if (str_contains((string) file_get_contents($file), "ROW:start:{$slug}")) {
                $relativePath = str_replace(resource_path('views').'/', '', $file);

                return route('dashboard.design-library.editor').'?file='.urlencode($relativePath);
            }
        }

        return null;
    }

    public function restoreHeaderPartial(): void
    {
        $name = config('layout.active_header');

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
        $name = config('layout.active_footer');

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
        if (! config('layout.active_header')) {
            return null;
        }

        if (! file_exists(resource_path('views/layouts/partials/header.blade.php'))) {
            return null;
        }

        return route('dashboard.design-library.editor').'?file='.urlencode('layouts/partials/header.blade.php');
    }

    public function footerEditorUrl(): ?string
    {
        if (! config('layout.active_footer')) {
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
                            @php $editUrl = $this->sharedRowEditorUrl($sharedRow->slug); @endphp
                            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $sharedRow->name }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-mono">{{ $sharedRow->slug }}</p>
                                </div>
                                @if($editUrl)
                                    <flux:button :href="$editUrl" variant="outline" size="sm" icon="pencil-square" wire:navigate>
                                        Edit
                                    </flux:button>
                                @else
                                    <flux:button variant="outline" size="sm" disabled>
                                        No page found
                                    </flux:button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </flux:main>
</div>
