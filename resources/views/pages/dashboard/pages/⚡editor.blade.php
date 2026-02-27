<?php

use App\Enums\RowCategory;
use App\Jobs\IndexDesignLibraryJob;
use App\Models\ContentOverride;
use App\Models\DesignRow;
use App\Support\VoltFileService;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.editor')] #[Title('Page Editor')] class extends Component
{
    use WithFileUploads;

    #[Url]
    public string $file = '';

    public string $phpSection = '';

    /** @var array<int, array{slug: string, name: string, blade: string}> */
    public array $rows = [];

    public bool $isDirty = false;

    public string $previewUrl = '';

    public string $liveUrl = '';

    public bool $showLibraryDrawer = false;

    public string $librarySearch = '';

    public string $libraryCategory = '';

    public ?int $insertAtIndex = null;

    public bool $showSeoModal = false;

    public string $pageSlug = '';

    public string $originalPageSlug = '';

    public bool $createSlugRedirect = false;

    public string $slugRedirectType = '301';

    public bool $isCachedPage = true;

    public string $seoTitle = '';

    public string $seoDescription = '';

    public bool $seoNoindex = false;

    public string $seoOgImage = '';

    public string $redirectUrl = '';

    public string $redirectType = '301';

    #[Validate('required|in:draft,published,unlisted,unpublished')]
    public string $pageStatus = 'published';

    // Content editor state
    public bool $showContentEditor = false;

    public ?int $editingRowIndex = null;

    /** @var array<int, array{slug: string, key: string, type: string, default: string, label: string}> */
    public array $contentFields = [];

    /** @var array<string, string|bool> */
    public array $contentValues = [];

    /** @var array<string, string|bool> */
    public array $originalContentValues = [];

    public mixed $pendingImageUpload = null;

    public string $pendingImageKey = '';

    public function mount(): void
    {
        if ($this->file) {
            $this->loadFile($this->file);
        }
    }

    public function updatedFile(string $value): void
    {
        if ($value) {
            $this->loadFile($value);
        }
    }

    public function updatedPendingImageUpload(): void
    {
        if (! $this->pendingImageUpload || ! $this->pendingImageKey) {
            return;
        }

        $path = $this->pendingImageUpload->store('content-overrides', 'public');
        $this->contentValues[$this->pendingImageKey] = $path;
        $this->pendingImageUpload = null;

        $field = collect($this->contentFields)->firstWhere('key', $this->pendingImageKey);

        if ($field) {
            session()->put('editor_draft_overrides.'.$field['slug'].':'.$field['key'], ['type' => 'image', 'value' => $path]);
            $this->refreshPreview();
        }
    }

    public function updatedContentValues(mixed $value, string $key): void
    {
        $field = collect($this->contentFields)->firstWhere('key', $key);

        if (! $field) {
            return;
        }

        $raw = $this->contentValues[$key] ?? '';
        $draftValue = $field['type'] === 'toggle' ? ($raw ? '1' : '0') : (string) $raw;

        session()->put('editor_draft_overrides.'.$field['slug'].':'.$key, ['type' => $field['type'], 'value' => $draftValue]);

        $this->refreshPreview();
    }

    /** @return array<string, string> */
    #[Computed]
    public function voltFiles(): array
    {
        return (new VoltFileService)->listVoltFiles()['Public Pages'] ?? [];
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, DesignRow> */
    #[Computed]
    public function libraryRows(): \Illuminate\Database\Eloquent\Collection
    {
        return DesignRow::query()
            ->when($this->libraryCategory, fn ($q) => $q->where('category', $this->libraryCategory))
            ->when($this->librarySearch, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->librarySearch.'%')
                    ->orWhere('description', 'like', '%'.$this->librarySearch.'%');
            }))
            ->orderBy('category')
            ->orderBy('sort_order')
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

    public function loadFile(string $relativePath): void
    {
        $this->file = $relativePath;
        $fullPath = resource_path('views/'.$relativePath);

        if (! file_exists($fullPath)) {
            $this->dispatch('notify', message: 'File not found.');

            return;
        }

        $service = new VoltFileService;
        $parsed = $service->parseFile($fullPath);

        $this->phpSection = $parsed['phpSection'];
        $this->rows = $parsed['rows'];
        $this->isDirty = false;
        $this->parseSeoFromPhpSection();
        $this->pageSlug = preg_match('#^pages/⚡([^/]+)\.blade\.php$#u', $relativePath, $m) ? $m[1] : '';
        $this->originalPageSlug = $this->pageSlug;
        $this->createSlugRedirect = false;
        $this->slugRedirectType = '301';
        $this->isCachedPage = $this->pageSlug ? $service->isRouteCached($this->pageSlug) : true;
        $this->liveUrl = $service->getRouteForFile($relativePath);
        $this->previewUrl = route('design-library.preview', ['token' => $service->previewToken($relativePath)]);

        $this->showContentEditor = false;
        $this->editingRowIndex = null;

        $this->refreshPreview();
    }

    public function moveRowUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        $rows = $this->rows;
        [$rows[$index - 1], $rows[$index]] = [$rows[$index], $rows[$index - 1]];
        $this->rows = array_values($rows);
        $this->isDirty = true;

        $this->refreshPreview();
    }

    public function moveRowDown(int $index): void
    {
        if ($index >= count($this->rows) - 1) {
            return;
        }

        $rows = $this->rows;
        [$rows[$index], $rows[$index + 1]] = [$rows[$index + 1], $rows[$index]];
        $this->rows = array_values($rows);
        $this->isDirty = true;

        $this->refreshPreview();
    }

    public function reorderRows(int $from, int $to): void
    {
        if ($from === $to) {
            return;
        }

        $row = array_splice($this->rows, $from, 1)[0];
        array_splice($this->rows, $to, 0, [$row]);
        $this->rows = array_values($this->rows);
        $this->isDirty = true;

        $this->refreshPreview();
    }

    public function removeRow(int $index): void
    {
        $slug = $this->rows[$index]['slug'] ?? null;

        if ($slug) {
            if ($this->phpSection) {
                $this->phpSection = (new VoltFileService)->removePhpCode($this->phpSection, $slug);
            }

            ContentOverride::query()->where('row_slug', $slug)->delete();
        }

        array_splice($this->rows, $index, 1);
        $this->rows = array_values($this->rows);
        $this->isDirty = true;

        if ($this->editingRowIndex === $index) {
            $this->showContentEditor = false;
            $this->editingRowIndex = null;
        }

        $this->refreshPreview();
    }

    public function openLibraryDrawer(int $atIndex): void
    {
        IndexDesignLibraryJob::dispatchSync();

        $this->insertAtIndex = $atIndex;
        $this->librarySearch = '';
        $this->libraryCategory = '';
        $this->showLibraryDrawer = true;
        unset($this->libraryRows);
    }

    public function insertRow(int $designRowId, int $atIndex): void
    {
        $designRow = DesignRow::query()->find($designRowId);

        if (! $designRow) {
            return;
        }

        $slug = $designRow->category->value.'-'.Str::random(6);
        $newRow = [
            'slug' => $slug,
            'name' => $designRow->name,
            'blade' => str_replace('__SLUG__', $slug, $designRow->blade_code),
        ];

        if ($designRow->php_code && $this->phpSection) {
            $this->phpSection = (new VoltFileService)->injectPhpCode(
                $this->phpSection,
                $designRow->php_code,
                $slug
            );
        }

        array_splice($this->rows, $atIndex, 0, [$newRow]);
        $this->rows = array_values($this->rows);
        $this->isDirty = true;
        $this->showLibraryDrawer = false;

        $this->refreshPreview();
    }

    public function openContentEditor(int $index): void
    {
        $this->editingRowIndex = $index;
        $row = $this->rows[$index];
        $this->contentFields = $this->parseContentFields($row['blade']);
        $this->pendingImageKey = '';
        $this->pendingImageUpload = null;

        $slugs = array_unique(array_column($this->contentFields, 'slug'));
        $overrides = ContentOverride::query()
            ->whereIn('row_slug', $slugs)
            ->get()
            ->keyBy(fn (ContentOverride $o) => $o->row_slug.':'.$o->key);

        $drafts = session('editor_draft_overrides', []);

        $this->contentValues = [];
        $this->originalContentValues = [];

        foreach ($this->contentFields as $field) {
            $dbKey = $field['slug'].':'.$field['key'];
            $dbValue = $overrides->get($dbKey)?->value;

            // originalContentValues always reflects the last-saved DB state
            $this->originalContentValues[$field['key']] = $field['type'] === 'toggle'
                ? ($dbValue !== null ? $dbValue === '1' : $field['default'] === '1')
                : ($dbValue ?? '');

            // contentValues prefers any unsaved session draft
            $draft = $drafts[$dbKey] ?? null;
            $rawValue = $draft !== null ? ($draft['value'] ?? null) : $dbValue;
            $this->contentValues[$field['key']] = $field['type'] === 'toggle'
                ? ($rawValue !== null ? $rawValue === '1' : $field['default'] === '1')
                : ($rawValue ?? '');
        }

        $this->showContentEditor = true;
    }

    public function closeContentEditor(): void
    {
        $this->showContentEditor = false;
        $this->editingRowIndex = null;
    }

    public function cancelContentEditor(): void
    {
        foreach ($this->contentFields as $field) {
            session()->forget('editor_draft_overrides.'.$field['slug'].':'.$field['key']);
        }

        $this->contentValues = $this->originalContentValues;
        $this->refreshPreview();
        $this->showContentEditor = false;
        $this->editingRowIndex = null;
    }

    private function persistAllDraftOverrides(): void
    {
        /** @var array<string, array{type: string, value: string}> $drafts */
        $drafts = session('editor_draft_overrides', []);

        foreach ($drafts as $draftKey => $draft) {
            [$slug, $key] = explode(':', $draftKey, 2);
            $type = $draft['type'];
            $value = $draft['value'];

            if ($value === '') {
                ContentOverride::query()
                    ->where('row_slug', $slug)
                    ->where('key', $key)
                    ->delete();
            } else {
                ContentOverride::updateOrCreate(
                    ['row_slug' => $slug, 'key' => $key],
                    ['type' => $type, 'value' => $value]
                );
            }
        }

        session()->forget('editor_draft_overrides');
    }

    public function setPendingImageKey(string $key): void
    {
        $this->pendingImageKey = $key;
    }

    public function removeImage(string $key): void
    {
        $this->contentValues[$key] = '';
    }

    public function saveFile(): void
    {
        if (! $this->file) {
            return;
        }

        $this->persistAllDraftOverrides();

        $service = new VoltFileService;
        $fullPath = resource_path('views/'.$this->file);
        $service->writeFile($fullPath, $service->buildFileContent($this->phpSection, $this->rows));

        $this->isDirty = false;
        $this->dispatch('notify', message: 'Page saved.');
    }

    public function discardChanges(): void
    {
        if ($this->file) {
            $this->loadFile($this->file);
        }
    }

    /**
     * Parse content() calls from a row's blade code into editable field definitions.
     *
     * @return array<int, array{slug: string, key: string, type: string, default: string, label: string}>
     */
    private function parseContentFields(string $blade): array
    {
        preg_match_all(
            "/content\('([^']+)',\s*'([^']+)',\s*'([^']*)'(?:,\s*'([^']*)')?\)/",
            $blade,
            $matches,
            PREG_SET_ORDER
        );

        $fields = [];
        $seen = [];

        foreach ($matches as $match) {
            $dedupeKey = $match[1].':'.$match[2];

            if (isset($seen[$dedupeKey])) {
                continue;
            }

            $seen[$dedupeKey] = true;
            $fields[] = [
                'slug' => $match[1],
                'key' => $match[2],
                'default' => $match[3],
                'type' => $match[4] ?? 'text',
                'label' => ucwords(str_replace('_', ' ', $match[2])),
            ];
        }

        return $fields;
    }

    public function saveSeoSettings(): void
    {
        $isPublicPage = (bool) preg_match('#^pages/⚡[^/]+\.blade\.php$#u', $this->file);

        $rules = ['pageStatus' => 'required|in:draft,published,unlisted,unpublished'];

        if ($isPublicPage) {
            $rules['pageSlug'] = ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'];
        }

        $this->validate($rules);

        if (! $this->file) {
            return;
        }

        if ($isPublicPage && preg_match('#^pages/⚡([^/]+)\.blade\.php$#u', $this->file, $m)) {
            $currentSlug = $m[1];
            $service = new VoltFileService;

            if ($this->pageSlug !== $currentSlug) {
                $newRelativePath = 'pages/⚡'.$this->pageSlug.'.blade.php';

                if (file_exists(resource_path('views/'.$newRelativePath))) {
                    $this->addError('pageSlug', 'A page with this slug already exists.');

                    return;
                }

                $newFile = $service->renamePage($this->file, $this->pageSlug, $this->isCachedPage);
                $this->file = $newFile;
                $this->liveUrl = $service->getRouteForFile($newFile);
                $this->previewUrl = route('design-library.preview', ['token' => $service->previewToken($newFile)]);

                if ($this->createSlugRedirect) {
                    $service->addRedirectRoute($currentSlug, $this->pageSlug, (int) $this->slugRedirectType);
                }
            } elseif ($service->isRouteCached($currentSlug) !== $this->isCachedPage) {
                $service->removePublicRoute($currentSlug);
                $service->addPublicRoute($currentSlug, $this->isCachedPage);
            }

            $this->originalPageSlug = $this->pageSlug;
            $this->createSlugRedirect = false;
            $this->slugRedirectType = '301';
        }

        $this->updatePhpSectionWithSeo();
        $this->showSeoModal = false;
        $this->saveFile();
    }

    private function parseSeoFromPhpSection(): void
    {
        preg_match("/#\[Title\('([^']*)'\)\]/", $this->phpSection, $titleMatch);
        $this->seoTitle = $titleMatch[1] ?? '';

        preg_match("/'description'\s*=>\s*'([^']*)'/", $this->phpSection, $descMatch);
        $this->seoDescription = $descMatch[1] ?? '';

        $this->seoNoindex = (bool) preg_match("/'noindex'\s*=>\s*true/", $this->phpSection);

        preg_match("/'ogImage'\s*=>\s*'([^']*)'/", $this->phpSection, $ogMatch);
        $this->seoOgImage = $ogMatch[1] ?? '';

        preg_match("/'status'\s*=>\s*'([^']*)'/", $this->phpSection, $statusMatch);
        $this->pageStatus = $statusMatch[1] ?? 'published';

        preg_match('/\/\/ ROW:php:start:page-redirect.*?redirect\(\'([^\']*)\',\s*(\d+)\)/s', $this->phpSection, $redirectMatch);
        $this->redirectUrl = $redirectMatch[1] ?? '';
        $this->redirectType = $redirectMatch[2] ?? '301';
    }

    private function updatePhpSectionWithSeo(): void
    {
        $escapedTitle = str_replace("'", "\'", $this->seoTitle);
        $escapedDesc = str_replace("'", "\'", $this->seoDescription);
        $escapedOgImage = str_replace("'", "\'", $this->seoOgImage);

        $this->phpSection = preg_replace(
            "/#\[Title\('([^']*)'\)\]/",
            "#[Title('{$escapedTitle}')]",
            $this->phpSection
        );

        $data = [];

        if (! empty($escapedDesc)) {
            $data[] = "'description' => '{$escapedDesc}'";
        }

        if ($this->seoNoindex) {
            $data[] = "'noindex' => true";
        }

        if (! empty($escapedOgImage)) {
            $data[] = "'ogImage' => '{$escapedOgImage}'";
        }

        if ($this->pageStatus !== 'published') {
            $data[] = "'status' => '{$this->pageStatus}'";
        }

        $newLayout = empty($data)
            ? "#[Layout('layouts.public')]"
            : "#[Layout('layouts.public', [".implode(', ', $data).'])]';

        $this->phpSection = preg_replace(
            "/#\[Layout\('layouts\.public'(?:,\s*\[[^\]]*\])?\)\]/",
            $newLayout,
            $this->phpSection
        );

        $service = new VoltFileService;
        $accessibleStatuses = ['published', 'unlisted'];

        // Remove both behavior blocks first, then re-inject the appropriate one.
        $this->phpSection = $service->removePhpCode($this->phpSection, 'page-status-abort');
        $this->phpSection = $service->removePhpCode($this->phpSection, 'page-redirect');

        if (! empty($this->redirectUrl)) {
            $escapedRedirectUrl = str_replace("'", "\'", $this->redirectUrl);
            $this->phpSection = $service->injectPhpCode(
                $this->phpSection,
                "public function boot(): void\n{\n    redirect('{$escapedRedirectUrl}', {$this->redirectType});\n}",
                'page-redirect'
            );
        } elseif (! in_array($this->pageStatus, $accessibleStatuses)) {
            $this->phpSection = $service->injectPhpCode(
                $this->phpSection,
                "public function boot(): void\n{\n    abort(404);\n}",
                'page-status-abort'
            );
        }
    }

    private function refreshPreview(): void
    {
        try {
            $service = new VoltFileService;
            // Strip behavior blocks so the preview iframe always renders the page content.
            $previewPhpSection = $service->removePhpCode($this->phpSection, 'page-status-abort');
            $previewPhpSection = $service->removePhpCode($previewPhpSection, 'page-redirect');
            $service->writePreviewFile($previewPhpSection, $this->rows, $this->file);
        } catch (\Throwable) {
            // Preview write failed; continue without updating the iframe.
        }

        $this->dispatch('refresh-preview', url: $this->previewUrl);
    }
}; ?>

