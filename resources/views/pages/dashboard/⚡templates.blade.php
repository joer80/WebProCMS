<?php

use App\Models\DesignRow;
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

    public bool $showConfirmModal = false;

    public string $confirmAction = '';

    public int $confirmId = 0;

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

    public function confirmActivateHeader(int $id): void
    {
        $this->confirmAction = 'activateHeader';
        $this->confirmId = $id;
        $this->showConfirmModal = true;
    }

    public function confirmDeactivateHeader(): void
    {
        $this->confirmAction = 'deactivateHeader';
        $this->showConfirmModal = true;
    }

    public function confirmActivateFooter(int $id): void
    {
        $this->confirmAction = 'activateFooter';
        $this->confirmId = $id;
        $this->showConfirmModal = true;
    }

    public function confirmDeactivateFooter(): void
    {
        $this->confirmAction = 'deactivateFooter';
        $this->showConfirmModal = true;
    }

    public function executeConfirm(): void
    {
        match ($this->confirmAction) {
            'activateHeader' => $this->activateHeader($this->confirmId),
            'deactivateHeader' => $this->deactivateHeader(),
            'activateFooter' => $this->activateFooter($this->confirmId),
            'deactivateFooter' => $this->deactivateFooter(),
            default => null,
        };

        $this->confirmAction = '';
        $this->confirmId = 0;
        $this->showConfirmModal = false;
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

<div x-data="dlPreview()">
    <div wire:ignore>
        <template x-teleport="body">
            <div
                x-show="showPreview"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @keydown.escape.window="closePreview()"
                class="fixed inset-0 z-[9999] flex flex-col"
            >
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closePreview()"></div>
                <div class="relative z-10 flex flex-col w-full h-full max-w-screen-xl mx-auto shadow-2xl">
                    <div class="flex items-center justify-between px-6 py-3 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                        <span class="font-semibold text-zinc-900 dark:text-white text-sm truncate pr-4" x-text="previewName"></span>
                        <div class="flex items-center gap-2 shrink-0">
                            <a :href="previewUrl" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                            >Open in Tab</a>
                            <button @click="closePreview()"
                                class="p-1.5 text-zinc-500 hover:text-zinc-900 dark:hover:text-white rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                                aria-label="Close preview"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-hidden bg-white dark:bg-zinc-950">
                        <iframe :src="showPreview ? previewUrl : 'about:blank'" class="w-full h-full border-0" scrolling="yes"></iframe>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Templates</flux:heading>
            <flux:text class="mt-1">Control the global header, footer, and layout settings for your site.</flux:text>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 mb-6 border-b border-zinc-200 dark:border-zinc-700">
            @foreach ([
                'header' => 'Header',
                'footer' => 'Footer',
                'layout' => 'Layout',
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
            <div class="space-y-6">
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
                                    wire:click="confirmDeactivateHeader"
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
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($this->availableHeaders as $header)
                            @php
                                $service = new \App\Support\LayoutService;
                                $isActive = $this->activeHeader === $service->templateName($header);
                                $headerPreviewUrl = route('dashboard.design-library.preview', ['type' => 'row', 'id' => $header->id]);
                            @endphp
                            <div class="group rounded-xl border {{ $isActive ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-zinc-200 dark:border-zinc-700 hover:border-primary/40' }} overflow-hidden transition-colors flex flex-col">
                                {{-- Preview thumbnail --}}
                                <div
                                    class="relative overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border-b {{ $isActive ? 'border-primary/20' : 'border-zinc-200 dark:border-zinc-700' }} cursor-pointer"
                                    style="height:9rem"
                                    x-data="{ scale: 1 }"
                                    x-init="$nextTick(() => scale = ($el.offsetWidth - 32) / 1280)"
                                    @click="openPreview('{{ $headerPreviewUrl }}', '{{ addslashes($header->name) }}')"
                                >
                                    <div class="absolute inset-0 flex items-center justify-center animate-pulse pointer-events-none">
                                        <flux:icon name="photo" class="size-8 text-zinc-300 dark:text-zinc-600" />
                                    </div>
                                    <iframe
                                        src="{{ $headerPreviewUrl }}"
                                        class="absolute border-0"
                                        :style="'top:16px;left:16px;width:1280px;height:800px;transform:scale('+scale+');transform-origin:top left;pointer-events:none;'"
                                        loading="lazy"
                                        scrolling="no"
                                        tabindex="-1"
                                        aria-hidden="true"
                                        onload="this.previousElementSibling.style.display='none'"
                                    ></iframe>
                                </div>
                                {{-- Card body --}}
                                <div class="p-4 flex items-center justify-between gap-4">
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
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ $headerPreviewUrl }}" target="_blank" rel="noopener noreferrer">
                                            <flux:button variant="ghost" size="sm" icon="eye" />
                                        </a>
                                        @if($isActive)
                                            @php $headerCardEditorUrl = $this->headerEditorUrl(); @endphp
                                            @if($headerCardEditorUrl)
                                                <flux:button :href="$headerCardEditorUrl" variant="outline" size="sm" icon="pencil-square" wire:navigate>Edit</flux:button>
                                            @endif
                                            <flux:button wire:click="confirmDeactivateHeader" variant="ghost" size="sm" icon="x-mark">Deactivate</flux:button>
                                        @else
                                            <flux:button wire:click="confirmActivateHeader({{ $header->id }})" variant="outline" size="sm">Activate</flux:button>
                                        @endif
                                    </div>
                                </div>
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
            <div class="space-y-6">
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
                                    wire:click="confirmDeactivateFooter"
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
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($this->availableFooters as $footer)
                            @php
                                $service = new \App\Support\LayoutService;
                                $isActive = $this->activeFooter === $service->templateName($footer);
                                $footerPreviewUrl = route('dashboard.design-library.preview', ['type' => 'row', 'id' => $footer->id]);
                            @endphp
                            <div class="group rounded-xl border {{ $isActive ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-zinc-200 dark:border-zinc-700 hover:border-primary/40' }} overflow-hidden transition-colors flex flex-col">
                                {{-- Preview thumbnail --}}
                                <div
                                    class="relative overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border-b {{ $isActive ? 'border-primary/20' : 'border-zinc-200 dark:border-zinc-700' }} cursor-pointer"
                                    style="height:18rem"
                                    x-data="{ scale: 1 }"
                                    x-init="$nextTick(() => scale = ($el.offsetWidth - 32) / 1280)"
                                    @click="openPreview('{{ $footerPreviewUrl }}', '{{ addslashes($footer->name) }}')"
                                >
                                    <div class="absolute inset-0 flex items-center justify-center animate-pulse pointer-events-none">
                                        <flux:icon name="photo" class="size-8 text-zinc-300 dark:text-zinc-600" />
                                    </div>
                                    <iframe
                                        src="{{ $footerPreviewUrl }}"
                                        class="absolute border-0"
                                        :style="'top:16px;left:16px;width:1280px;height:800px;transform:scale('+scale+');transform-origin:top left;pointer-events:none;'"
                                        loading="lazy"
                                        scrolling="no"
                                        tabindex="-1"
                                        aria-hidden="true"
                                        onload="this.previousElementSibling.style.display='none'"
                                    ></iframe>
                                </div>
                                {{-- Card body --}}
                                <div class="p-4 flex items-center justify-between gap-4">
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
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ $footerPreviewUrl }}" target="_blank" rel="noopener noreferrer">
                                            <flux:button variant="ghost" size="sm" icon="eye" />
                                        </a>
                                        @if($isActive)
                                            @php $footerCardEditorUrl = $this->footerEditorUrl(); @endphp
                                            @if($footerCardEditorUrl)
                                                <flux:button :href="$footerCardEditorUrl" variant="outline" size="sm" icon="pencil-square" wire:navigate>Edit</flux:button>
                                            @endif
                                            <flux:button wire:click="confirmDeactivateFooter" variant="ghost" size="sm" icon="x-mark">Deactivate</flux:button>
                                        @else
                                            <flux:button wire:click="confirmActivateFooter({{ $footer->id }})" variant="outline" size="sm">Activate</flux:button>
                                        @endif
                                    </div>
                                </div>
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

    </flux:main>

    <flux:modal wire:model="showConfirmModal" class="w-full max-w-sm">
        @php
            $confirmHeading = match($confirmAction) {
                'activateHeader', 'activateFooter' => 'Activate template?',
                'deactivateHeader' => 'Revert header?',
                'deactivateFooter' => 'Revert footer?',
                default => 'Are you sure?',
            };
            $confirmText = match($confirmAction) {
                'activateHeader' => 'This will replace the current header.',
                'deactivateHeader' => 'This will revert to the built-in default header.',
                'activateFooter' => 'This will replace the current footer.',
                'deactivateFooter' => 'This will revert to the built-in default footer.',
                default => 'This action cannot be undone.',
            };
        @endphp
        <flux:heading size="lg">{{ $confirmHeading }}</flux:heading>
        <flux:text class="mt-2">{{ $confirmText }}</flux:text>
        <div class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button variant="primary" wire:click="executeConfirm">Confirm</flux:button>
        </div>
    </flux:modal>

</div>
