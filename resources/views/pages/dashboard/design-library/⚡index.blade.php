<?php

use App\Enums\PageCategory;
use App\Enums\RowCategory;
use App\Jobs\IndexDesignLibraryJob;
use App\Models\DesignPage;
use App\Models\DesignRow;
use App\Support\DesignLibraryService;
use App\Support\VoltFileService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Design Library')] class extends Component {
    use WithPagination;

    #[Url]
    public string $tab = 'rows';

    #[Url]
    public string $category = '';

    public bool $showModal = false;
    public ?int $editingId = null;
    public bool $indexing = false;

    public function mount(): void
    {
        IndexDesignLibraryJob::dispatchSync();
        unset($this->rows, $this->pages, $this->allRows);
    }

    // Row form fields
    public string $formName = '';
    public string $formCategory = '';
    public string $formDescription = '';
    public string $formBladeCode = '';
    public string $formPhpCode = '';

    // Page form fields
    /** @var list<string> */
    public array $formRowNames = [];

    // Confirm delete
    public ?int $confirmingDelete = null;

    #[Computed]
    public function rows(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return DesignRow::query()
            ->when($this->category, fn ($q) => $q->where('category', $this->category))
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);
    }

    #[Computed]
    public function pages(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return DesignPage::query()
            ->when($this->category, fn ($q) => $q->where('website_category', $this->category))
            ->orderBy('website_category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20, pageName: 'pagesPage');
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, DesignRow> */
    #[Computed]
    public function allRows(): \Illuminate\Database\Eloquent\Collection
    {
        return DesignRow::query()
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    /** @return array<string, string> */
    #[Computed]
    public function rowCategories(): array
    {
        return collect(RowCategory::cases())
            ->mapWithKeys(fn (RowCategory $c) => [$c->value => $c->label()])
            ->all();
    }

    /** @return array<string, string> */
    #[Computed]
    public function pageCategories(): array
    {
        return collect(PageCategory::cases())
            ->mapWithKeys(fn (PageCategory $c) => [$c->value => $c->label()])
            ->all();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->category = '';
        $this->resetPage();
        $this->resetPage('pagesPage');
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
        $this->resetPage('pagesPage');
    }

    public function syncLibrary(): void
    {
        $this->indexing = true;
        IndexDesignLibraryJob::dispatchSync();
        $this->indexing = false;

        unset($this->rows, $this->pages, $this->allRows);

        $this->dispatch('notify', message: 'Design Library synced successfully.');
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetForm();
        $this->editingId = $id;

        if ($this->tab === 'rows') {
            $row = DesignRow::query()->findOrFail($id);
            $this->formName = $row->name;
            $this->formCategory = $row->category->value;
            $this->formDescription = $row->description ?? '';
            $this->formBladeCode = $row->bladeCodeFromFile();
            $this->formPhpCode = $row->phpCodeFromFile();
        } else {
            $page = DesignPage::query()->findOrFail($id);
            $this->formName = $page->name;
            $this->formCategory = $page->website_category->value;
            $this->formDescription = $page->description ?? '';
            $this->formRowNames = $page->row_names ?? [];
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->tab === 'rows') {
            $this->validate([
                'formName' => ['required', 'string', 'max:255'],
                'formCategory' => ['required', 'string'],
                'formBladeCode' => ['required', 'string'],
            ]);
        } else {
            $this->validate([
                'formName' => ['required', 'string', 'max:255'],
                'formCategory' => ['required', 'string'],
            ]);
        }

        $service = new DesignLibraryService;

        if ($this->tab === 'rows') {
            $dbData = [
                'name' => $this->formName,
                'description' => $this->formDescription ?: null,
                'category' => $this->formCategory,
            ];

            $fileData = array_merge($dbData, [
                'blade_code' => $this->formBladeCode,
                'php_code' => $this->formPhpCode ?: null,
            ]);

            if ($this->editingId) {
                $row = DesignRow::query()->findOrFail($this->editingId);
                $row->update($dbData);

                // Write back to source file if it exists
                if ($row->source_file) {
                    $fullPath = $service->fullPath($row->source_file);
                    if (file_exists($fullPath)) {
                        $service->writeTemplateFile($fullPath, array_merge($fileData, ['sort_order' => $row->sort_order]));
                    }
                }

                $message = 'Row updated.';
            } else {
                $slug = $this->formCategory.'-'.now()->format('YmdHis');
                $dbData['source_file'] = 'rows/'.$this->formCategory.'/'.$slug.'.blade.php';
                $dbData['sort_order'] = 0;
                $row = DesignRow::query()->create($dbData);

                if (app()->isLocal()) {
                    $service->writeTemplateFile($service->fullPath($row->source_file), array_merge($fileData, ['source_file' => $row->source_file, 'sort_order' => 0]));
                }

                $message = 'Row created.';
            }

            unset($this->rows);
        } else {
            $data = [
                'name' => $this->formName,
                'description' => $this->formDescription ?: null,
                'row_names' => array_values(array_filter($this->formRowNames)),
                'website_category' => $this->formCategory,
            ];

            if ($this->editingId) {
                $page = DesignPage::query()->findOrFail($this->editingId);
                $page->update($data);

                if ($page->source_file) {
                    $fullPath = $service->fullPath($page->source_file);
                    if (file_exists($fullPath)) {
                        $service->writePageFile($fullPath, array_merge($data, ['sort_order' => $page->sort_order]));
                    }
                }

                $message = 'Page bundle updated.';
            } else {
                $slug = $this->formCategory.'-'.now()->format('YmdHis');
                $data['source_file'] = 'pages/'.$this->formCategory.'/'.$slug.'.blade.php';
                $data['sort_order'] = 0;
                $page = DesignPage::query()->create($data);

                if (app()->isLocal()) {
                    $fullPath = $service->fullPath($page->source_file);
                    $service->writePageFile($fullPath, array_merge($data, ['sort_order' => $page->sort_order]));
                }

                $message = 'Page bundle created.';
            }

            unset($this->pages);
        }

        $this->showModal = false;
        $this->resetForm();

        $this->dispatch('notify', message: $message);
    }

    public function deleteItem(int $id): void
    {
        $service = new DesignLibraryService;

        if ($this->tab === 'rows') {
            $row = DesignRow::query()->findOrFail($id);

            if ($row->source_file) {
                $fullPath = $service->fullPath($row->source_file);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $row->delete();
            unset($this->rows);
        } else {
            $page = DesignPage::query()->findOrFail($id);

            if ($page->source_file) {
                $fullPath = $service->fullPath($page->source_file);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $page->delete();
            unset($this->pages);
        }

        $this->confirmingDelete = null;
        $this->dispatch('notify', message: 'Deleted.');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formName = '';
        $this->formCategory = '';
        $this->formDescription = '';
        $this->formBladeCode = '';
        $this->formPhpCode = '';
        $this->formRowNames = [];
    }

    // ── Menus tab ────────────────────────────────────────────────────────────

    public bool $showImportModal = false;

    public string $importTemplateType = '';

    public string $importMode = 'placeholder';

    /** @return array<string, mixed> */
    #[Computed]
    public function menuTemplates(): array
    {
        return config('menu-templates', []);
    }

    public function openImportModal(string $type): void
    {
        $this->importTemplateType = $type;
        $this->importMode = 'placeholder';
        $this->showImportModal = true;
    }

    public function importMenuTemplate(): void
    {
        $template = config("menu-templates.{$this->importTemplateType}");

        if (! $template) {
            return;
        }

        $currentConfig = config('navigation');
        $currentMenusBySlug = collect($currentConfig['menus'] ?? [])->keyBy('slug')->all();

        foreach ($template['menus'] ?? [] as $menu) {
            $processedItems = [];

            foreach ($menu['items'] ?? [] as $item) {
                if (! isset($item['route'])) {
                    $processedItems[] = $item;
                    continue;
                }

                if ($this->importMode === 'create' && ! str_contains($item['route'], '.')) {
                    $slug = $item['route'];
                    $pageFile = resource_path("views/pages/⚡{$slug}.blade.php");

                    if (! file_exists($pageFile)) {
                        (new VoltFileService)->createPage($slug, $item['label']);
                    }

                    $processedItems[] = $item;
                } elseif ($this->importMode === 'create') {
                    $routeExists = collect(app('router')->getRoutes()->getRoutesByName())->has($item['route']);
                    $processedItems[] = $routeExists
                        ? $item
                        : array_merge(array_diff_key($item, ['route' => '']), ['url' => '#']);
                } else {
                    $processedItems[] = array_merge(array_diff_key($item, ['route' => '']), ['url' => '#']);
                }
            }

            $menu['items'] = $processedItems;
            // Replace existing menu with same slug, or add new one
            $currentMenusBySlug[$menu['slug']] = $menu;
        }

        // Merge footer slugs from template (add any not already present)
        $currentFooterSlugs = $currentConfig['footer_slugs'] ?? [];

        foreach ($template['footer_slugs'] ?? [] as $slug) {
            if (! in_array($slug, $currentFooterSlugs, true)) {
                $currentFooterSlugs[] = $slug;
            }
        }

        $currentConfig['menus'] = array_values($currentMenusBySlug);
        $currentConfig['footer_slugs'] = array_values($currentFooterSlugs);
        $currentConfig['show_auth_links'] = $template['show_auth_links'] ?? $currentConfig['show_auth_links'];
        $currentConfig['show_account_in_footer'] = $template['show_account_in_footer'] ?? $currentConfig['show_account_in_footer'];

        $configPath = config_path('navigation.php');
        file_put_contents($configPath, $this->buildNavigationConfigFile($currentConfig));

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($configPath, true);
        }

        config(['navigation' => $currentConfig]);

        $this->showImportModal = false;

        $this->dispatch('notify', message: 'Navigation imported. Visit the Menus page to review your changes.');
    }

    /**
     * @param array{
     *     show_auth_links: bool,
     *     show_account_in_footer: bool,
     *     footer_slugs: array<int, string>,
     *     menus: array<int, array{slug: string, label: string, items: array<int, array<string, mixed>>}>
     * } $config
     */
    private function buildNavigationConfigFile(array $config): string
    {
        $showAuth = ($config['show_auth_links'] ?? false) ? 'true' : 'false';
        $showAccountInFooter = ($config['show_account_in_footer'] ?? true) ? 'true' : 'false';

        $footerSlugItems = array_map(
            fn (string $s): string => "'" . str_replace("'", "\\'", $s) . "'",
            $config['footer_slugs'] ?? [],
        );
        $footerSlugsLine = "    'footer_slugs' => [" . implode(', ', $footerSlugItems) . '],';

        $menuBlocks = [];

        foreach ($config['menus'] ?? [] as $menu) {
            $slug = str_replace("'", "\\'", $menu['slug']);
            $menuLabel = str_replace("'", "\\'", $menu['label']);

            $itemLines = array_map(
                fn (array $item): string => '            ' . $this->formatNavItemForConfig($item) . ',',
                $menu['items'] ?? [],
            );

            $block = [
                "        [",
                "            'slug' => '{$slug}',",
                "            'label' => '{$menuLabel}',",
                "            'items' => [",
                ...$itemLines,
                "            ],",
                "        ],",
            ];

            $menuBlocks = [...$menuBlocks, ...$block];
        }

        $lines = [
            "<?php\n",
            "/*",
            "|--------------------------------------------------------------------------",
            "| Navigation",
            "|--------------------------------------------------------------------------",
            "|",
            "| Defines the public navigation and footer links for the site.",
            "| Edit these menus via the dashboard at /dashboard/menus.",
            "|",
            "| Keys:",
            "|   show_auth_links         - whether login/register/dashboard appear in the nav",
            "|   show_account_in_footer  - whether an Account column appears in the footer",
            "|   footer_slugs            - slugs of menus rendered as footer columns (in order)",
            "|   menus                   - all menus; templates request them by slug",
            "|",
            "*/\n",
            "return [\n",
            "    'show_auth_links' => {$showAuth},",
            "    'show_account_in_footer' => {$showAccountInFooter},",
            $footerSlugsLine,
            "    'menus' => [",
            ...$menuBlocks,
            "    ],\n",
            "];\n",
        ];

        return implode("\n", $lines);
    }

    /** @param array<string, mixed> $item */
    private function formatNavItemForConfig(array $item): string
    {
        $parts = [];

        foreach ($item as $key => $value) {
            if (is_bool($value)) {
                $parts[] = "'{$key}' => " . ($value ? 'true' : 'false');
            } else {
                $escaped = str_replace("'", "\\'", (string) $value);
                $parts[] = "'{$key}' => '{$escaped}'";
            }
        }

        return '[' . implode(', ', $parts) . ']';
    }
}; ?>

<div>
    {{-- Edit / Create Modal --}}
    <flux:modal wire:model="showModal" class="w-full max-w-3xl">
        <flux:heading size="lg" class="mb-6">
            {{ $editingId ? __('Edit') : __('New') }} {{ $tab === 'rows' ? __('Row') : __('Page Bundle') }}
        </flux:heading>

        @if (! app()->isLocal())
            <flux:callout variant="warning" class="mb-6">
                <flux:callout.heading>Not in local environment</flux:callout.heading>
                <flux:callout.text>Changes will update the database and write to disk. Library files should be edited locally and committed to git.</flux:callout.text>
            </flux:callout>
        @endif

        <form wire:submit="save" class="space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <flux:input wire:model="formName" label="Name" required autofocus />

                <flux:select wire:model="formCategory" label="Category" required>
                    <flux:select.option value="">Select a category…</flux:select.option>
                    @foreach ($tab === 'rows' ? $this->rowCategories : $this->pageCategories as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <flux:input wire:model="formDescription" label="Description (optional)" />

            @if ($tab === 'rows')
                <flux:field>
                    <flux:label>Blade Code <span class="text-red-500">*</span></flux:label>
                    <flux:textarea wire:model="formBladeCode" rows="10" class="font-mono text-xs" placeholder="<section>...</section>" />
                    <flux:error name="formBladeCode" />
                </flux:field>

                <flux:field>
                    <flux:label>PHP Code to Inject (optional)</flux:label>
                    <flux:textarea wire:model="formPhpCode" rows="5" class="font-mono text-xs" placeholder="public string $heroTitle = '';" />
                    <flux:description>This code will be injected into the Volt component class when the row is inserted into a page.</flux:description>
                </flux:field>
            @else
                <flux:field>
                    <flux:label>Rows in this bundle</flux:label>
                    <select wire:model="formRowNames" multiple size="10"
                        class="block w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/50">
                        @foreach ($this->allRows->groupBy(fn ($r) => $r->category->label()) as $categoryLabel => $categoryRows)
                            <optgroup label="{{ $categoryLabel }}">
                                @foreach ($categoryRows as $row)
                                    @php $templateName = basename($row->source_file, '.blade.php'); @endphp
                                    <option value="{{ $templateName }}">{{ $row->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <flux:description>Hold Cmd/Ctrl to select multiple rows. Order matches selection order.</flux:description>
                </flux:field>
            @endif

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $editingId ? __('Update') : __('Create') }}</span>
                    <span wire:loading>{{ __('Saving…') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Import Menu Template Modal --}}
    <flux:modal wire:model="showImportModal" class="w-full max-w-lg">
        @php
            $importTemplate = $importTemplateType ? config("menu-templates.{$importTemplateType}") : null;
            $importTypeLabel = $importTemplateType ? collect(\App\Enums\PageCategory::cases())->firstWhere(fn ($c) => $c->value === $importTemplateType)?->label() ?? ucfirst($importTemplateType) : '';
        @endphp
        <flux:heading size="lg" class="mb-4">{{ __('Import :type Navigation', ['type' => $importTypeLabel]) }}</flux:heading>

        @if ($importTemplate)
            <div class="mb-5 space-y-3">
                @foreach ($importTemplate['menus'] ?? [] as $menu)
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
                        <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">{{ $menu['label'] }}</p>
                        <ul class="space-y-0.5">
                            @foreach (array_slice($menu['items'] ?? [], 0, 6) as $item)
                                <li class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5">
                                    <span class="size-1 rounded-full bg-zinc-400 shrink-0"></span>
                                    {{ $item['label'] }}
                                </li>
                            @endforeach
                            @if (count($menu['items'] ?? []) > 6)
                                <li class="text-xs text-zinc-400 dark:text-zinc-500 italic">+ {{ count($menu['items']) - 6 }} more…</li>
                            @endif
                        </ul>
                    </div>
                @endforeach
            </div>

            <flux:radio.group wire:model="importMode" label="{{ __('How should unregistered page links be handled?') }}" class="mb-6">
                <flux:radio value="placeholder" label="{{ __('Placeholder links (#)') }}" description="{{ __('All items use # as the URL. Edit them in Menus after importing.') }}" />
                <flux:radio value="create" label="{{ __('Create blank pages') }}" description="{{ __('Missing pages are created automatically and linked. Existing pages are reused.') }}" />
            </flux:radio.group>
        @endif

        <div class="flex items-center justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button wire:click="importMenuTemplate" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="importMenuTemplate">{{ __('Import') }}</span>
                <span wire:loading wire:target="importMenuTemplate">{{ __('Importing…') }}</span>
            </flux:button>
        </div>
    </flux:modal>

    <flux:main>
        {{-- Environment warning --}}
        @if (! app()->isLocal())
            <flux:callout variant="warning" class="mb-6">
                <flux:callout.heading>Production environment</flux:callout.heading>
                <flux:callout.text>Library file edits write directly to disk. Make changes locally and commit to git before deploying.</flux:callout.text>
            </flux:callout>
        @endif

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4 mb-8">
            <div>
                <flux:heading size="xl">{{ __('Design Library') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Browse and manage reusable row and page templates.') }}</flux:text>
            </div>
            <div class="flex items-center gap-3">
                <flux:button
                    wire:click="syncLibrary"
                    wire:loading.attr="disabled"
                    variant="outline"
                    icon="arrow-path"
                >
                    <span wire:loading.remove wire:target="syncLibrary">{{ __('Sync Library') }}</span>
                    <span wire:loading wire:target="syncLibrary">{{ __('Syncing…') }}</span>
                </flux:button>
                <flux:button href="{{ route('dashboard.design-library.editor') }}" variant="outline" icon="pencil-square" wire:navigate>
                    {{ __('Page Editor') }}
                </flux:button>
                @if ($tab !== 'menus')
                    <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                        {{ $tab === 'rows' ? __('Add Row') : __('Add Page') }}
                    </flux:button>
                @endif
            </div>
        </div>

        <div class="flex gap-8">
            {{-- Category sidebar --}}
            <aside class="hidden lg:block w-48 shrink-0">
                <div class="sticky top-20">
                    <flux:heading size="sm" class="mb-3 text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-xs">
                        @if ($tab === 'rows')
                            {{ __('Categories') }}
                        @else
                            {{ __('Website Type') }}
                        @endif
                    </flux:heading>
                    <nav class="space-y-1">
                        <button
                            wire:click="$set('category', '')"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors {{ $category === '' ? 'bg-primary/10 text-primary font-medium' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                        >
                            {{ __('All') }}
                        </button>
                        @php
                            $sidebarItems = $tab === 'rows'
                                ? $this->rowCategories
                                : $this->pageCategories;
                        @endphp
                        @foreach ($sidebarItems as $value => $label)
                            <button
                                wire:click="$set('category', '{{ $value }}')"
                                class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors {{ $category === $value ? 'bg-primary/10 text-primary font-medium' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </nav>
                </div>
            </aside>

            {{-- Main content --}}
            <div class="flex-1 min-w-0">
                {{-- Tabs --}}
                <div class="flex gap-1 mb-6 border-b border-zinc-200 dark:border-zinc-700">
                    <button
                        wire:click="setTab('rows')"
                        class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px {{ $tab === 'rows' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                    >
                        {{ __('Row Library') }}
                        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ $tab === 'rows' ? 'bg-primary/10 text-primary' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400' }}">
                            {{ $this->rows->total() }}
                        </span>
                    </button>
                    <button
                        wire:click="setTab('pages')"
                        class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px {{ $tab === 'pages' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                    >
                        {{ __('Page Library') }}
                        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ $tab === 'pages' ? 'bg-primary/10 text-primary' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400' }}">
                            {{ $this->pages->total() }}
                        </span>
                    </button>
                    <button
                        wire:click="setTab('menus')"
                        class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px {{ $tab === 'menus' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                    >
                        {{ __('Menus') }}
                        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ $tab === 'menus' ? 'bg-primary/10 text-primary' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400' }}">
                            {{ count($this->menuTemplates) }}
                        </span>
                    </button>
                </div>

                @if ($tab === 'menus')
                    @php
                        $visibleTemplates = $category
                            ? array_filter($this->menuTemplates, fn ($k) => $k === $category, ARRAY_FILTER_USE_KEY)
                            : $this->menuTemplates;
                    @endphp
                    @if (empty($visibleTemplates))
                        <div class="text-center py-20 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
                            <flux:icon name="bars-3" class="size-12 mx-auto mb-3 text-zinc-300 dark:text-zinc-600" />
                            <flux:heading class="text-zinc-500">No templates for this type</flux:heading>
                        </div>
                    @else
                        <div class="mb-4">
                            <flux:callout variant="info">
                                <flux:callout.text>Browse suggested navigation menus by website type. Click <strong>Import</strong> to add them to your active navigation. Menus whose slug already exists in your navigation are skipped on import. After importing, visit <a href="{{ route('dashboard.menus') }}" class="underline" wire:navigate>Menus</a> to review.</flux:callout.text>
                            </flux:callout>
                        </div>
                        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach ($visibleTemplates as $typeKey => $template)
                                @php
                                    $typeLabel = collect(\App\Enums\PageCategory::cases())->firstWhere(fn ($c) => $c->value === $typeKey)?->label() ?? ucfirst($typeKey);
                                @endphp
                                <div wire:key="menu-template-{{ $typeKey }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden hover:border-primary/40 transition-colors flex flex-col">
                                    <div class="px-4 pt-4 pb-3 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                                        <div>
                                            <h3 class="font-semibold text-zinc-900 dark:text-white text-sm">{{ $typeLabel }}</h3>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ count($template['menus'] ?? []) }} menu(s)</p>
                                        </div>
                                        <flux:badge size="sm">{{ $typeKey }}</flux:badge>
                                    </div>
                                    <div class="px-4 py-3 flex-1 space-y-3">
                                        @foreach ($template['menus'] ?? [] as $menu)
                                            <div>
                                                <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wide mb-1">{{ $menu['label'] }}</p>
                                                <ul class="space-y-0.5">
                                                    @foreach (array_slice($menu['items'] ?? [], 0, 5) as $item)
                                                        <li class="text-xs text-zinc-500 dark:text-zinc-500 flex items-center gap-1.5">
                                                            <span class="size-1 rounded-full bg-zinc-300 dark:bg-zinc-600 shrink-0"></span>
                                                            {{ $item['label'] }}
                                                        </li>
                                                    @endforeach
                                                    @if (count($menu['items'] ?? []) > 5)
                                                        <li class="text-xs text-zinc-400 dark:text-zinc-600 italic pl-2.5">+ {{ count($menu['items']) - 5 }} more</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="px-4 pb-4 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                        <flux:button wire:click="openImportModal('{{ $typeKey }}')" variant="primary" size="sm" class="w-full" icon="arrow-down-tray">
                                            {{ __('Import') }}
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @elseif ($tab === 'rows')
                    @if ($this->rows->isEmpty())
                        <div class="text-center py-20 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
                            <flux:icon name="squares-2x2" class="size-12 mx-auto mb-3 text-zinc-300 dark:text-zinc-600" />
                            <flux:heading class="text-zinc-500">No rows yet</flux:heading>
                            <flux:text class="mt-1 text-sm">Add rows manually or sync from your library files.</flux:text>
                            <div class="mt-6 flex justify-center gap-3">
                                <flux:button wire:click="syncLibrary" variant="outline" size="sm" icon="arrow-path">Sync Library</flux:button>
                                <flux:button wire:click="openCreateModal" variant="primary" size="sm" icon="plus">Add Row</flux:button>
                            </div>
                        </div>
                    @else
                        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach ($this->rows as $row)
                                <div wire:key="row-{{ $row->id }}" class="group rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden hover:border-primary/40 transition-colors flex flex-col">
                                    {{-- Live preview --}}
                                    <div class="relative h-40 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                        <div class="absolute inset-0 flex items-center justify-center animate-pulse pointer-events-none">
                                            <flux:icon name="photo" class="size-8 text-zinc-300 dark:text-zinc-600" />
                                        </div>
                                        <iframe
                                            src="{{ route('dashboard.design-library.preview', ['type' => 'row', 'id' => $row->id]) }}"
                                            class="absolute top-0 left-0 border-0"
                                            style="width:1280px;height:800px;transform:scale(0.25);transform-origin:0 0;pointer-events:none;"
                                            loading="lazy"
                                            scrolling="no"
                                            tabindex="-1"
                                            aria-hidden="true"
                                            onload="this.previousElementSibling.style.display='none'"
                                        ></iframe>
                                    </div>

                                    {{-- Card body --}}
                                    <div class="p-4 flex flex-col flex-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <h3 class="font-semibold text-zinc-900 dark:text-white text-sm truncate">{{ $row->name }}</h3>
                                                @if ($row->description)
                                                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">{{ $row->description }}</p>
                                                @endif
                                            </div>
                                            <flux:badge size="sm" class="shrink-0">{{ $row->category->label() }}</flux:badge>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-2">
                                            <flux:button
                                                href="{{ route('dashboard.design-library.editor', ['rowId' => $row->id]) }}"
                                                variant="primary"
                                                size="sm"
                                                class="flex-1"
                                                wire:navigate
                                            >
                                                {{ __('Use in Page') }}
                                            </flux:button>
                                            <a href="{{ route('dashboard.design-library.preview', ['type' => 'row', 'id' => $row->id]) }}" target="_blank" rel="noopener noreferrer">
                                                <flux:button variant="ghost" size="sm" icon="eye" />
                                            </a>
                                            <flux:button wire:click="openEditModal({{ $row->id }})" variant="ghost" size="sm" icon="pencil" />
                                            @if ($confirmingDelete === $row->id)
                                                <flux:button wire:click="deleteItem({{ $row->id }})" variant="danger" size="sm">{{ __('Delete?') }}</flux:button>
                                                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">{{ __('No') }}</flux:button>
                                            @else
                                                <flux:button wire:click="$set('confirmingDelete', {{ $row->id }})" variant="ghost" size="sm" icon="trash" class="text-red-500 dark:text-red-400" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($this->rows->hasPages())
                            <div class="mt-6">{{ $this->rows->links() }}</div>
                        @endif
                    @endif
                @elseif ($tab === 'pages')
                    @if ($this->pages->isEmpty())
                        <div class="text-center py-20 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
                            <flux:icon name="document-text" class="size-12 mx-auto mb-3 text-zinc-300 dark:text-zinc-600" />
                            <flux:heading class="text-zinc-500">No page bundles yet</flux:heading>
                            <flux:text class="mt-1 text-sm">Create a bundle to quickly insert multiple rows at once.</flux:text>
                            <div class="mt-6 flex justify-center gap-3">
                                <flux:button wire:click="syncLibrary" variant="outline" size="sm" icon="arrow-path">Sync Library</flux:button>
                                <flux:button wire:click="openCreateModal" variant="primary" size="sm" icon="plus">Add Page</flux:button>
                            </div>
                        </div>
                    @else
                        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach ($this->pages as $page)
                                <div wire:key="page-{{ $page->id }}" class="group rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden hover:border-primary/40 transition-colors flex flex-col">
                                    {{-- Live preview --}}
                                    <div class="relative h-40 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                        <div class="absolute inset-0 flex items-center justify-center animate-pulse pointer-events-none">
                                            <flux:icon name="photo" class="size-8 text-zinc-300 dark:text-zinc-600" />
                                        </div>
                                        <iframe
                                            src="{{ route('dashboard.design-library.preview', ['type' => 'page', 'id' => $page->id]) }}"
                                            class="absolute top-0 left-0 border-0"
                                            style="width:1280px;height:800px;transform:scale(0.25);transform-origin:0 0;pointer-events:none;"
                                            loading="lazy"
                                            scrolling="no"
                                            tabindex="-1"
                                            aria-hidden="true"
                                            onload="this.previousElementSibling.style.display='none'"
                                        ></iframe>
                                    </div>

                                    <div class="p-4 flex flex-col flex-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <h3 class="font-semibold text-zinc-900 dark:text-white text-sm truncate">{{ $page->name }}</h3>
                                                @if ($page->description)
                                                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">{{ $page->description }}</p>
                                                @endif
                                            </div>
                                            <flux:badge size="sm" class="shrink-0">{{ $page->website_category->label() }}</flux:badge>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-2">
                                            <flux:button
                                                href="{{ route('dashboard.design-library.editor', ['pageId' => $page->id]) }}"
                                                variant="primary"
                                                size="sm"
                                                class="flex-1"
                                                wire:navigate
                                            >
                                                {{ __('Use in Page') }}
                                            </flux:button>
                                            <a href="{{ route('dashboard.design-library.preview', ['type' => 'page', 'id' => $page->id]) }}" target="_blank" rel="noopener noreferrer">
                                                <flux:button variant="ghost" size="sm" icon="eye" />
                                            </a>
                                            <flux:button wire:click="openEditModal({{ $page->id }})" variant="ghost" size="sm" icon="pencil" />
                                            @if ($confirmingDelete === $page->id)
                                                <flux:button wire:click="deleteItem({{ $page->id }})" variant="danger" size="sm">{{ __('Delete?') }}</flux:button>
                                                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">{{ __('No') }}</flux:button>
                                            @else
                                                <flux:button wire:click="$set('confirmingDelete', {{ $page->id }})" variant="ghost" size="sm" icon="trash" class="text-red-500 dark:text-red-400" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($this->pages->hasPages())
                            <div class="mt-6">{{ $this->pages->links() }}</div>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </flux:main>
</div>