<div>
    {{-- Library Drawer --}}
    <flux:modal wire:model="showLibraryDrawer" class="w-full max-w-xl">
        <flux:heading size="lg" class="mb-4">{{ __('Insert Row') }}</flux:heading>

        <div class="flex gap-3 mb-4">
            <flux:input
                wire:model.live="librarySearch"
                placeholder="Search rows…"
                icon="magnifying-glass"
                class="flex-1"
            />
            <flux:select wire:model.live="libraryCategory" class="w-44">
                <flux:select.option value="">{{ __('All categories') }}</flux:select.option>
                @foreach ($this->rowCategories as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @if ($this->libraryRows->isEmpty())
            <div class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="squares-2x2" class="size-10 mx-auto mb-3 opacity-40" />
                <p class="text-sm">No rows found.</p>
            </div>
        @else
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @foreach ($this->libraryRows as $libRow)
                    <div wire:key="lib-{{ $libRow->id }}" class="flex items-center gap-3 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-zinc-900 dark:text-white text-sm truncate">{{ $libRow->name }}</div>
                            @if ($libRow->description)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate mt-0.5">{{ $libRow->description }}</div>
                            @endif
                            <flux:badge size="sm" class="mt-1">{{ $libRow->category->label() }}</flux:badge>
                        </div>
                        <flux:button
                            wire:click="insertRow({{ $libRow->id }}, {{ $insertAtIndex ?? count($rows) }})"
                            variant="primary"
                            size="sm"
                        >
                            {{ __('Insert') }}
                        </flux:button>
                    </div>
                @endforeach
            </div>
        @endif
    </flux:modal>


    <div
        x-data="{
            previewWidth: null,
            showAllBreakpoints: false,
            setWidth(w) { this.previewWidth = this.previewWidth === w ? null : w; }
        }"
        @keydown.ctrl.s.window.prevent="if ($wire.file) $wire.saveFile()"
        @keydown.meta.s.window.prevent="if ($wire.file) $wire.saveFile()"
        @message.window="if ($event.origin === window.location.origin && $event.data && $event.data.type === 'editor-save-page' && $wire.file) $wire.saveFile()"
        class="flex flex-col min-h-screen bg-white dark:bg-zinc-900"
    >
        {{-- Editor toolbar --}}
        <div class="sticky top-0 z-30 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 px-6 py-3 flex items-center gap-3">
            <flux:button href="{{ route('dashboard.pages') }}" variant="outline" size="sm" icon="list-bullet" wire:navigate>
                {{ __('Pages') }}
            </flux:button>

            <div class="w-48 shrink-0">
                <flux:select wire:model.live="file" placeholder="Select a page to edit…" size="sm">
                    <flux:select.option value="">{{ __('Select a page…') }}</flux:select.option>
                    @foreach ($this->voltFiles as $label => $path)
                        <flux:select.option value="{{ $path }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            {{-- Center: preview width controls --}}
            <div class="flex-1 flex justify-center items-center gap-1">
                @if ($file)
                    <div x-show="! showAllBreakpoints" class="flex items-center gap-0.5">
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="device-phone-mobile"
                            x-on:click="setWidth('390px')"
                            x-bind:class="previewWidth === '390px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            title="Mobile (390px)"
                            :loading="false"
                        />
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="device-tablet"
                            x-on:click="setWidth('768px')"
                            x-bind:class="previewWidth === '768px' && ! showAllBreakpoints ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            title="Tablet (768px)"
                            :loading="false"
                        />
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="computer-desktop"
                            x-on:click="setWidth(null)"
                            x-bind:class="previewWidth === null ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            title="Desktop (full width)"
                            :loading="false"
                        />
                    </div>

                    <div x-show="showAllBreakpoints" class="flex items-center gap-0.5" style="display: none">
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="device-phone-mobile"
                            x-on:click="setWidth('375px')"
                            x-bind:class="previewWidth === '375px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            title="Mobile (375px)"
                            :loading="false"
                        />
                        <flux:button
                            size="sm"
                            variant="ghost"
                            x-on:click="setWidth('640px')"
                            x-bind:class="previewWidth === '640px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            title="SM — 640px"
                            :loading="false"
                        >sm</flux:button>
                        <flux:button
                            size="sm"
                            variant="ghost"
                            x-on:click="setWidth('768px')"
                            x-bind:class="previewWidth === '768px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            title="MD — 768px"
                            :loading="false"
                        >md</flux:button>
                        <flux:button
                            size="sm"
                            variant="ghost"
                            x-on:click="setWidth('1024px')"
                            x-bind:class="previewWidth === '1024px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            title="LG — 1024px"
                            :loading="false"
                        >lg</flux:button>
                        <flux:button
                            size="sm"
                            variant="ghost"
                            x-on:click="setWidth('1280px')"
                            x-bind:class="previewWidth === '1280px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            title="XL — 1280px"
                            :loading="false"
                        >xl</flux:button>
                        <flux:button
                            size="sm"
                            variant="ghost"
                            x-on:click="setWidth('1536px')"
                            x-bind:class="previewWidth === '1536px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            title="2XL — 1536px"
                            :loading="false"
                        >2xl</flux:button>
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="computer-desktop"
                            x-on:click="setWidth(null)"
                            x-bind:class="previewWidth === null ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            title="Desktop (full width)"
                            :loading="false"
                        />
                    </div>

                    <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-700 mx-1"></div>

                    <flux:button
                        size="sm"
                        variant="ghost"
                        icon="arrows-right-left"
                        x-on:click="showAllBreakpoints = ! showAllBreakpoints"
                        x-bind:class="showAllBreakpoints ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                        title="Toggle breakpoint mode"
                        :loading="false"
                    />
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if ($isDirty)
                    <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">Unsaved changes</span>
                @endif

                @if ($file)
                    <flux:tooltip content="Page settings">
                        <flux:button variant="ghost" size="sm" icon="adjustments-horizontal" wire:click="$set('showSeoModal', true)" :loading="false" />
                    </flux:tooltip>

                    @if ($liveUrl)
                        <a href="{{ $liveUrl }}" target="_blank">
                            <flux:button variant="outline" size="sm" icon="arrow-top-right-on-square">{{ __('View Live') }}</flux:button>
                        </a>
                    @endif
                    <flux:button wire:click="saveFile" variant="primary" size="sm" icon="check">
                        {{ __('Save') }}
                    </flux:button>
                    <flux:tooltip content="Discard changes">
                        <flux:button wire:click="discardChanges" variant="ghost" size="sm" icon="arrow-path" />
                    </flux:tooltip>
                @endif
            </div>
        </div>

        @if (! app()->isLocal())
            <div class="px-6 pt-4">
                <flux:callout variant="warning">
                    <flux:callout.heading>Production environment</flux:callout.heading>
                    <flux:callout.text>Saving will write directly to the blade file on disk. Changes will be lost on next deployment unless committed to git.</flux:callout.text>
                </flux:callout>
            </div>
        @endif

        @if (! $file)
            <div class="flex items-center justify-center h-96 text-zinc-500 dark:text-zinc-400">
                <div class="text-center">
                    <flux:icon name="document-text" class="size-16 mx-auto mb-4 opacity-30" />
                    <flux:heading class="text-zinc-500">Select a page to edit</flux:heading>
                    <flux:text class="mt-2 text-sm">Choose a volt file from the dropdown above to get started.</flux:text>
                </div>
            </div>
        @else
            <div class="flex" style="height: calc(100vh - 120px);">
                {{-- Right panel: row list / inline content editor --}}
                <div class="w-96 shrink-0 order-last border-l border-zinc-200 dark:border-zinc-700 flex flex-col">
                    @if ($showContentEditor && $editingRowIndex !== null && isset($rows[$editingRowIndex]))
                        {{-- Content editor view --}}
                        <div class="shrink-0 flex items-center gap-2 p-3 border-b border-zinc-200 dark:border-zinc-700">
                            <flux:button
                                wire:click="cancelContentEditor"
                                variant="ghost"
                                size="sm"
                                icon="arrow-left"
                                title="Back to rows"
                            />
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $rows[$editingRowIndex]['name'] }}</div>
                                <div class="text-[10px] font-mono text-zinc-400 dark:text-zinc-500 truncate">{{ $rows[$editingRowIndex]['slug'] }}</div>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto p-4">
                            @if (empty($contentFields))
                                <div class="text-center py-8 text-zinc-400 dark:text-zinc-500">
                                    <flux:icon name="pencil-slash" class="size-10 mx-auto mb-2 opacity-40" />
                                    <p class="text-sm">This row has no editable content fields.</p>
                                </div>
                            @else
                                <div class="space-y-5">
                                    @foreach ($contentFields as $field)
                                        <div wire:key="field-{{ $field['key'] }}">
                                            <flux:label class="mb-1.5">{{ $field['label'] }}</flux:label>

                                            @if ($field['type'] === 'image')
                                                @php $currentPath = $contentValues[$field['key']] ?? ''; @endphp
                                                @if ($currentPath)
                                                    <div class="mb-2 relative inline-block">
                                                        <img
                                                            src="{{ Storage::url($currentPath) }}"
                                                            alt=""
                                                            class="h-24 rounded-lg object-cover border border-zinc-200 dark:border-zinc-700"
                                                        >
                                                        <button
                                                            wire:click="removeImage('{{ $field['key'] }}')"
                                                            class="absolute -top-2 -right-2 size-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600"
                                                            title="Remove image"
                                                        >
                                                            <flux:icon name="x-mark" class="size-3" />
                                                        </button>
                                                    </div>
                                                @endif
                                                <div
                                                    x-data
                                                    x-on:click="$refs.imgInput_{{ $field['key'] }}.click()"
                                                    class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg cursor-pointer hover:border-primary transition-colors"
                                                >
                                                    <flux:icon name="photo" class="size-5 text-zinc-400 shrink-0" />
                                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                                        {{ $currentPath ? 'Replace image…' : 'Upload image…' }}
                                                    </span>
                                                    <input
                                                        x-ref="imgInput_{{ $field['key'] }}"
                                                        type="file"
                                                        accept="image/*"
                                                        class="hidden"
                                                        x-on:change="
                                                            $wire.setPendingImageKey('{{ $field['key'] }}').then(() => {
                                                                $wire.upload('pendingImageUpload', $event.target.files[0])
                                                            })
                                                        "
                                                    >
                                                </div>
                                            @elseif ($field['type'] === 'richtext')
                                                <flux:textarea
                                                    wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
                                                    rows="4"
                                                    placeholder="{{ $field['default'] }}"
                                                />
                                                <flux:text class="text-xs text-zinc-400 mt-1">HTML is supported.</flux:text>
                                            @elseif ($field['type'] === 'toggle')
                                                <flux:checkbox
                                                    wire:model.live="contentValues.{{ $field['key'] }}"
                                                    label="Yes"
                                                />
                                            @elseif (str_ends_with($field['key'], '_url'))
                                                <flux:input
                                                    wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
                                                    type="url"
                                                    placeholder="{{ $field['default'] ?: 'https://' }}"
                                                />
                                            @else
                                                <flux:input
                                                    wire:model.live.debounce.400ms="contentValues.{{ $field['key'] }}"
                                                    placeholder="{{ $field['default'] }}"
                                                />
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="shrink-0 flex gap-2 p-3 border-t border-zinc-200 dark:border-zinc-700">
                            <flux:button wire:click="closeContentEditor" variant="outline" icon="arrow-left" class="flex-1">
                                {{ __('Back') }}
                            </flux:button>
                            <flux:button wire:click="cancelContentEditor" variant="outline" icon="x-mark" title="Discard changes made since opening this row">
                                {{ __('Cancel') }}
                            </flux:button>
                        </div>
                    @else
                        {{-- Row list view --}}
                        <div class="shrink-0 px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                            <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">{{ __('Page Rows') }}</flux:heading>
                            @php
                                $statusTooltips = [
                                    'draft'       => 'Saved but not yet visible to the public.',
                                    'published'   => 'Live and visible to all visitors.',
                                    'unlisted'    => 'Accessible via direct link, but excluded from auto-generated navigation.',
                                    'unpublished' => 'Removed from public access — visitors will see a 404.',
                                ];
                            @endphp
                            <div class="flex items-center gap-1.5">
                                <flux:tooltip content="{{ $statusTooltips[$pageStatus] ?? '' }}" position="left">
                                    <span
                                        x-data
                                        x-bind:class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': $wire.pageStatus === 'published',
                                            'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300': $wire.pageStatus === 'unlisted',
                                            'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': $wire.pageStatus === 'draft',
                                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': $wire.pageStatus === 'unpublished',
                                        }"
                                        class="text-xs font-medium px-2 py-0.5 rounded-full capitalize cursor-default"
                                        x-text="$wire.pageStatus"
                                    ></span>
                                </flux:tooltip>

                                <flux:tooltip content="{{ $seoNoindex ? 'Search engines are prevented from indexing this page.' : 'Search engines can index this page.' }}" position="left">
                                    <span
                                        x-data
                                        x-bind:class="$wire.seoNoindex
                                            ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                                            : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'"
                                        class="text-xs font-medium px-2 py-0.5 rounded-full cursor-default"
                                        x-text="$wire.seoNoindex ? 'No Index' : 'Indexed'"
                                    ></span>
                                </flux:tooltip>

                                <flux:tooltip content="{{ $redirectUrl ? 'Redirects to: '.$redirectUrl : 'No redirect configured.' }}" position="left">
                                    <span
                                        x-data
                                        x-bind:class="$wire.redirectUrl
                                            ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                            : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'"
                                        class="text-xs font-medium px-2 py-0.5 rounded-full cursor-default"
                                        x-text="$wire.redirectUrl ? 'Redirect' : 'No Redirect'"
                                    ></span>
                                </flux:tooltip>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto p-3 space-y-2" x-data="{ dragging: null, over: null }">
                            @forelse ($rows as $index => $row)
                                <div
                                    wire:key="row-item-{{ $row['slug'] }}"
                                    class="rounded-lg border bg-white dark:bg-zinc-900 overflow-hidden transition-colors {{ $editingRowIndex === $index ? 'border-primary' : 'border-zinc-200 dark:border-zinc-700' }}"
                                    draggable="true"
                                    @dragstart="dragging = {{ $index }}"
                                    @dragover.prevent="over = {{ $index }}"
                                    @drop="if (dragging !== null) { $wire.reorderRows(dragging, over); } dragging = null; over = null"
                                    @dragend="dragging = null; over = null"
                                    :style="{
                                        opacity: dragging === {{ $index }} ? '0.4' : '',
                                        'border-top': over === {{ $index }} && dragging !== null && dragging > {{ $index }} ? '2px solid var(--color-primary)' : '',
                                        'border-bottom': over === {{ $index }} && dragging !== null && dragging < {{ $index }} ? '2px solid var(--color-primary)' : ''
                                    }"
                                >
                                    {{-- Clickable name area opens content editor --}}
                                    <button
                                        wire:click="openContentEditor({{ $index }})"
                                        class="w-full text-left px-3 pt-3 pb-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors"
                                    >
                                        <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $row['name'] }}</div>
                                        <div class="text-[10px] font-mono text-zinc-400 dark:text-zinc-500 truncate mt-0.5">{{ $row['slug'] }}</div>
                                    </button>

                                    {{-- Row actions --}}
                                    <div class="relative flex items-center px-2 pb-2">
                                        <div class="flex items-center gap-0.5">
                                            <flux:button
                                                wire:click="moveRowUp({{ $index }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="arrow-up"
                                                :disabled="$index === 0"
                                                :class="$index === 0 ? 'opacity-15!' : ''"
                                                title="Move up"
                                                :loading="false"
                                            />
                                            <flux:button
                                                wire:click="moveRowDown({{ $index }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="arrow-down"
                                                :disabled="$index === count($rows) - 1"
                                                :class="$index === count($rows) - 1 ? 'opacity-15!' : ''"
                                                title="Move down"
                                                :loading="false"
                                            />
                                        </div>
                                        <div class="absolute left-1/2 -translate-x-1/2">
                                            <flux:icon name="bars-2" class="size-4 text-zinc-400 dark:text-zinc-500 cursor-grab active:cursor-grabbing" title="Drag to reorder" />
                                        </div>
                                        <div class="flex items-center gap-0.5 ml-auto">
                                            <flux:button
                                                wire:click="openLibraryDrawer({{ $index }})"
                                                variant="ghost"
                                                size="sm"
                                                title="Insert row above"
                                                class="px-1!"
                                                :loading="false"
                                            >
                                                <span class="inline-flex items-center">
                                                    <flux:icon name="plus" class="size-3" />
                                                    <flux:icon name="arrow-up" class="size-3" />
                                                </span>
                                            </flux:button>
                                            <flux:button
                                                wire:click="openLibraryDrawer({{ $index + 1 }})"
                                                variant="ghost"
                                                size="sm"
                                                title="Insert row below"
                                                class="px-1!"
                                                :loading="false"
                                            >
                                                <span class="inline-flex items-center">
                                                    <flux:icon name="plus" class="size-3" />
                                                    <flux:icon name="arrow-down" class="size-3" />
                                                </span>
                                            </flux:button>
                                            <flux:button
                                                wire:click="removeRow({{ $index }})"
                                                wire:confirm="Remove this row from the page?"
                                                variant="ghost"
                                                size="sm"
                                                icon="trash"
                                                class="text-red-500 dark:text-red-400"
                                                title="Remove row"
                                                :loading="false"
                                            />
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-zinc-400 dark:text-zinc-500">
                                    <flux:icon name="squares-2x2" class="size-10 mx-auto mb-2 opacity-40" />
                                    <p class="text-xs">No rows yet.</p>
                                </div>
                            @endforelse

                            {{-- Append row at end --}}
                            <button
                                wire:click="openLibraryDrawer({{ count($rows) }})"
                                class="w-full py-3 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg text-sm text-zinc-500 dark:text-zinc-400 hover:border-primary hover:text-primary transition-colors flex items-center justify-center gap-2"
                            >
                                <flux:icon name="plus" class="size-4" />
                                {{ __('Add Row') }}
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Left panel: iframe preview --}}
                <div class="flex-1 flex flex-col bg-zinc-100 dark:bg-zinc-950 overflow-auto order-first">
@if ($previewUrl)
                        <div
                            class="flex-1 flex flex-col mx-auto w-full transition-all duration-300"
                            :style="previewWidth ? 'max-width: ' + previewWidth : 'max-width: 100%'"
                        >
                            <iframe
                                wire:ignore
                                id="page-preview"
                                class="flex-1 w-full border-0"
                                x-init="$el.src = {{ Js::from($previewUrl) }}"
                                x-on:refresh-preview.window="$el.src = $event.detail.url + '?_=' + Date.now()"
                            ></iframe>
                        </div>
                    @else
                        <div class="flex-1 flex items-center justify-center text-zinc-400 dark:text-zinc-600">
                            <div class="text-center">
                                <flux:icon name="eye-slash" class="size-12 mx-auto mb-3 opacity-40" />
                                <p class="text-sm">No preview available for this page.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- SEO / Page Settings modal --}}
    <flux:modal wire:model="showSeoModal" class="w-full max-w-lg">
        <flux:heading size="lg">Page Settings</flux:heading>

        <div class="mt-6 space-y-4">
            @if (preg_match('#^pages/⚡[^/]+\.blade\.php$#u', $file))
                {{-- Slug --}}
                <div>
                    <flux:field>
                        <flux:label>Slug</flux:label>
                        <flux:input wire:model.live.debounce.300ms="pageSlug" placeholder="my-page-slug" />
                        <flux:description>URL path: /{{ $pageSlug ?: '…' }}</flux:description>
                        <flux:error name="pageSlug" />
                    </flux:field>
                </div>

                @if ($originalPageSlug && $pageSlug !== $originalPageSlug)
                    {{-- Redirect from old slug --}}
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 space-y-3">
                        <flux:switch
                            label="Redirect /{{ $originalPageSlug }} to /{{ $pageSlug ?: '…' }}"
                            description="Forward visitors from the old URL to the new one."
                            wire:model="createSlugRedirect"
                        />

                        @if ($createSlugRedirect)
                            <flux:field>
                                <flux:label>Redirect type</flux:label>
                                <flux:select wire:model="slugRedirectType">
                                    <flux:select.option value="301">301 — Permanent</flux:select.option>
                                    <flux:select.option value="302">302 — Temporary</flux:select.option>
                                </flux:select>
                                <flux:description>Use 301 for permanent moves. Use 302 for temporary redirects.</flux:description>
                            </flux:field>
                        @endif
                    </div>
                @endif

                {{-- Cache --}}
                <div>
                    <flux:switch
                        label="Cache response"
                        description="Full-page cache this page for 1 hour for unauthenticated visitors. Disable for pages with dynamic or user-specific content."
                        wire:model="isCachedPage"
                    />
                </div>
            @endif

            {{-- Visibility / Status --}}
            <div>
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="pageStatus">
                        <flux:select.option value="draft">Draft</flux:select.option>
                        <flux:select.option value="published">Published</flux:select.option>
                        <flux:select.option value="unlisted">Unlisted</flux:select.option>
                        <flux:select.option value="unpublished">Unpublished</flux:select.option>
                    </flux:select>
                    <div x-data>
                        <flux:description x-show="$wire.pageStatus === 'draft'">Saved but not yet visible to the public.</flux:description>
                        <flux:description x-show="$wire.pageStatus === 'published'">Live and visible to all visitors.</flux:description>
                        <flux:description x-show="$wire.pageStatus === 'unlisted'">Accessible via direct link, but excluded from auto-generated navigation.</flux:description>
                        <flux:description x-show="$wire.pageStatus === 'unpublished'">Removed from public access — visitors will see a 404.</flux:description>
                    </div>
                    <flux:error name="pageStatus" />
                </flux:field>
            </div>

            {{-- SEO --}}
            <div x-data="{ seoOpen: false }" class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <button
                    type="button"
                    @click="seoOpen = !seoOpen"
                    class="w-full flex items-center justify-between text-left"
                >
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-200">SEO</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Page title, meta description, indexing, and social sharing.</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-600 dark:text-zinc-300 transition-transform duration-200 shrink-0 ml-3" :class="seoOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="seoOpen" x-transition class="mt-4 space-y-4">
                    <flux:input
                        label="Page Title"
                        wire:model="seoTitle"
                        description="Shown in the browser tab and search engine results."
                    />

                    <flux:textarea
                        label="Meta Description"
                        wire:model="seoDescription"
                        rows="3"
                        description="A short summary of the page for search engines (150–160 characters recommended)."
                    />

                    <flux:switch
                        label="No Index"
                        description="Prevent search engines from indexing this page."
                        wire:model="seoNoindex"
                    />

                    <flux:field>
                        <flux:label>OG Image URL</flux:label>
                        <flux:input wire:model="seoOgImage" type="url" placeholder="https://example.com/image.jpg" />
                        <flux:description>Paste a full URL to a 1200×630px image for social sharing previews.</flux:description>
                        <flux:error name="seoOgImage" />
                    </flux:field>
                </div>
            </div>

            {{-- Redirect --}}
            <div x-data="{ redirectOpen: false }" class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <button
                    type="button"
                    @click="redirectOpen = !redirectOpen"
                    class="w-full flex items-center justify-between text-left"
                >
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-200">Redirect</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Forward visitors to another URL when this page is loaded.</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-600 dark:text-zinc-300 transition-transform duration-200 shrink-0 ml-3" :class="redirectOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="redirectOpen" x-transition class="mt-4 space-y-3">
                    <flux:input
                        label="Redirect URL"
                        wire:model="redirectUrl"
                        type="url"
                        placeholder="https://example.com/new-page"
                        description="Leave blank to disable. When set, visitors will be forwarded here instead of seeing this page."
                    />
                    <flux:field>
                        <flux:label>Redirect Type</flux:label>
                        <flux:select wire:model="redirectType">
                            <flux:select.option value="301">301 — Permanent</flux:select.option>
                            <flux:select.option value="302">302 — Temporary</flux:select.option>
                        </flux:select>
                        <flux:description>Use 301 for permanent moves (search engines will update their index). Use 302 for temporary redirects.</flux:description>
                    </flux:field>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button variant="primary" wire:click="saveSeoSettings">Save</flux:button>
        </div>
    </flux:modal>
</div>
