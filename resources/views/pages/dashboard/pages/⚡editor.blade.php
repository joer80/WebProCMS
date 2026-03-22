<?php

use App\Enums\ContentType;
use App\Enums\RowCategory;
use App\Jobs\IndexDesignLibraryJob;
use App\Models\ContentOverride;
use App\Models\DesignPage;
use App\Models\DesignRow;
use App\Models\MediaItem;
use App\Models\SharedRow;
use App\Support\DesignLibraryService;
use App\Support\LayoutService;
use App\Support\RowItemLibrary;
use App\Support\SchemaCache;
use App\Support\VoltFileService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\ResponseCache\Facades\ResponseCache;

new #[Layout('layouts.editor')] #[Title('Page Editor')] class extends Component
{
    use WithFileUploads;

    #[Url]
    public string $file = '';

    #[Url]
    public string $previewContext = '';

    /** @var array<string, string> value => label for the "Preview as" dropdown */
    public array $previewContextOptions = [];

    public string $phpSection = '';

    /** @var array<int, array{slug: string, name: string, blade: string}> */
    public array $rows = [];

    public bool $isDirty = false;

    /** @var array<int, array<int, array{slug: string, name: string, blade: string}>> */
    public array $rowHistory = [];

    public int $historyIndex = -1;

    public int $savedHistoryIndex = -1;

    public string $previewUrl = '';

    public string $liveUrl = '';

    public bool $showLibraryDrawer = false;

    public string $libraryTab = 'rows';

    public string $librarySearch = '';

    public string $libraryCategory = 'content';

    public string $libraryPageCategory = '';

    public ?int $insertAtIndex = null;

    public bool $showSeoModal = false;

    public string $pageSlug = '';

    public string $originalPageSlug = '';

    public bool $createSlugRedirect = false;

    public string $slugRedirectType = '301';

    public bool $isCachedPage = true;

    public string $pageName = '';

    public string $seoTitle = '';

    public string $seoDescription = '';

    public bool $seoNoindex = false;

    public string $seoOgImage = '';

    public string $redirectUrl = '';

    public string $redirectType = '301';

    public bool $requiresLogin = false;

    public string $requiredRole = '';

    #[Validate('required|in:draft,published,unlisted,unpublished')]
    public string $pageStatus = 'published';

    public bool $altRowsEnabled = true;

    // Content editor state
    public bool $showContentEditor = false;

    public bool $showItemPicker = false;

    public ?int $insertAtItemIndex = null;

    public ?int $editingRowIndex = null;

    /** @var array<int, array{slug: string, key: string, type: string, default: string, label: string, group: string}> */
    public array $contentFields = [];

    /** @var array<string, string|bool> */
    public array $contentValues = [];

    /** @var array<string, string|bool> */
    public array $originalContentValues = [];

    public mixed $pendingImageUpload = null;

    public string $pendingImageKey = '';

    public bool $showMediaPicker = false;

    public string $mediaPickerKey = '';

    public string $mediaPickerCategorySlug = '';

    public string $pendingGridItemFieldKey = '';

    public int $pendingGridItemIndex = 0;

    public ?int $pendingRemoveRowIndex = null;

    public string $pendingGridItemSubKey = '';

    public bool $showGalleryPicker = false;

    public string $pendingGalleryFieldKey = '';

    /** @var array<string, array<string, string>> */
    public array $rowDesignValues = [];

    /** @var array<string, array<string, string>> */
    public array $rowDesignDefaults = [];

    // Library browse mode state — keyed by row slug
    /** @var array<string, array{category: string, position: int, rowOptions: array<int, array{id: int, name: string}>, categoryOptions: array<int, array{value: string, label: string}>}> */
    public array $rowBrowseData = [];

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
        $draftValue = $field['type'] === 'toggle'
            ? ($raw ? '1' : '0')
            : ($field['type'] === 'classes' && (string) $raw === $field['default'] ? '' : (string) $raw);

        session()->put('editor_draft_overrides.'.$field['slug'].':'.$key, ['type' => $field['type'], 'value' => $draftValue]);

        $this->refreshPreview();
    }

    public function updatedRowDesignValues(mixed $value, string $key): void
    {
        $parts = explode('.', $key, 2);

        if (count($parts) !== 2) {
            return;
        }

        [$slug, $fieldKey] = $parts;
        $default = $this->rowDesignDefaults[$slug][$fieldKey] ?? '';
        $storeValue = (string) $value === $default ? '' : (string) $value;

        $drafts = session('editor_draft_overrides', []);
        $textKeys = ['section_id', 'section_animation', 'section_animation_delay', 'section_bg_position', 'section_bg_size', 'section_bg_repeat'];
        $type = in_array($fieldKey, $textKeys, true) ? 'text' : 'classes';
        $drafts[$slug.':'.$fieldKey] = ['type' => $type, 'value' => $storeValue];
        session(['editor_draft_overrides' => $drafts]);

        $this->isDirty = true;
        $this->refreshPreview();
    }

    public function resetRowDesignField(string $slug, string $fieldKey): void
    {
        $default = $this->rowDesignDefaults[$slug][$fieldKey] ?? '';
        $this->rowDesignValues[$slug][$fieldKey] = $default;

        $drafts = session('editor_draft_overrides', []);
        $textKeys = ['section_id', 'section_animation', 'section_animation_delay', 'section_bg_position', 'section_bg_size', 'section_bg_repeat'];
        $resetType = $fieldKey === 'section_bg_image' ? 'image' : (in_array($fieldKey, $textKeys, true) ? 'text' : 'classes');
        $drafts[$slug.':'.$fieldKey] = ['type' => $resetType, 'value' => ''];
        session(['editor_draft_overrides' => $drafts]);

        $this->isDirty = true;
        $this->refreshPreview();
    }

    /**
     * @param  array<string>  $usedBlockNames  Block names already claimed by other rows (passed by ref so applyAutoBemAllRows can track state).
     */
    public function applyAutoBem(int $index, array &$usedBlockNames = []): void
    {
        $row = $this->rows[$index];
        $slug = $row['slug'];

        $existingId = $this->rowDesignValues[$slug]['section_id'] ?? '';
        $baseBlockName = $existingId ?: Str::slug($row['name']);

        // When called for a single row, collect section_ids already in use on other rows
        // so we can avoid generating duplicate block names.
        if (empty($usedBlockNames)) {
            $drafts = session('editor_draft_overrides', []);
            foreach ($this->rows as $i => $r) {
                if ($i === $index) {
                    continue;
                }
                $otherId = $drafts[$r['slug'].':section_id']['value']
                    ?? $this->rowDesignValues[$r['slug']]['section_id']
                    ?? '';
                if ($otherId !== '') {
                    $usedBlockNames[] = $otherId;
                }
            }
        }

        // Ensure the block name is unique — append -2, -3, … as needed
        $blockName = $baseBlockName;
        $counter = 2;
        while (in_array($blockName, $usedBlockNames, true)) {
            $blockName = $baseBlockName.'-'.$counter;
            $counter++;
        }
        $usedBlockNames[] = $blockName;

        $drafts = session('editor_draft_overrides', []);

        $this->rowDesignValues[$slug]['section_id'] = $blockName;
        $drafts[$slug.':section_id'] = ['type' => 'text', 'value' => $blockName];

        // Find every _id type field registered in this row and assign BEM element IDs
        $allFields = $this->parseContentFields($row['blade'], $slug);

        foreach ($allFields as $field) {
            if ($field['type'] !== 'id') {
                continue;
            }

            // Strip _id suffix and replace underscores with hyphens to get the element name
            $elementName = str_replace('_', '-', preg_replace('/_id$/', '', $field['key']));
            $bemId = $blockName.'__'.$elementName;

            $drafts[$field['slug'].':'.$field['key']] = ['type' => 'text', 'value' => $bemId];

            // Keep contentValues in sync if this row's editor is currently open
            if ($this->editingRowIndex === $index && array_key_exists($field['key'], $this->contentValues)) {
                $this->contentValues[$field['key']] = $bemId;
            }
        }

        session(['editor_draft_overrides' => $drafts]);

        $this->isDirty = true;
        $this->refreshPreview();
    }

    public function applyAutoBemAllRows(): void
    {
        $usedBlockNames = [];
        foreach (array_keys($this->rows) as $index) {
            $this->applyAutoBem($index, $usedBlockNames);
        }
    }

    public function toggleNoAltRow(string $slug): void
    {
        $current = $this->rowDesignValues[$slug]['section_no_alt'] ?? '';
        $newValue = $current === '1' ? '' : '1';

        $this->rowDesignValues[$slug]['section_no_alt'] = $newValue;

        $drafts = session('editor_draft_overrides', []);
        $drafts[$slug.':section_no_alt'] = ['type' => 'toggle', 'value' => $newValue];
        session(['editor_draft_overrides' => $drafts]);

        $this->isDirty = true;
        $this->syncAltRowClasses();
        $this->refreshPreview();
    }

    public function updatedAltRowsEnabled(): void
    {
        $this->syncAltRowClasses();
        $this->refreshPreview();
    }

    #[Computed]
    public function isLayoutPartial(): bool
    {
        return str_contains($this->file ?? '', 'layouts/partials/');
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

    /** @return \Illuminate\Database\Eloquent\Collection<int, SharedRow> */
    #[Computed]
    public function sharedLibraryRows(): \Illuminate\Database\Eloquent\Collection
    {
        return SharedRow::query()->orderBy('name')->get();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, DesignPage> */
    #[Computed]
    public function libraryPages(): \Illuminate\Database\Eloquent\Collection
    {
        return DesignPage::query()
            ->when($this->librarySearch, fn ($q) => $q->where('name', 'like', '%'.$this->librarySearch.'%'))
            ->when($this->libraryPageCategory, fn ($q) => $q->where('website_category', $this->libraryPageCategory))
            ->orderBy('website_category')
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

    /** @return array<string, string> */
    #[Computed]
    public function pageCategories(): array
    {
        return collect(\App\Enums\PageCategory::cases())
            ->mapWithKeys(fn (\App\Enums\PageCategory $c) => [$c->value => $c->label()])
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
        $this->parseSeoFromPhpSection();
        $this->loadRowDesignValues();
        $this->isDirty = false;
        $this->pageSlug = preg_match('#^pages/⚡([^/]+)\.blade\.php$#u', $relativePath, $m) ? $m[1] : '';
        $this->originalPageSlug = $this->pageSlug;
        $this->createSlugRedirect = false;
        $this->slugRedirectType = '301';

        $routeKey = $this->pageSlug ?: $service->getRoutePathForFile($relativePath);

        if ($routeKey) {
            $this->requiresLogin = $service->isAuthRoute($routeKey);

            if ($this->requiresLogin) {
                $this->isCachedPage = $service->isAuthRouteCached($routeKey);
                $this->requiredRole = $service->getRouteAuthRole($routeKey);
            } else {
                $this->isCachedPage = $service->isRouteCached($routeKey);
                $this->requiredRole = '';
            }
        } else {
            $this->requiresLogin = false;
            $this->isCachedPage = true;
            $this->requiredRole = '';
        }
        $this->rowHistory = [$this->rows];
        $this->historyIndex = 0;
        $this->savedHistoryIndex = 0;
        $this->liveUrl = $service->getRouteForFile($relativePath);
        $this->previewUrl = route('design-library.preview', ['token' => $service->previewToken($relativePath)]);

        $this->showContentEditor = false;
        $this->editingRowIndex = null;

        $this->loadPreviewContextOptions();
        $this->refreshPreview();
    }

    public function updatedPreviewContext(): void
    {
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
        $this->pushHistory();
        $this->isDirty = true;
        $this->syncAltRowClasses();

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
        $this->pushHistory();
        $this->isDirty = true;
        $this->syncAltRowClasses();

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
        $this->pushHistory();
        $this->isDirty = true;
        $this->syncAltRowClasses();

        $this->refreshPreview();
    }

    public function toggleRowVisibility(int $index): void
    {
        $this->rows[$index]['hidden'] = empty($this->rows[$index]['hidden']) ? true : false;
        $this->pushHistory();
        $this->isDirty = true;

        $this->refreshPreview();
    }

    public function renameRow(int $index, string $name): void
    {
        $trimmed = trim($name);
        if ($trimmed === '' || $trimmed === ($this->rows[$index]['name'] ?? '')) {
            return;
        }

        $this->rows[$index]['name'] = $trimmed;
        $this->pushHistory();
        $this->isDirty = true;
    }

    public function removeRow(int $index): void
    {
        $slug = $this->rows[$index]['slug'] ?? null;
        $isShared = ! empty($this->rows[$index]['shared']);

        if ($slug) {
            if ($this->phpSection) {
                $this->phpSection = (new VoltFileService)->removePhpCode($this->phpSection, $slug);
            }

            if (! $isShared) {
                ContentOverride::query()->where('row_slug', $slug)->delete();
            }
        }

        array_splice($this->rows, $index, 1);
        $this->rows = array_values($this->rows);
        $this->pushHistory();
        $this->loadRowDesignValues();
        $this->isDirty = true;

        if ($this->editingRowIndex === $index) {
            $this->showContentEditor = false;
            $this->editingRowIndex = null;
        }

        $this->refreshPreview();
    }

    public function removeAllRows(): void
    {
        foreach ($this->rows as $row) {
            $slug = $row['slug'] ?? null;
            $isShared = ! empty($row['shared']);

            if ($slug) {
                if ($this->phpSection) {
                    $this->phpSection = (new VoltFileService)->removePhpCode($this->phpSection, $slug);
                }

                if (! $isShared) {
                    ContentOverride::query()->where('row_slug', $slug)->delete();
                }
            }
        }

        $this->rows = [];
        $this->pushHistory();
        $this->loadRowDesignValues();
        $this->isDirty = true;
        $this->showContentEditor = false;
        $this->editingRowIndex = null;
        $this->refreshPreview();
        $this->dispatch('notify', message: 'All rows removed.');
    }

    public function pasteAllRows(array $rows): void
    {
        $newRows = [];

        foreach ($rows as $row) {
            $oldSlug = $row['slug'] ?? '';
            $templateName = Str::before($oldSlug, ':');
            $newSlug = $templateName.':'.Str::random(6);

            $overrides = ContentOverride::query()->where('row_slug', $oldSlug)->get();

            foreach ($overrides as $override) {
                ContentOverride::query()->create([
                    'row_slug' => $newSlug,
                    'page_slug' => null,
                    'key' => $override->key,
                    'type' => $override->type->value,
                    'value' => $override->value,
                ]);
            }

            $newRows[] = [
                'slug' => $newSlug,
                'name' => $row['name'] ?? '',
                'blade' => str_replace($oldSlug, $newSlug, $row['blade'] ?? ''),
                'shared' => $row['shared'] ?? false,
                'hidden' => $row['hidden'] ?? false,
            ];
        }

        $this->rows = array_merge($this->rows, $newRows);
        $this->pushHistory();
        $this->loadRowDesignValues();
        $this->isDirty = true;
        $this->showContentEditor = false;
        $this->editingRowIndex = null;
        $this->refreshPreview();
        $this->dispatch('notify', message: 'Rows pasted.');
    }

    public function pasteSingleRow(array $row): void
    {
        $oldSlug = $row['slug'] ?? '';
        $templateName = Str::before($oldSlug, ':');
        $newSlug = $templateName.':'.Str::random(6);

        $overrides = ContentOverride::query()->where('row_slug', $oldSlug)->get();

        foreach ($overrides as $override) {
            ContentOverride::query()->create([
                'row_slug' => $newSlug,
                'page_slug' => null,
                'key' => $override->key,
                'type' => $override->type->value,
                'value' => $override->value,
            ]);
        }

        $this->rows[] = [
            'slug' => $newSlug,
            'name' => $row['name'] ?? '',
            'blade' => str_replace($oldSlug, $newSlug, $row['blade'] ?? ''),
            'shared' => false,
            'hidden' => $row['hidden'] ?? false,
        ];
        $this->rows = array_values($this->rows);
        $this->pushHistory();
        $this->loadRowDesignValues();
        $this->isDirty = true;
        $this->showContentEditor = false;
        $this->editingRowIndex = null;
        $this->refreshPreview();
        $this->dispatch('notify', message: 'Row pasted.');
    }

    public function openLibraryDrawer(int $atIndex): void
    {
        IndexDesignLibraryJob::dispatchSync();

        $this->insertAtIndex = $atIndex;
        $this->libraryTab = 'rows';
        $this->librarySearch = '';
        $this->libraryCategory = 'content';
        $this->libraryPageCategory = '';
        $this->showLibraryDrawer = true;
        unset($this->libraryRows, $this->libraryPages);
    }

    public function switchLibraryTab(string $tab): void
    {
        $this->libraryTab = $tab;
        $this->librarySearch = '';
        $this->libraryCategory = 'content';
        $this->libraryPageCategory = '';
        unset($this->libraryRows, $this->libraryPages);
    }

    public function openBrowseMode(int $rowIndex): void
    {
        $this->browsingRowIndex = $rowIndex;
        $slug = $this->rows[$rowIndex]['slug'];
        $this->rowBrowseData[$slug] = $this->buildBrowseDataForSlug($slug);
    }

    public function openAllBrowseMode(): void
    {
        foreach ($this->rows as $row) {
            $this->rowBrowseData[$row['slug']] = $this->buildBrowseDataForSlug($row['slug']);
        }
    }

    private function buildBrowseDataForSlug(string $slug): array
    {
        $templateName = Str::before($slug, ':');

        $designRow = DesignRow::query()
            ->where('source_file', 'LIKE', '%/'.$templateName.'.blade.php')
            ->first();

        $category = $designRow?->category?->value ?? RowCategory::cases()[0]->value;

        $categoryOptions = DesignRow::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->map(fn (mixed $cat) => [
                'value' => $cat instanceof RowCategory ? $cat->value : (string) $cat,
                'label' => $cat instanceof RowCategory ? $cat->label() : ucwords(str_replace('-', ' ', (string) $cat)),
            ])
            ->values()
            ->toArray();

        $rowOptions = DesignRow::query()
            ->where('category', $category)
            ->orderBy('sort_order')
            ->get(['id', 'name'])
            ->map(fn (DesignRow $r) => ['id' => $r->id, 'name' => $r->name])
            ->values()
            ->toArray();

        $position = 0;

        if ($designRow) {
            $ids = array_column($rowOptions, 'id');
            $pos = array_search($designRow->id, $ids, true);
            $position = $pos !== false ? (int) $pos : 0;
        }

        return compact('category', 'categoryOptions', 'rowOptions', 'position');
    }

    public function browseCategoryChange(string $slug, string $category): void
    {
        $rowOptions = DesignRow::query()
            ->where('category', $category)
            ->orderBy('sort_order')
            ->get(['id', 'name'])
            ->map(fn (DesignRow $r) => ['id' => $r->id, 'name' => $r->name])
            ->values()
            ->toArray();

        $this->rowBrowseData[$slug]['category'] = $category;
        $this->rowBrowseData[$slug]['position'] = 0;
        $this->rowBrowseData[$slug]['rowOptions'] = $rowOptions;

        if (! empty($rowOptions)) {
            $this->applyBrowseRow($slug, $rowOptions[0]['id']);
        }
    }

    public function browseRowStep(string $slug, int $step): void
    {
        $options = $this->rowBrowseData[$slug]['rowOptions'] ?? [];
        $count = count($options);

        if ($count === 0) {
            return;
        }

        $position = (($this->rowBrowseData[$slug]['position'] ?? 0) + $step + $count) % $count;
        $this->rowBrowseData[$slug]['position'] = $position;
        $this->applyBrowseRow($slug, $options[$position]['id']);
    }

    public function browseRowJump(string $slug, int $rowId): void
    {
        $options = $this->rowBrowseData[$slug]['rowOptions'] ?? [];
        $position = array_search($rowId, array_column($options, 'id'), true);

        if ($position === false) {
            return;
        }

        $this->rowBrowseData[$slug]['position'] = (int) $position;
        $this->applyBrowseRow($slug, $rowId);
    }

    public function reloadRowFromLibrary(string $slug): void
    {
        $templateName = explode(':', $slug, 2)[0];

        $designRow = DesignRow::query()
            ->where('source_file', 'like', "%/{$templateName}.blade.php")
            ->orWhere('source_file', "{$templateName}.blade.php")
            ->first();

        if (! $designRow) {
            return;
        }

        $this->applyBrowseRow($slug, $designRow->id);
    }

    private function applyBrowseRow(string $slug, int $designRowId): void
    {
        $rowIndex = collect($this->rows)->search(fn (array $r) => $r['slug'] === $slug);

        if ($rowIndex === false) {
            return;
        }

        $designRow = DesignRow::query()->find($designRowId);

        if (! $designRow) {
            return;
        }

        $existing = $this->rows[$rowIndex];

        $this->rows[$rowIndex] = [
            'slug' => $slug,
            'name' => $designRow->name,
            'blade' => str_replace('__SLUG__', $slug, $designRow->bladeCodeFromFile()),
            'shared' => $existing['shared'] ?? false,
            'hidden' => $existing['hidden'] ?? false,
            // Track which design library template is active so saveFile can
            // sync active_header / active_footer without changing the slug.
            'template' => basename($designRow->source_file, '.blade.php'),
        ];

        $this->pushHistory();
        $this->loadRowDesignValues();
        $this->isDirty = true;
        $this->refreshPreview();
    }

    public function insertRow(int $designRowId, int $atIndex): void
    {
        $designRow = DesignRow::query()->find($designRowId);

        if (! $designRow) {
            return;
        }

        $slug = basename($designRow->source_file, '.blade.php').':'.Str::random(6);
        $newRow = [
            'slug' => $slug,
            'name' => $designRow->name,
            'blade' => str_replace('__SLUG__', $slug, $designRow->bladeCodeFromFile()),
        ];

        $phpCode = $designRow->phpCodeFromFile();
        if ($phpCode && $this->phpSection) {
            $this->phpSection = (new VoltFileService)->injectPhpCode(
                $this->phpSection,
                $phpCode,
                $slug
            );
        }

        array_splice($this->rows, $atIndex, 0, [$newRow]);
        $this->rows = array_values($this->rows);
        $this->pushHistory();
        $this->loadRowDesignValues();
        $this->isDirty = true;
        $this->showLibraryDrawer = false;

        $this->refreshPreview();
    }

    public function insertPageBundle(int $designPageId, int $atIndex): void
    {
        $page = DesignPage::query()->find($designPageId);

        if (! $page || empty($page->row_names)) {
            return;
        }

        $offset = 0;
        foreach ($page->row_names as $templateName) {
            $row = DesignRow::query()
                ->where('source_file', 'like', '%/'.$templateName.'.blade.php')
                ->first();

            if ($row) {
                $this->insertRow($row->id, $atIndex + $offset);
                $offset++;
            }
        }

        $this->showLibraryDrawer = false;
        $this->dispatch('notify', message: $offset.' rows inserted from "'.$page->name.'".');
    }

    public function makeRowShared(int $index): void
    {
        $row = $this->rows[$index];
        $slug = $row['slug'];
        $service = new VoltFileService;

        $service->makeRowShared($slug, $row['name'], $row['blade']);

        ContentOverride::query()->where('row_slug', $slug)->update(['page_slug' => null]);

        $filename = str_replace(':', '-', $slug);
        $this->rows[$index]['blade'] = "@include('shared-rows.{$filename}')";
        $this->rows[$index]['shared'] = true;
        $this->pushHistory();

        $this->isDirty = true;
        $this->refreshPreview();
        $this->dispatch('notify', message: 'Row is now shared.');
    }

    public function insertSharedRow(string $slug, int $atIndex): void
    {
        $sharedRow = SharedRow::query()->where('slug', $slug)->first();

        if (! $sharedRow) {
            return;
        }

        $filename = str_replace(':', '-', $slug);
        $newRow = [
            'slug' => $slug,
            'name' => $sharedRow->name,
            'blade' => "@include('shared-rows.{$filename}')",
            'shared' => true,
        ];

        array_splice($this->rows, $atIndex, 0, [$newRow]);
        $this->rows = array_values($this->rows);
        $this->pushHistory();
        $this->loadRowDesignValues();
        $this->isDirty = true;
        $this->showLibraryDrawer = false;

        $this->refreshPreview();
    }

    public function openContentEditor(int $index): void
    {
        $this->editingRowIndex = $index;
        $row = $this->rows[$index];
        $this->contentFields = array_values(array_filter(
            $this->parseContentFields($row['blade'], $row['slug']),
            fn ($f) => ! in_array($f['key'], ['section_classes', 'section_container_classes', 'section_id', 'section_attrs', 'section_animation', 'section_animation_delay', 'section_bg_image', 'section_bg_position', 'section_bg_size', 'section_bg_repeat'], true)
        ));
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

            // Alt text belongs to the image, not the page. Always read the live value from
            // the MediaItem so edits in the media library are immediately reflected here.
            if ($field['type'] === 'text' && str_ends_with($field['key'], '_alt')) {
                $imageDbKey = $field['slug'].':'.substr($field['key'], 0, -4);
                $imagePath = $drafts[$imageDbKey]['value'] ?? $overrides->get($imageDbKey)?->value;
                if ($imagePath) {
                    $liveAlt = MediaItem::query()->where('path', $imagePath)->value('alt');
                    if ($liveAlt !== null) {
                        $dbValue = $liveAlt;
                    }
                }
            }

            // originalContentValues always reflects the last-saved DB state
            $this->originalContentValues[$field['key']] = $field['type'] === 'toggle'
                ? ($dbValue !== null ? $dbValue === '1' : $field['default'] === '1')
                : (in_array($field['type'], ['classes', 'grid'], true) ? ($dbValue ?: $field['default']) : ($dbValue ?? ''));

            // contentValues prefers any unsaved session draft
            $draft = $drafts[$dbKey] ?? null;
            $rawValue = $draft !== null ? ($draft['value'] ?? null) : $dbValue;
            $this->contentValues[$field['key']] = $field['type'] === 'toggle'
                ? ($rawValue !== null ? $rawValue === '1' : $field['default'] === '1')
                : (in_array($field['type'], ['classes', 'grid'], true) ? ($rawValue ?: $field['default']) : ($rawValue ?? ''));
        }

        $this->showContentEditor = true;
        $this->dispatch('content-editor-opened');
    }

    public function openItemPicker(): void
    {
        $this->insertAtItemIndex = null;
        $this->showItemPicker = true;
    }

    public function openItemPickerAbove(int $itemIndex): void
    {
        $this->insertAtItemIndex = $itemIndex;
        $this->showItemPicker = true;
    }

    public function openItemPickerBelow(int $itemIndex): void
    {
        $this->insertAtItemIndex = $itemIndex + 1;
        $this->showItemPicker = true;
    }

    public function addItemToRow(string $itemKey): void
    {
        $items = RowItemLibrary::items();

        if (! isset($items[$itemKey]) || $this->editingRowIndex === null) {
            return;
        }

        $item = $items[$itemKey];
        $slug = $this->rows[$this->editingRowIndex]['slug'];
        $blade = $this->rows[$this->editingRowIndex]['blade'];

        $snippet = $item['blade'];
        $prefix = $itemKey;

        if (isset($item['prefix'])) {
            $prefix = $this->findUniquePrefix($blade, $item['prefix']);
            $snippet = str_replace('__PREFIX__', $prefix, $snippet);
        }
        $snippet = str_replace('__SLUG__', $slug, $snippet);

        $marked = "{{-- @dl-item:{$itemKey}:{$prefix}:{$item['name']} --}}\n{$snippet}\n{{-- /@dl-item --}}";

        if ($this->insertAtItemIndex !== null) {
            $blocks = $this->extractItemBlocks($blade);
            if (isset($blocks[$this->insertAtItemIndex])) {
                $targetFull = $blocks[$this->insertAtItemIndex]['full'];
                $this->rows[$this->editingRowIndex]['blade'] = str_replace(
                    "\n".$targetFull,
                    "\n".$marked."\n".$targetFull,
                    $blade
                );
            } else {
                $this->rows[$this->editingRowIndex]['blade'] = str_replace(
                    '</x-dl.section>',
                    "\n".$marked."\n</x-dl.section>",
                    $blade
                );
            }
            $this->insertAtItemIndex = null;
        } else {
            $this->rows[$this->editingRowIndex]['blade'] = str_replace(
                '</x-dl.section>',
                "\n".$marked."\n</x-dl.section>",
                $blade
            );
        }

        $this->showItemPicker = false;
        $this->isDirty = true;
        $this->pushHistory();
        $this->openContentEditor($this->editingRowIndex);
        $this->refreshPreview();
    }

    private function findUniquePrefix(string $blade, string $desiredPrefix): string
    {
        preg_match_all('/\bprefix=["\']([^"\']+)["\']/', $blade, $matches);
        $existingPrefixes = $matches[1];

        if (! in_array($desiredPrefix, $existingPrefixes, true)) {
            return $desiredPrefix;
        }

        $i = 2;
        while (in_array("{$desiredPrefix}_{$i}", $existingPrefixes, true)) {
            $i++;
        }

        return "{$desiredPrefix}_{$i}";
    }

    public function deleteItemFromRow(int $itemIndex): void
    {
        if ($this->editingRowIndex === null) {
            return;
        }

        $blade = $this->rows[$this->editingRowIndex]['blade'];
        $blocks = $this->extractItemBlocks($blade);

        if (! isset($blocks[$itemIndex])) {
            return;
        }

        $full = $blocks[$itemIndex]['full'];
        $blade = str_replace("\n".$full."\n", "\n", $blade);
        $blade = str_replace($full, '', $blade);

        $this->rows[$this->editingRowIndex]['blade'] = $blade;
        $this->isDirty = true;
        $this->pushHistory();
        $this->openContentEditor($this->editingRowIndex);
        $this->refreshPreview();
    }

    public function moveItemUp(int $itemIndex): void
    {
        if ($itemIndex === 0 || $this->editingRowIndex === null) {
            return;
        }

        $this->swapItems($itemIndex - 1, $itemIndex);
    }

    public function moveItemDown(int $itemIndex): void
    {
        if ($this->editingRowIndex === null) {
            return;
        }

        $blocks = $this->extractItemBlocks($this->rows[$this->editingRowIndex]['blade']);

        if ($itemIndex >= count($blocks) - 1) {
            return;
        }

        $this->swapItems($itemIndex, $itemIndex + 1);
    }

    public function reorderItems(int $from, int $to): void
    {
        if ($from === $to || $this->editingRowIndex === null) {
            return;
        }

        $blade = $this->rows[$this->editingRowIndex]['blade'];
        $blocks = $this->extractItemBlocks($blade);

        foreach ($blocks as $i => $block) {
            $blade = str_replace($block['full'], "%%DL_ITEM_{$i}%%", $blade);
        }

        $moved = array_splice($blocks, $from, 1)[0];
        array_splice($blocks, $to, 0, [$moved]);

        foreach ($blocks as $i => $block) {
            $blade = str_replace("%%DL_ITEM_{$i}%%", $block['full'], $blade);
        }

        $this->rows[$this->editingRowIndex]['blade'] = $blade;
        $this->isDirty = true;
        $this->pushHistory();
        $this->openContentEditor($this->editingRowIndex);
        $this->refreshPreview();
    }

    private function swapItems(int $indexA, int $indexB): void
    {
        $blade = $this->rows[$this->editingRowIndex]['blade'];
        $blocks = $this->extractItemBlocks($blade);

        $blade = str_replace($blocks[$indexA]['full'], '%%DL_SWAP_A%%', $blade);
        $blade = str_replace($blocks[$indexB]['full'], '%%DL_SWAP_B%%', $blade);
        $blade = str_replace('%%DL_SWAP_A%%', $blocks[$indexB]['full'], $blade);
        $blade = str_replace('%%DL_SWAP_B%%', $blocks[$indexA]['full'], $blade);

        $this->rows[$this->editingRowIndex]['blade'] = $blade;
        $this->isDirty = true;
        $this->pushHistory();
        $this->openContentEditor($this->editingRowIndex);
        $this->refreshPreview();
    }

    /**
     * @return array<int, array{full: string, type: string, prefix: string, name: string}>
     */
    public function extractItemBlocks(string $blade): array
    {
        preg_match_all(
            '/\{\{--\s*@dl-item:([a-z_-]+):([a-z_0-9]+):([^-]+?)\s*--\}\}.*?\{\{--\s*\/@dl-item\s*--\}\}/s',
            $blade,
            $matches,
            PREG_SET_ORDER
        );

        return array_values(array_map(fn ($m, $i) => [
            'index' => $i,
            'full' => $m[0],
            'type' => $m[1],
            'prefix' => $m[2],
            'name' => trim($m[3]),
        ], $matches, array_keys($matches)));
    }

    /**
     * Extract top-level x-dl.* components from a blade snippet (direct children of x-dl.section).
     * Returns components in document order with their full text and registered field keys.
     *
     * @return list<array{index: int, slug: string, name: string, full: string, fieldKeys: list<string>, openOffset: int, closeOffset: int}>
     */
    public function extractTopLevelComponentsFromBlade(string $blade): array
    {
        // section is excluded entirely (its fields are handled separately via rowDesignValues)
        // card/group/accordion-item are included in $allComps so their fields get merged into
        // the enclosing top-level component, but they cannot themselves be top-level sidebar cards
        $skipAllSlugs = ['section'];
        $skipTopLevelSlugs = ['card', 'group', 'accordion-item'];

        preg_match_all('/<x-dl\.([\w-]+)((?:"[^"]*"|'."'[^']*'".'|[^>])*)\s*\/?'.'>/', $blade, $tagMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        $allComps = [];

        foreach ($tagMatches as $tagMatch) {
            $slug = $tagMatch[1][0];

            if (in_array($slug, $skipAllSlugs, true)) {
                continue;
            }

            $className = 'App\\View\\Components\\Dl\\'.Str::studly($slug);

            if (! class_exists($className) || ! method_exists($className, 'schemaFields')) {
                continue;
            }

            $openTag = $tagMatch[0][0];
            $openOffset = $tagMatch[0][1];
            $attrsStr = $tagMatch[2][0];
            $isSelfClosing = str_contains($openTag, '/>');

            $attrs = [];
            preg_match_all('/(\w[\w-]*)=(["\'])(.*?)\2/s', $attrsStr, $attrMatches, PREG_SET_ORDER);

            foreach ($attrMatches as $am) {
                $attrs[$am[1]] = $am[3];
            }

            $fieldKeys = array_column($className::schemaFields($attrs), 'key');

            if ($isSelfClosing) {
                $closeOffset = $openOffset + strlen($openTag);
                $fullText = $openTag;
            } else {
                $closeTag = '</x-dl.'.$slug.'>';
                $openPattern = '<x-dl.'.$slug;

                // Track nesting depth so same-type nested components don't confuse strpos.
                $depth = 1;
                $searchPos = $openOffset + strlen($openTag);
                $closePos = false;

                // Helper: scan forward from $from respecting quoted strings, return position of the closing >.
                $findTagEnd = function (string $blade, int $from): int|false {
                    $len = strlen($blade);
                    $i = $from;
                    $inQuote = false;
                    $quoteChar = '';
                    while ($i < $len) {
                        $c = $blade[$i];
                        if ($inQuote) {
                            if ($c === $quoteChar) {
                                $inQuote = false;
                            }
                        } else {
                            if ($c === '"' || $c === "'") {
                                $inQuote = true;
                                $quoteChar = $c;
                            } elseif ($c === '>') {
                                return $i;
                            }
                        }
                        $i++;
                    }

                    return false;
                };

                while ($depth > 0) {
                    $nextOpen = strpos($blade, $openPattern, $searchPos);
                    $nextClose = strpos($blade, $closeTag, $searchPos);

                    if ($nextClose === false) {
                        break;
                    }

                    if ($nextOpen !== false && $nextOpen < $nextClose) {
                        // Verify it is a proper opening tag, not a longer component name.
                        $charAfter = $blade[$nextOpen + strlen($openPattern)] ?? '';
                        if ($charAfter === ' ' || $charAfter === "\n" || $charAfter === "\t" || $charAfter === '/' || $charAfter === '>') {
                            // Only increment depth for non-self-closing nested tags.
                            $nestedTagEnd = $findTagEnd($blade, $nextOpen + strlen($openPattern));
                            $nestedSelfClosing = $nestedTagEnd !== false && $blade[$nestedTagEnd - 1] === '/';
                            if (! $nestedSelfClosing) {
                                $depth++;
                            }
                        }
                        $searchPos = $nextOpen + strlen($openPattern);
                    } else {
                        $depth--;
                        if ($depth === 0) {
                            $closePos = $nextClose;
                            break;
                        }
                        $searchPos = $nextClose + strlen($closeTag);
                    }
                }

                if ($closePos === false) {
                    $closeOffset = $openOffset + strlen($openTag);
                    $fullText = $openTag;
                } else {
                    $closeOffset = $closePos + strlen($closeTag);
                    $fullText = substr($blade, $openOffset, $closeOffset - $openOffset);
                }
            }

            $allComps[] = [
                'slug' => $slug,
                'attrs' => $attrs,
                'fieldKeys' => $fieldKeys,
                'openOffset' => $openOffset,
                'closeOffset' => $closeOffset,
                'full' => $fullText,
                'skipTopLevel' => in_array($slug, $skipTopLevelSlugs, true),
            ];
        }

        // Keep only top-level components (not nested inside another component's offset range)
        $topLevel = [];

        foreach ($allComps as $i => $comp) {
            $isNested = false;

            foreach ($allComps as $j => $other) {
                if ($i === $j) {
                    continue;
                }

                if ($comp['openOffset'] > $other['openOffset'] && $comp['openOffset'] < $other['closeOffset']) {
                    $isNested = true;
                    break;
                }
            }

            if (! $isNested && ! $comp['skipTopLevel']) {
                $topLevel[] = $comp;
            }
        }

        usort($topLevel, fn ($a, $b) => $a['openOffset'] <=> $b['openOffset']);

        // Merge field keys from nested components into each top-level component
        foreach ($topLevel as &$topComp) {
            foreach ($allComps as $nested) {
                if ($nested['openOffset'] > $topComp['openOffset'] && $nested['openOffset'] < $topComp['closeOffset']) {
                    $topComp['fieldKeys'] = array_values(array_unique(array_merge($topComp['fieldKeys'], $nested['fieldKeys'])));
                }
            }
        }
        unset($topComp);

        $result = [];

        foreach ($topLevel as $i => $comp) {
            $prefix = $comp['attrs']['prefix'] ?? '';
            $name = ucwords(str_replace(['-', '_'], ' ', $prefix ?: $comp['slug']));
            $result[] = array_merge($comp, ['index' => $i, 'name' => $name]);
        }

        return $result;
    }

    public function reorderComponents(int $from, int $to): void
    {
        if ($from === $to || $this->editingRowIndex === null) {
            return;
        }

        $blade = $this->rows[$this->editingRowIndex]['blade'];
        $components = $this->extractTopLevelComponentsFromBlade($blade);

        foreach ($components as $i => $comp) {
            $blade = str_replace($comp['full'], "%%DL_COMP_{$i}%%", $blade);
        }

        $moved = array_splice($components, $from, 1)[0];
        array_splice($components, $to, 0, [$moved]);

        foreach ($components as $i => $comp) {
            $blade = str_replace("%%DL_COMP_{$i}%%", $comp['full'], $blade);
        }

        $this->rows[$this->editingRowIndex]['blade'] = $blade;
        $this->isDirty = true;
        $this->pushHistory();
        $this->openContentEditor($this->editingRowIndex);
        $this->refreshPreview();
    }

    public function moveComponentUp(int $index): void
    {
        if ($this->editingRowIndex === null || $index <= 0) {
            return;
        }

        $this->swapComponents($index, $index - 1);
    }

    public function moveComponentDown(int $index): void
    {
        if ($this->editingRowIndex === null) {
            return;
        }

        $components = $this->extractTopLevelComponentsFromBlade($this->rows[$this->editingRowIndex]['blade']);

        if ($index >= count($components) - 1) {
            return;
        }

        $this->swapComponents($index, $index + 1);
    }

    public function deleteComponent(int $componentIndex): void
    {
        if ($this->editingRowIndex === null) {
            return;
        }

        $blade = $this->rows[$this->editingRowIndex]['blade'];
        $components = $this->extractTopLevelComponentsFromBlade($blade);

        if (! isset($components[$componentIndex])) {
            return;
        }

        $full = $components[$componentIndex]['full'];
        $blade = str_replace("\n".$full."\n", "\n", $blade);
        $blade = str_replace($full, '', $blade);

        $this->rows[$this->editingRowIndex]['blade'] = $blade;
        $this->isDirty = true;
        $this->pushHistory();
        $this->openContentEditor($this->editingRowIndex);
        $this->refreshPreview();
    }

    private function swapComponents(int $indexA, int $indexB): void
    {
        $blade = $this->rows[$this->editingRowIndex]['blade'];
        $components = $this->extractTopLevelComponentsFromBlade($blade);

        $blade = str_replace($components[$indexA]['full'], '%%DL_SWAP_COMP_A%%', $blade);
        $blade = str_replace($components[$indexB]['full'], '%%DL_SWAP_COMP_B%%', $blade);
        $blade = str_replace('%%DL_SWAP_COMP_A%%', $components[$indexB]['full'], $blade);
        $blade = str_replace('%%DL_SWAP_COMP_B%%', $components[$indexA]['full'], $blade);

        $this->rows[$this->editingRowIndex]['blade'] = $blade;
        $this->isDirty = true;
        $this->pushHistory();
        $this->openContentEditor($this->editingRowIndex);
        $this->refreshPreview();
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
        $this->dispatch('content-editor-closed');
    }

    private function persistAllDraftOverrides(): void
    {
        /** @var array<string, array{type: string, value: string}> $drafts */
        $drafts = session('editor_draft_overrides', []);

        $sharedSlugs = collect($this->rows)
            ->filter(fn (array $r) => ! empty($r['shared']))
            ->pluck('slug')
            ->flip()
            ->all();

        foreach ($drafts as $draftKey => $draft) {
            $lastColon = strrpos($draftKey, ':');
            $slug = substr($draftKey, 0, $lastColon);
            $key = substr($draftKey, $lastColon + 1);
            $type = $draft['type'];
            $value = $draft['value'];

            // For image fields, capture the old path before overwriting so we can clean up
            // page-specific uploads (content-overrides/) that are being removed or replaced.
            $oldPath = $type === 'image'
                ? ContentOverride::query()->where('row_slug', $slug)->where('key', $key)->value('value')
                : null;

            // Normalize UI-only types (id, attrs, htag, url, etc.) to a valid ContentType value
            $storedType = ContentType::tryFrom($type)?->value ?? 'text';

            if ($value === '') {
                ContentOverride::query()
                    ->where('row_slug', $slug)
                    ->where('key', $key)
                    ->delete();
            } else {
                ContentOverride::updateOrCreate(
                    ['row_slug' => $slug, 'key' => $key],
                    ['type' => $storedType, 'value' => $value, 'page_slug' => isset($sharedSlugs[$slug]) ? null : ($this->pageSlug ?: null)]
                );
            }

            // Only delete files that were uploaded directly to this page (content-overrides/).
            // Media library images are managed independently and must not be deleted here.
            if ($oldPath && str_starts_with($oldPath, 'content-overrides/')) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Alt text belongs to the image. Sync any edited alt text back to the MediaItem
        // and update every other page that references the same image.
        foreach ($drafts as $draftKey => $draft) {
            $lastColon = strrpos($draftKey, ':');
            $slug = substr($draftKey, 0, $lastColon);
            $key = substr($draftKey, $lastColon + 1);

            if ($draft['type'] !== 'text' || ! str_ends_with($key, '_alt') || $draft['value'] === '') {
                continue;
            }

            $imageKey = substr($key, 0, -4);
            $imagePath = ContentOverride::query()
                ->where('row_slug', $slug)
                ->where('key', $imageKey)
                ->value('value');

            if (! $imagePath) {
                continue;
            }

            MediaItem::query()->where('path', $imagePath)->update(['alt' => $draft['value']]);

            ContentOverride::query()
                ->where('key', $imageKey)
                ->where('value', $imagePath)
                ->where('type', 'image')
                ->where('row_slug', '!=', $slug)
                ->pluck('row_slug')
                ->each(function (string $affectedSlug) use ($key, $draft): void {
                    ContentOverride::updateOrCreate(
                        ['row_slug' => $affectedSlug, 'key' => $key],
                        ['type' => 'text', 'value' => $draft['value']]
                    );
                });
        }

        session()->forget('editor_draft_overrides');

        $hasClassesChange = false;
        $syncer = new \App\Support\BladeClassSyncer;

        foreach ($drafts as $draftKey => $draft) {
            if ($draft['type'] !== 'classes' || $draft['value'] === '') {
                continue;
            }

            $hasClassesChange = true;
            $lastColon = strrpos($draftKey, ':');
            $slug = substr($draftKey, 0, $lastColon);
            $key = substr($draftKey, $lastColon + 1);

            if (isset($sharedSlugs[$slug])) {
                $filename = str_replace(':', '-', $slug);
                $bladeFile = resource_path('views/shared-rows/'.$filename.'.blade.php');
            } else {
                $bladeFile = resource_path('views/'.$this->file);
            }

            $syncer->sync($bladeFile, $slug, $key, $draft['value']);
        }

        if ($hasClassesChange && (app()->isProduction() || config('cms.rebuild_assets_locally'))) {
            defer(fn () => (new \App\Jobs\RebuildAssets)->handle());
        }
    }

    public function setPendingImageKey(string $key): void
    {
        $this->pendingImageKey = $key;
    }

    public function removeImage(string $key): void
    {
        $this->contentValues[$key] = '';

        $field = collect($this->contentFields)->firstWhere('key', $key);

        if ($field) {
            session()->put('editor_draft_overrides.'.$field['slug'].':'.$key, ['type' => 'image', 'value' => '']);
            $this->refreshPreview();
        }
    }

    public function openMediaPicker(string $key): void
    {
        $this->mediaPickerKey = $key;
        $this->mediaPickerCategorySlug = $key === 'section_bg_image' ? 'backgrounds' : '';
        $this->showMediaPicker = true;
    }

    public function openRowDesignImagePicker(string $slug): void
    {
        $this->mediaPickerKey = 'row-design-bg:'.$slug;
        $this->mediaPickerCategorySlug = 'backgrounds';
        $this->showMediaPicker = true;
    }

    public function removeRowDesignImage(string $slug): void
    {
        $this->rowDesignValues[$slug]['section_bg_image'] = '';

        $drafts = session('editor_draft_overrides', []);
        $drafts[$slug.':section_bg_image'] = ['type' => 'image', 'value' => ''];
        session(['editor_draft_overrides' => $drafts]);

        $this->isDirty = true;
        $this->refreshPreview();
    }

    public function openGridItemMediaPicker(string $fieldKey, int $idx, string $subKey): void
    {
        $this->pendingGridItemFieldKey = $fieldKey;
        $this->pendingGridItemIndex = $idx;
        $this->pendingGridItemSubKey = $subKey;
        $this->mediaPickerKey = 'grid-item';
        $this->showMediaPicker = true;
    }

    public function openGalleryPicker(string $fieldKey): void
    {
        $this->pendingGalleryFieldKey = $fieldKey;
        $this->showGalleryPicker = true;
    }

    #[On('media-images-picked')]
    public function handleMediaImagesPicked(string $key, array $images): void
    {
        $this->showGalleryPicker = false;
        $current = json_decode($this->contentValues[$key] ?? '[]', true) ?: [];

        foreach ($images as $img) {
            $current[] = ['image' => $img['path'], 'alt' => $img['alt'], 'caption' => ''];
        }

        $this->contentValues[$key] = json_encode($current);

        $field = collect($this->contentFields)->firstWhere('key', $key);
        if ($field) {
            session()->put('editor_draft_overrides.'.$field['slug'].':'.$key, ['type' => 'grid', 'value' => $this->contentValues[$key]]);
        }

        $this->dispatch('content-grid-reset', key: $key, value: $this->contentValues[$key]);
        $this->refreshPreview();
    }

    #[On('media-image-picked')]
    public function handleMediaImagePicked(string $key, string $path, string $alt = ''): void
    {
        if (str_starts_with($key, 'row-design-bg:')) {
            $slug = substr($key, 14);
            $this->rowDesignValues[$slug]['section_bg_image'] = $path;

            $drafts = session('editor_draft_overrides', []);
            $drafts[$slug.':section_bg_image'] = ['type' => 'image', 'value' => $path];
            session(['editor_draft_overrides' => $drafts]);

            $this->showMediaPicker = false;
            $this->isDirty = true;
            $this->refreshPreview();

            return;
        }

        if ($key === 'grid-item' && $this->pendingGridItemFieldKey) {
            $fieldKey = $this->pendingGridItemFieldKey;
            $items = json_decode($this->contentValues[$fieldKey] ?? '[]', true) ?: [];

            if (isset($items[$this->pendingGridItemIndex])) {
                $items[$this->pendingGridItemIndex][$this->pendingGridItemSubKey] = $path;

                if ($this->pendingGridItemSubKey === 'image' && empty($items[$this->pendingGridItemIndex]['alt'] ?? '')) {
                    $items[$this->pendingGridItemIndex]['alt'] = $alt;
                }
            }

            $this->contentValues[$fieldKey] = json_encode($items);

            $field = collect($this->contentFields)->firstWhere('key', $fieldKey);
            if ($field) {
                session()->put('editor_draft_overrides.'.$field['slug'].':'.$fieldKey, ['type' => 'grid', 'value' => $this->contentValues[$fieldKey]]);
            }

            $this->pendingGridItemFieldKey = '';
            $this->showMediaPicker = false;
            $this->dispatch('content-grid-reset', key: $fieldKey, value: $this->contentValues[$fieldKey]);
            $this->refreshPreview();

            return;
        }

        $this->contentValues[$key] = $path;
        $this->showMediaPicker = false;

        $field = collect($this->contentFields)->firstWhere('key', $key);

        if (! $field) {
            return;
        }

        session()->put('editor_draft_overrides.'.$field['slug'].':'.$field['key'], ['type' => 'image', 'value' => $path]);

        if ($alt !== '') {
            $altKey = $key.'_alt';
            $altField = collect($this->contentFields)->firstWhere('key', $altKey);
            if ($altField) {
                $this->contentValues[$altKey] = $alt;
                session()->put('editor_draft_overrides.'.$altField['slug'].':'.$altField['key'], ['type' => 'text', 'value' => $alt]);
            }
        }

        $this->refreshPreview();
    }

    public function saveFile(): void
    {
        if (! $this->file) {
            return;
        }

        $this->persistAllDraftOverrides();

        $this->resetEmptyClassesFields();

        // Delete overrides for rows that are no longer on this page.
        if ($this->pageSlug) {
            $currentSlugs = collect($this->rows)->pluck('slug')->filter()->values()->toArray();

            ContentOverride::query()
                ->where('page_slug', $this->pageSlug)
                ->when(! empty($currentSlugs), fn ($q) => $q->whereNotIn('row_slug', $currentSlugs))
                ->delete();
        }

        $rowsToWrite = $this->rows;

        // When saving a layout partial after a browse (template switched), rewrite
        // the slug in the file so it reflects the new template name. This keeps
        // the sidebar label and config in sync on the next load without changing
        // the in-memory slug (which would collapse the browse panel).
        if ($this->isLayoutPartial && ! empty($rowsToWrite) && isset($rowsToWrite[0]['template'])) {
            $firstRow = $rowsToWrite[0];
            $parts = explode(':', $firstRow['slug'], 2);
            $type = $parts[1] ?? '';
            if (in_array($type, ['header', 'footer'])) {
                $newSlug = $firstRow['template'].':'.$type;
                $rowsToWrite[0]['slug'] = $newSlug;
                $rowsToWrite[0]['blade'] = str_replace($firstRow['slug'], $newSlug, $firstRow['blade']);
                (new LayoutService)->writeConfig(["active_{$type}" => $firstRow['template']]);
            }
        } elseif ($this->isLayoutPartial && ! empty($rowsToWrite)) {
            // No browse — sync config from slug prefix (handles plain saves).
            $parts = explode(':', $rowsToWrite[0]['slug'], 2);
            $type = $parts[1] ?? '';
            if (in_array($type, ['header', 'footer'])) {
                (new LayoutService)->writeConfig(["active_{$type}" => $parts[0]]);
            }
        } else {
            // For regular page rows, rewrite slugs that changed due to template browsing.
            // $rowsToWrite is a copy so in-memory state (and browse panel) stay intact.
            foreach ($rowsToWrite as $i => $row) {
                if (! empty($row['template'])) {
                    $parts = explode(':', $row['slug'], 2);
                    $randomId = $parts[1] ?? '';
                    if ($randomId) {
                        $newSlug = $row['template'].':'.$randomId;
                        $rowsToWrite[$i]['slug'] = $newSlug;
                        $rowsToWrite[$i]['blade'] = str_replace($row['slug'], $newSlug, $row['blade']);
                    }
                }
            }
        }

        $service = new VoltFileService;
        $fullPath = resource_path('views/'.$this->file);
        $service->writeFile($fullPath, $service->buildFileContent($this->phpSection, $rowsToWrite));

        ResponseCache::clear();

        $this->isDirty = false;
        $this->savedHistoryIndex = $this->historyIndex;
        $this->dispatch('notify', message: 'Page saved.');
    }

    public function resetClassesField(string $key): void
    {
        $field = collect($this->contentFields)->firstWhere('key', $key);

        if ($field && $field['type'] === 'classes') {
            $this->contentValues[$key] = $field['default'];
            $this->updatedContentValues($field['default'], $key);
        }
    }

    public function resetContentField(string $key): void
    {
        $field = collect($this->contentFields)->firstWhere('key', $key);

        if (! $field) {
            return;
        }

        $original = $this->originalContentValues[$key] ?? ($field['type'] === 'toggle' ? false : '');
        $this->contentValues[$key] = $original;
        session()->forget('editor_draft_overrides.'.$field['slug'].':'.$key);

        if ($field['type'] === 'grid') {
            $gridJson = is_string($original) ? $original : json_encode($original);
            $this->dispatch('content-grid-reset', key: $key, value: $gridJson);
        }

        if ($field['type'] === 'attrs') {
            $attrsJson = is_string($original) ? $original : json_encode($original);
            $this->dispatch('content-attrs-reset', key: $key, value: $attrsJson);
        }

        if ($field['type'] === 'richtext') {
            $this->dispatch('content-richtext-reset', key: $key, value: (string) $original);
        }

        $this->refreshPreview();
    }

    public function clearGridItems(string $key): void
    {
        $field = collect($this->contentFields)->firstWhere('key', $key);

        if (! $field) {
            return;
        }

        $this->contentValues[$key] = '[]';
        session()->put('editor_draft_overrides.'.$field['slug'].':'.$key, ['type' => 'grid', 'value' => '[]']);
        $this->dispatch('content-grid-reset', key: $key, value: '[]');
        $this->refreshPreview();
    }

    public function resetEmptyClassesFields(): void
    {
        foreach ($this->contentFields as $field) {
            if ($field['type'] === 'classes' && ($this->contentValues[$field['key']] ?? '') === '') {
                $this->contentValues[$field['key']] = $field['default'];
            }
        }
    }

    public function discardChanges(): void
    {
        session()->forget('editor_draft_overrides');

        if ($this->file) {
            $this->loadFile($this->file);
        }
    }

    private function pushHistory(): void
    {
        if ($this->historyIndex < count($this->rowHistory) - 1) {
            $this->rowHistory = array_slice($this->rowHistory, 0, $this->historyIndex + 1);
        }

        $this->rowHistory[] = $this->rows;
        $this->historyIndex = count($this->rowHistory) - 1;

        if (count($this->rowHistory) > 20) {
            array_shift($this->rowHistory);
            $this->historyIndex = count($this->rowHistory) - 1;
        }
    }

    public function undo(): void
    {
        if ($this->historyIndex <= 0) {
            return;
        }

        $this->historyIndex--;
        $this->rows = $this->rowHistory[$this->historyIndex];
        $this->loadRowDesignValues();
        $this->isDirty = $this->historyIndex !== $this->savedHistoryIndex;
        $this->showContentEditor = false;
        $this->editingRowIndex = null;
        $this->refreshPreview();
    }

    public function redo(): void
    {
        if ($this->historyIndex >= count($this->rowHistory) - 1) {
            return;
        }

        $this->historyIndex++;
        $this->rows = $this->rowHistory[$this->historyIndex];
        $this->loadRowDesignValues();
        $this->isDirty = $this->historyIndex !== $this->savedHistoryIndex;
        $this->showContentEditor = false;
        $this->editingRowIndex = null;
        $this->refreshPreview();
    }

    private function loadRowDesignValues(): void
    {
        $this->rowDesignDefaults = [];

        foreach ($this->rows as $row) {
            $fields = $this->parseContentFields($row['blade'], $row['slug']);

            foreach ($fields as $field) {
                if (in_array($field['key'], ['section_classes', 'section_container_classes', 'section_id', 'section_animation', 'section_animation_delay', 'section_bg_image', 'section_bg_position', 'section_bg_size', 'section_bg_repeat'], true)) {
                    $this->rowDesignDefaults[$row['slug']][$field['key']] = $field['default'];
                }
            }
        }

        foreach (array_keys($this->rowDesignDefaults) as $slug) {
            $this->rowDesignDefaults[$slug]['section_no_alt'] = '';
        }

        $slugs = array_keys($this->rowDesignDefaults);

        if (empty($slugs)) {
            $this->rowDesignValues = [];

            return;
        }

        $overrides = ContentOverride::query()
            ->whereIn('row_slug', $slugs)
            ->whereIn('key', ['section_classes', 'section_container_classes', 'section_no_alt', 'section_id', 'section_animation', 'section_animation_delay', 'section_bg_image', 'section_bg_position', 'section_bg_size', 'section_bg_repeat'])
            ->get()
            ->keyBy(fn (ContentOverride $o) => $o->row_slug.':'.$o->key);

        $this->rowDesignValues = [];

        foreach ($this->rowDesignDefaults as $slug => $defaults) {
            $this->rowDesignValues[$slug] = [];

            foreach ($defaults as $fieldKey => $default) {
                $override = $overrides->get($slug.':'.$fieldKey);
                $this->rowDesignValues[$slug][$fieldKey] = $override?->value ?: $default;
            }
        }

        $this->syncAltRowClasses();
    }

    /**
     * Ensure row-alt is applied to every even-position row (2nd, 4th, 6th…)
     * and removed from every odd-position row, reflecting current row order.
     */
    private function syncAltRowClasses(): void
    {
        $altClass = 'row-alt';
        $drafts = session('editor_draft_overrides', []);
        $changed = false;
        $altRowsStart = \App\Models\Setting::get('branding.alt_rows_start', 'even');

        foreach ($this->rows as $index => $row) {
            $slug = $row['slug'];

            if (! isset($this->rowDesignDefaults[$slug]['section_classes'])) {
                continue;
            }

            $current = $this->rowDesignValues[$slug]['section_classes'] ?? '';
            $classList = array_values(array_filter(preg_split('/\s+/', trim($current))));

            // Determine which position index receives alt coloring
            $shouldAlt = $altRowsStart === 'odd' ? ($index % 2 === 0) : ($index % 2 === 1);

            // Never override rows that have a colored/non-white background (e.g. CTAs with bg-primary)
            $classesWithoutAlt = array_filter($classList, fn ($c) => $c !== $altClass);
            $hasColoredBg = $this->rowHasColoredBackground(implode(' ', $classesWithoutAlt));

            $noAlt = ! $this->altRowsEnabled
                || ($this->rowDesignValues[$slug]['section_no_alt'] ?? '') === '1'
                || $hasColoredBg;

            if ($shouldAlt && ! $noAlt && ! in_array($altClass, $classList, true)) {
                $classList[] = $altClass;
            } elseif (! $shouldAlt || $noAlt) {
                $classList = array_values(array_filter($classList, fn ($c) => $c !== $altClass));
            }

            $newValue = implode(' ', $classList);

            if ($newValue !== $current) {
                $this->rowDesignValues[$slug]['section_classes'] = $newValue;
                $default = $this->rowDesignDefaults[$slug]['section_classes'];
                $storeValue = $newValue === $default ? '' : $newValue;
                $drafts[$slug.':section_classes'] = ['type' => 'classes', 'value' => $storeValue];
                $changed = true;
            }
        }

        if ($changed) {
            session(['editor_draft_overrides' => $drafts]);
        }
    }

    /**
     * Returns true when the given class string contains a non-neutral background color.
     * Neutral backgrounds (white, near-white) can receive the alt row color.
     * Colored backgrounds (bg-primary, dark zinc, brand colors) should not be overridden.
     * Only matches unprefixed bg-* classes (ignores dark:bg-*, hover:bg-*, etc.).
     */
    private function rowHasColoredBackground(string $classes): bool
    {
        $neutralBgs = ['bg-white', 'bg-zinc-50', 'bg-gray-50', 'bg-slate-50', 'bg-neutral-50', 'bg-transparent'];

        preg_match_all('/(?:^|\s)(bg-[\w-]+)/', $classes, $matches);
        $bgClasses = $matches[1] ?? [];

        if (empty($bgClasses)) {
            return false;
        }

        foreach ($bgClasses as $bgClass) {
            if (! in_array($bgClass, $neutralBgs, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse editable field definitions for a row from its @schema block.
     *
     * @return array<int, array{slug: string, key: string, type: string, default: string, label: string, group: string}>
     */
    private function parseContentFields(string $blade, string $slug): array
    {
        $normalized = str_replace($slug, '__SLUG__', $blade);
        $schemaFields = app(DesignLibraryService::class)->parseSchemaFields($normalized);

        if (empty($schemaFields) && str_contains($slug, ':')) {
            $templateName = substr($slug, 0, strrpos($slug, ':'));
            $schemaFields = app(SchemaCache::class)->getFieldsForRow($templateName);
        }

        return array_map(fn ($field) => array_merge($field, ['slug' => $slug]), $schemaFields);
    }

    public function saveSeoSettings(): void
    {
        $isPublicPage = (bool) preg_match('#^pages/⚡[^/]+\.blade\.php$#u', $this->file);
        $isSubdirPublicPage = ! $isPublicPage && (bool) preg_match('#^pages/(?!dashboard/).*⚡[^/]+\.blade\.php$#u', $this->file);

        $rules = ['pageStatus' => 'required|in:draft,published,unlisted,unpublished'];

        if ($isPublicPage) {
            $rules['pageSlug'] = ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'];

            if ($this->requiresLogin && $this->requiredRole !== '') {
                $rules['requiredRole'] = 'in:manager,admin,super';
            }
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

                $service->removePageRoute($currentSlug);
                $newFile = $service->renamePage($this->file, $this->pageSlug);
                $this->file = $newFile;
                $this->liveUrl = $service->getRouteForFile($newFile);
                $this->previewUrl = route('design-library.preview', ['token' => $service->previewToken($newFile)]);

                if ($this->createSlugRedirect) {
                    $service->addRedirectRoute($currentSlug, $this->pageSlug, (int) $this->slugRedirectType);
                }

                if ($this->requiresLogin) {
                    $service->addAuthRoute($this->pageSlug, $this->isCachedPage, $this->requiredRole);
                } else {
                    $service->addPublicRoute($this->pageSlug, $this->isCachedPage);
                }
            } else {
                $currentlyAuth = $service->isAuthRoute($currentSlug);
                $currentRole = $currentlyAuth ? $service->getRouteAuthRole($currentSlug) : '';
                $currentlyCached = $currentlyAuth
                    ? $service->isAuthRouteCached($currentSlug)
                    : $service->isRouteCached($currentSlug);

                $routeChanged = $currentlyAuth !== $this->requiresLogin
                    || $currentlyCached !== $this->isCachedPage
                    || $currentRole !== $this->requiredRole;

                if ($routeChanged) {
                    $service->removePageRoute($currentSlug);

                    if ($this->requiresLogin) {
                        $service->addAuthRoute($currentSlug, $this->isCachedPage, $this->requiredRole);
                    } else {
                        $service->addPublicRoute($currentSlug, $this->isCachedPage);
                    }
                }
            }

            $this->originalPageSlug = $this->pageSlug;
            $this->createSlugRedirect = false;
            $this->slugRedirectType = '301';
        }

        if ($isSubdirPublicPage) {
            $service = new VoltFileService;
            $routePath = $service->getRoutePathForFile($this->file);

            if ($routePath) {
                $currentlyAuth = $service->isAuthRoute($routePath);
                $currentRole = $currentlyAuth ? $service->getRouteAuthRole($routePath) : '';
                $currentlyCached = $currentlyAuth
                    ? $service->isAuthRouteCached($routePath)
                    : $service->isRouteCached($routePath);

                $routeChanged = $currentlyAuth !== $this->requiresLogin
                    || $currentlyCached !== $this->isCachedPage
                    || $currentRole !== $this->requiredRole;

                if ($routeChanged) {
                    $service->updateRouteSection($routePath, $this->isCachedPage, $this->requiresLogin, $this->requiredRole);
                }
            }
        }

        $this->updatePhpSectionWithSeo();
        $this->showSeoModal = false;
        $this->saveFile();
    }

    private function parseSeoFromPhpSection(): void
    {
        preg_match("/public string \\\$pageName = '([^']*)';/", $this->phpSection, $pageNameMatch);
        $this->pageName = $pageNameMatch[1] ?? '';

        preg_match("/#\[Title\('([^']*)'\)\]/", $this->phpSection, $titleMatch);
        $this->seoTitle = $titleMatch[1] ?? '';

        preg_match("/'description'\s*=>\s*'([^']*)'/", $this->phpSection, $descMatch);
        $this->seoDescription = $descMatch[1] ?? '';

        $this->seoNoindex = (bool) preg_match("/'noindex'\s*=>\s*true/", $this->phpSection);

        preg_match("/'ogImage'\s*=>\s*'([^']*)'/", $this->phpSection, $ogMatch);
        $this->seoOgImage = $ogMatch[1] ?? '';

        preg_match("/'status'\s*=>\s*'([^']*)'/", $this->phpSection, $statusMatch);
        $this->pageStatus = $statusMatch[1] ?? 'published';

        if ((bool) preg_match("/'alt_rows_disabled'\s*=>\s*true/", $this->phpSection)) {
            $this->altRowsEnabled = false;
        } else {
            $this->altRowsEnabled = (bool) \App\Models\Setting::get('branding.alt_rows_enabled', false);
        }

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

        if (! $this->altRowsEnabled) {
            $data[] = "'alt_rows_disabled' => true";
        }

        $newLayout = empty($data)
            ? "#[Layout('layouts.public')]"
            : "#[Layout('layouts.public', [".implode(', ', $data).'])]';

        $this->phpSection = preg_replace(
            "/#\[Layout\('layouts\.public'(?:,\s*\[[^\]]*\])?\)\]/",
            $newLayout,
            $this->phpSection
        );

        $escapedPageName = str_replace("'", "\'", $this->pageName);

        if (preg_match("/public string \\\$pageName = '[^']*';/", $this->phpSection)) {
            $this->phpSection = preg_replace_callback(
                "/public string \\\$pageName = '[^']*';/",
                fn ($m) => "public string \$pageName = '{$escapedPageName}';",
                $this->phpSection
            );
        } else {
            $this->phpSection = preg_replace_callback(
                '/(new #\[.+?\] class extends Component\s*\{)/s',
                fn ($m) => $m[1]."\n    public string \$pageName = '{$escapedPageName}';",
                $this->phpSection
            );
        }

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

        // Remove any legacy page-auth PHP block — auth is now handled at the route level.
        $this->phpSection = $service->removePhpCode($this->phpSection, 'page-auth');
    }

    /**
     * Populate $previewContextOptions for files that require route parameters.
     * When options are available, auto-selects the first if none is already chosen.
     */
    private function loadPreviewContextOptions(): void
    {
        $this->previewContextOptions = [];

        if ($this->file === 'pages/blog/⚡show.blade.php') {
            $this->previewContextOptions = \App\Models\Post::query()
                ->orderByDesc('published_at')
                ->limit(50)
                ->pluck('title', 'slug')
                ->all();

            if (! $this->previewContext || ! isset($this->previewContextOptions[$this->previewContext])) {
                $this->previewContext = array_key_first($this->previewContextOptions) ?? '';
            }

            return;
        }

        // No context options for this file — clear any stale selection.
        $this->previewContext = '';
    }

    /**
     * Resolve the previewContext string into a keyed array for the mount injector.
     *
     * @return array<string, string>
     */
    private function resolvePreviewContext(): array
    {
        if ($this->file === 'pages/blog/⚡show.blade.php' && $this->previewContext) {
            return ['slug' => $this->previewContext];
        }

        return [];
    }

    private function refreshPreview(): void
    {
        try {
            $service = new VoltFileService;
            // Strip behavior blocks so the preview iframe always renders the page content.
            $previewPhpSection = $service->removePhpCode($this->phpSection, 'page-status-abort');
            $previewPhpSection = $service->removePhpCode($previewPhpSection, 'page-redirect');
            $service->writePreviewFile($previewPhpSection, $this->rows, $this->file, $this->resolvePreviewContext());
        } catch (\Throwable) {
            // Preview write failed; continue without updating the iframe.
        }

        $this->dispatch('refresh-preview', url: $this->previewUrl);
    }

    public function generateAiContent(string $fieldKey, string $prompt, string $fieldType): void
    {
        $provider = \App\Models\Setting::get('ai.provider', 'claude');
        $isRichText = $fieldType === 'richtext';
        $systemPrompt = $isRichText
            ? 'You are a content writer. Generate HTML content based on the user\'s request. Return only the HTML, no markdown code fences, no explanation.'
            : 'You are a content writer. Generate concise, well-written text based on the user\'s request. Return only the text, no quotes, no explanation.';

        try {
            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'max_tokens' => 1024,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'OpenAI API error.');
                }

                $content = $response->json('choices.0.message.content', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => 'claude-haiku-4-5-20251001',
                    'max_tokens' => 1024,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Claude API error.');
                }

                $content = $response->json('content.0.text', '');
            }

            $this->dispatch('ai-content-generated', fieldKey: $fieldKey, content: $content);
        } catch (\Exception $e) {
            $this->dispatch('ai-generate-error', fieldKey: $fieldKey, message: $e->getMessage());
        }
    }
}; ?>

<div>
    {{-- Item Picker --}}
    <flux:modal wire:model="showItemPicker" class="w-full max-w-sm">
        <flux:heading size="lg" class="mb-1">{{ __('Add Item') }}</flux:heading>
        <flux:subheading class="mb-4">{{ __('Choose an item to add to this section.') }}</flux:subheading>
        <div class="grid grid-cols-2 gap-2">
            @foreach (\App\Support\RowItemLibrary::items() as $itemKey => $item)
                <button
                    wire:click="addItemToRow('{{ $itemKey }}')"
                    class="flex items-center gap-2 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors text-left"
                >
                    <flux:icon name="{{ $item['icon'] }}" class="size-4 text-zinc-500 dark:text-zinc-400 shrink-0" />
                    <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $item['name'] }}</span>
                </button>
            @endforeach
        </div>
    </flux:modal>

    {{-- Library Drawer --}}
    <flux:modal wire:model="showLibraryDrawer" class="w-full max-w-6xl">
        <flux:heading size="lg" class="mb-4">{{ __('Insert Row') }}</flux:heading>

        {{-- Tabs --}}
        <div class="flex gap-1 mb-4 border-b border-zinc-200 dark:border-zinc-700">
            <button
                wire:click="switchLibraryTab('rows')"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors -mb-px {{ $libraryTab === 'rows' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
            >
                {{ __('Rows') }}
            </button>
            <button
                wire:click="switchLibraryTab('shared')"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors -mb-px {{ $libraryTab === 'shared' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
            >
                {{ __('Shared Rows') }}
            </button>
            <button
                wire:click="switchLibraryTab('groups')"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors -mb-px {{ $libraryTab === 'groups' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
            >
                {{ __('Row Groups') }}
            </button>
        </div>

        <div class="min-h-[40rem]">
        @if ($libraryTab === 'rows')
            <div class="flex gap-3 mb-4">
                <flux:input
                    wire:model.live="librarySearch"
                    placeholder="Search rows…"
                    icon="magnifying-glass"
                    class="flex-1"
                    autofocus
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
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[40rem] overflow-y-auto pr-1">
                    @foreach ($this->libraryRows as $libRow)
                        <div wire:key="lib-{{ $libRow->id }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden hover:border-primary/40 transition-colors flex flex-col">
                            {{-- Live preview --}}
                            <div class="relative h-44 overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border-b border-zinc-200 dark:border-zinc-700">
                                <div class="absolute inset-0 flex items-center justify-center animate-pulse pointer-events-none">
                                    <flux:icon name="photo" class="size-8 text-zinc-300 dark:text-zinc-600" />
                                </div>
                                <iframe
                                    src="{{ route('dashboard.design-library.preview', ['type' => 'row', 'id' => $libRow->id]) }}"
                                    class="absolute top-0 border-0"
                                    style="width:1280px;height:800px;transform:scale(0.25);transform-origin:top center;pointer-events:none;left:50%;margin-left:-640px;"
                                    loading="lazy"
                                    scrolling="no"
                                    tabindex="-1"
                                    aria-hidden="true"
                                    onload="this.previousElementSibling.style.display='none'"
                                ></iframe>
                            </div>
                            {{-- Card body --}}
                            <div class="p-3 flex flex-col flex-1">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-zinc-900 dark:text-white text-sm truncate">{{ $libRow->name }}</div>
                                    @if ($libRow->description)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate mt-0.5">{{ $libRow->description }}</div>
                                    @endif
                                    <flux:badge size="sm" class="mt-1">{{ $libRow->category->label() }}</flux:badge>
                                </div>
                                <div class="mt-2 pt-2 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-2">
                                    <flux:button
                                        wire:click="insertRow({{ $libRow->id }}, {{ $insertAtIndex ?? count($rows) }})"
                                        variant="primary"
                                        size="sm"
                                        class="flex-1"
                                    >
                                        {{ __('Insert') }}
                                    </flux:button>
                                    <a href="{{ route('dashboard.design-library.preview', ['type' => 'row', 'id' => $libRow->id]) }}" target="_blank" rel="noopener noreferrer">
                                        <flux:button variant="ghost" size="sm" icon="eye" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @elseif ($libraryTab === 'shared')
            @if ($this->sharedLibraryRows->isEmpty())
                <div class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="share" class="size-10 mx-auto mb-3 opacity-40" />
                    <p class="text-sm">No shared rows yet.</p>
                    <p class="text-xs mt-1">Use the "Make Shared" action on any row to share it.</p>
                </div>
            @else
                <div class="space-y-2 max-h-[40rem] overflow-y-auto">
                    @foreach ($this->sharedLibraryRows as $sharedRow)
                        <div wire:key="shared-{{ $sharedRow->slug }}" class="flex items-center gap-3 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-zinc-900 dark:text-white text-sm truncate">{{ $sharedRow->name }}</div>
                                <div class="text-[10px] font-mono text-zinc-400 dark:text-zinc-500 truncate mt-0.5">{{ $sharedRow->slug }}</div>
                            </div>
                            <flux:button
                                wire:click="insertSharedRow('{{ $sharedRow->slug }}', {{ $insertAtIndex ?? count($rows) }})"
                                variant="primary"
                                size="sm"
                            >
                                {{ __('Insert') }}
                            </flux:button>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="flex gap-3 mb-4">
                <flux:input
                    wire:model.live="librarySearch"
                    placeholder="Search row groups…"
                    icon="magnifying-glass"
                    class="flex-1"
                    autofocus
                />
                <flux:select wire:model.live="libraryPageCategory" class="w-44">
                    <flux:select.option value="">{{ __('All types') }}</flux:select.option>
                    @foreach ($this->pageCategories as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            @if ($this->libraryPages->isEmpty())
                <div class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="document-text" class="size-10 mx-auto mb-3 opacity-40" />
                    <p class="text-sm">No row groups found.</p>
                    <p class="text-xs mt-1">Create groups in the Design Library to quickly insert multiple rows at once.</p>
                </div>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[40rem] overflow-y-auto pr-1">
                    @foreach ($this->libraryPages as $libPage)
                        <div wire:key="page-{{ $libPage->id }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden hover:border-primary/40 transition-colors flex flex-col">
                            {{-- Live preview --}}
                            <div class="relative h-44 overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border-b border-zinc-200 dark:border-zinc-700">
                                <div class="absolute inset-0 flex items-center justify-center animate-pulse pointer-events-none">
                                    <flux:icon name="photo" class="size-8 text-zinc-300 dark:text-zinc-600" />
                                </div>
                                <iframe
                                    src="{{ route('dashboard.design-library.preview', ['type' => 'page', 'id' => $libPage->id]) }}"
                                    class="absolute top-0 border-0"
                                    style="width:1280px;height:800px;transform:scale(0.25);transform-origin:top center;pointer-events:none;left:50%;margin-left:-640px;"
                                    loading="lazy"
                                    scrolling="no"
                                    tabindex="-1"
                                    aria-hidden="true"
                                    onload="this.previousElementSibling.style.display='none'"
                                ></iframe>
                            </div>
                            {{-- Card body --}}
                            <div class="p-3 flex flex-col flex-1">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-zinc-900 dark:text-white text-sm truncate">{{ $libPage->name }}</div>
                                    @if ($libPage->description)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate mt-0.5">{{ $libPage->description }}</div>
                                    @endif
                                    <flux:badge size="sm" class="mt-1 shrink-0">{{ $libPage->website_category->label() }}</flux:badge>
                                </div>
                                @if (! empty($libPage->row_names))
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach ($libPage->row_names as $rowName)
                                            <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400">
                                                <flux:icon name="rectangle-stack" class="size-2.5" />
                                                {{ $rowName }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="mt-2 pt-2 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-2">
                                    <flux:button
                                        wire:click="insertPageBundle({{ $libPage->id }}, {{ $insertAtIndex ?? count($rows) }})"
                                        variant="primary"
                                        size="sm"
                                        class="flex-1"
                                    >
                                        {{ __('Insert All') }}
                                    </flux:button>
                                    <a href="{{ route('dashboard.design-library.preview', ['type' => 'page', 'id' => $libPage->id]) }}" target="_blank" rel="noopener noreferrer">
                                        <flux:button variant="ghost" size="sm" icon="eye" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
        </div>
    </flux:modal>


    <div
        x-data="{
            previewWidth: null,
            showAllBreakpoints: false,
            activePreview: 'a',
            setWidth(w) { this.previewWidth = this.previewWidth === w ? null : w; },
            selectRowBySlug(slug, openEditor = true) {
                const rows = $wire.rows;
                const index = rows.findIndex(r => r.slug === slug);
                if (index !== -1) {
                    $dispatch('row-selected', { index: index });
                    if (openEditor) { $wire.openContentEditor(index); }
                    $nextTick(() => {
                        const el = document.querySelector('[data-row-sidebar-index=\'' + index + '\']');
                        if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
                    });
                }
            },
            refreshPreview(url) {
                const nextKey = this.activePreview === 'a' ? 'b' : 'a';
                const active = document.getElementById('page-preview-' + this.activePreview);
                const next = document.getElementById('page-preview-' + nextKey);
                let savedScroll = 0;
                try { savedScroll = active.contentWindow.scrollY || 0; } catch (e) {}
                next.onload = () => {
                    try { next.contentWindow.scrollTo(0, savedScroll); } catch (e) {}
                    next.style.zIndex = '2';
                    next.style.opacity = '1';
                    active.style.zIndex = '1';
                    active.style.opacity = '0';
                    this.activePreview = nextKey;
                };
                next.src = url + '?_=' + Date.now();
            }
        }"
        @keydown.ctrl.s.window.prevent="if ($wire.file) $wire.saveFile()"
        @keydown.meta.s.window.prevent="if ($wire.file) $wire.saveFile()"
        @keydown.ctrl.c.window="
            const activeTag = document.activeElement?.tagName;
            const isTextField = ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeTag) || document.activeElement?.isContentEditable;
            const hasSelection = window.getSelection()?.toString().length > 0;
            if (!isTextField && !hasSelection) { $dispatch('copy-row-keyboard'); }
        "
        @keydown.meta.c.window="
            const activeTag = document.activeElement?.tagName;
            const isTextField = ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeTag) || document.activeElement?.isContentEditable;
            const hasSelection = window.getSelection()?.toString().length > 0;
            if (!isTextField && !hasSelection) { $dispatch('copy-row-keyboard'); }
        "
        @keydown.ctrl.v.window="
            const activeTag = document.activeElement?.tagName;
            const isTextField = ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeTag) || document.activeElement?.isContentEditable;
            if (!isTextField) { const data = localStorage.getItem('webprocms_copied_single_row'); if (data) { $wire.pasteSingleRow(JSON.parse(data)); } }
        "
        @keydown.meta.v.window="
            const activeTag = document.activeElement?.tagName;
            const isTextField = ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeTag) || document.activeElement?.isContentEditable;
            if (!isTextField) { const data = localStorage.getItem('webprocms_copied_single_row'); if (data) { $wire.pasteSingleRow(JSON.parse(data)); } }
        "
        @message.window="
            if ($event.data && $event.data.editorRowSlug) {
                if ($event.data.editorGroup) { $dispatch('pending-group', { group: $event.data.editorGroup }); }
                if ($event.data.editorSubgroup) { $dispatch('pending-subgroup', { subgroup: $event.data.editorSubgroup }); }
                selectRowBySlug($event.data.editorRowSlug, !!$event.data.editorGroup);
            }
            else if ($event.origin === window.location.origin && $event.data && $event.data.type === 'editor-save-page' && $wire.file) { $wire.saveFile(); }
        "
        class="flex flex-col min-h-screen bg-white dark:bg-zinc-900"
    >
        {{-- Editor toolbar --}}
        <div class="sticky top-0 z-30 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 px-6 py-3 flex items-center gap-3">
            <div class="flex-1 flex items-center gap-3">
                <flux:tooltip content="{{ $this->isLayoutPartial ? 'Back to Templates' : 'Back to Pages' }}" position="bottom">
                    <flux:button href="{{ $this->isLayoutPartial ? route('dashboard.templates') : route('dashboard.pages') }}" variant="outline" size="sm" icon="arrow-left" wire:navigate />
                </flux:tooltip>

                @if ($liveUrl)
                    <flux:tooltip content="Back to Website" position="bottom">
                        <flux:button href="{{ $liveUrl }}" variant="outline" size="sm" icon="globe-alt" />
                    </flux:tooltip>
                @endif

                @if (! $this->isLayoutPartial)
                    <flux:tooltip content="Selected Page">
                        <flux:select wire:model.live="file" placeholder="Select a page to edit…" size="sm" class="w-48">
                            <flux:select.option value="">{{ __('Select a page…') }}</flux:select.option>
                            @foreach ($this->voltFiles as $label => $path)
                                <flux:select.option value="{{ $path }}">{{ $label }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:tooltip>

                    @if ($previewContextOptions)
                        <flux:tooltip content="Preview as" position="bottom">
                            <flux:select wire:model.live="previewContext" size="sm" class="w-52">
                                @foreach ($previewContextOptions as $value => $label)
                                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:tooltip>
                    @endif

                    @if ($file)
                        <flux:tooltip content="Page Settings" position="bottom">
                            <flux:button variant="outline" size="sm" icon="cog-6-tooth" wire:click="$set('showSeoModal', true)" :loading="false" />
                        </flux:tooltip>
                    @endif
                @endif


            </div>

            {{-- Page status badges --}}
            @if ($file && ! $this->isLayoutPartial)
                @php
                    $statusTooltips = [
                        'draft'       => 'Saved but not yet visible to the public.',
                        'published'   => 'Live and visible to all visitors.',
                        'unlisted'    => 'Accessible via direct link, but excluded from auto-generated navigation.',
                        'unpublished' => 'Removed from public access — visitors will see a 404.',
                    ];
                @endphp
                <div class="flex-1 flex items-center justify-center gap-1.5">
                    <flux:tooltip content="{{ $statusTooltips[$pageStatus] ?? '' }}" position="bottom">
                        <span
                            x-data
                            x-bind:class="{
                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': $wire.pageStatus === 'published',
                                'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300': $wire.pageStatus === 'unlisted',
                                'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': $wire.pageStatus === 'draft',
                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': $wire.pageStatus === 'unpublished',
                            }"
                            class="text-xs font-medium px-2 py-0.5 rounded-full capitalize cursor-pointer"
                            x-text="$wire.pageStatus"
                            @click="$wire.showSeoModal = true; $dispatch('open-settings-section', 'basic')"
                        ></span>
                    </flux:tooltip>

                    <flux:tooltip content="{{ $seoNoindex ? 'Search engines are prevented from indexing this page.' : 'Search engines can index this page.' }}" position="bottom">
                        <span
                            x-data
                            x-bind:class="$wire.seoNoindex
                                ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                                : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'"
                            class="text-xs font-medium px-2 py-0.5 rounded-full cursor-pointer"
                            x-text="$wire.seoNoindex ? 'No Index' : 'Indexed'"
                            @click="$wire.showSeoModal = true; $dispatch('open-settings-section', 'seo')"
                        ></span>
                    </flux:tooltip>

                    <flux:tooltip content="{{ $redirectUrl ? 'Redirects to: '.$redirectUrl : 'No redirect configured.' }}" position="bottom">
                        <span
                            x-data
                            x-bind:class="$wire.redirectUrl
                                ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'"
                            class="text-xs font-medium px-2 py-0.5 rounded-full cursor-pointer"
                            x-text="$wire.redirectUrl ? 'Redirect' : 'No Redirect'"
                            @click="$wire.showSeoModal = true; $dispatch('open-settings-section', 'redirect')"
                        ></span>
                    </flux:tooltip>
                </div>
            @endif

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

            <div class="flex-1 flex items-center justify-end gap-2">
                @if ($isDirty)
                    <span class="text-xs text-amber-600 dark:text-amber-400 font-medium whitespace-nowrap">Unsaved changes</span>
                @endif

                @if ($file)
                    <span class="{{ $historyIndex <= 0 ? 'opacity-30 pointer-events-none' : '' }}">
                        <flux:tooltip content="Undo">
                            <flux:button wire:click="undo" variant="ghost" size="sm" icon="arrow-uturn-left" />
                        </flux:tooltip>
                    </span>
                    <span class="{{ $historyIndex >= count($rowHistory) - 1 ? 'opacity-30 pointer-events-none' : '' }}">
                        <flux:tooltip content="Redo">
                            <flux:button wire:click="redo" variant="ghost" size="sm" icon="arrow-uturn-right" />
                        </flux:tooltip>
                    </span>
                    <span class="{{ ! $isDirty ? 'opacity-30 pointer-events-none' : '' }}">
                        <flux:tooltip content="Discard unsaved changes">
                            <flux:button wire:click="discardChanges" variant="ghost" size="sm" icon="arrow-path" />
                        </flux:tooltip>
                    </span>
                    @if ($liveUrl)
                        <a href="{{ $liveUrl }}" target="_blank">
                            <flux:button variant="outline" size="sm" icon="arrow-top-right-on-square">{{ __('View Live') }}</flux:button>
                        </a>
                    @endif
                    <flux:button wire:click="saveFile" variant="primary" size="sm" icon="check">
                        {{ __('Save') }}
                    </flux:button>
                @endif
            </div>
        </div>


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
                <div
                    class="w-96 shrink-0 order-last border-l border-zinc-200 dark:border-zinc-700 flex flex-col"
                    x-data="{ editorOpen: false, designMode: false, advancedMode: false, groupMode: null, allGroupsOpen: false, selectedRowIndex: null, pendingGroup: null, pendingSubgroup: null }"
                    x-on:pending-group.window="pendingGroup = $event.detail.group"
                    x-on:pending-subgroup.window="pendingSubgroup = $event.detail.subgroup"
                    x-on:content-editor-opened.window="
                        editorOpen = true; designMode = false; advancedMode = false;
                        if (pendingGroup) {
                            const g = pendingGroup; pendingGroup = null;
                            const sg = pendingSubgroup; pendingSubgroup = null;
                            $nextTick(() => {
                                $dispatch('open-group', { group: g });
                                $nextTick(() => {
                                    if (sg) { $dispatch('open-subgroup', { subgroup: sg }); }
                                    const gEl = document.querySelector('[data-group-id=\'' + g + '\']');
                                    if (gEl) { gEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
                                });
                            });
                        }
                    "
                    x-on:content-editor-closed.window="editorOpen = false"
                    x-on:row-selected.window="selectedRowIndex = $event.detail.index"
                    x-on:row-deselected.window="selectedRowIndex = null"
                    x-on:copy-row-keyboard.window="
                        if (selectedRowIndex !== null) {
                            const row = $wire.rows[selectedRowIndex];
                            if (row) { localStorage.setItem('webprocms_copied_single_row', JSON.stringify(row)); $dispatch('notify', { message: 'Row copied.' }); }
                        }
                    "
                >
                    {{-- Content editor view --}}
                    <div x-show="editorOpen" class="flex flex-col flex-1" style="display: none">
                        @if ($editingRowIndex !== null && isset($rows[$editingRowIndex]))
                        <div class="shrink-0 flex items-center gap-2 p-3 border-b border-zinc-200 dark:border-zinc-700">
                            <flux:button
                                @click="editorOpen = false"
                                variant="ghost"
                                size="sm"
                                icon="arrow-left"
                                title="Back to rows"
                            />
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $rows[$editingRowIndex]['name'] }}</div>
                                <div class="text-[10px] font-mono text-zinc-400 dark:text-zinc-500 truncate">{{ $rows[$editingRowIndex]['slug'] }}</div>
                            </div>
                            <div class="flex rounded-md border border-zinc-200 dark:border-zinc-700 overflow-hidden shrink-0">
                                <button type="button" @click="if (!designMode && !advancedMode) { allGroupsOpen = !allGroupsOpen; $dispatch('set-group-open', { value: allGroupsOpen }); } else { designMode = false; advancedMode = false; allGroupsOpen = true; $wire.resetEmptyClassesFields(); $dispatch('set-group-mode', {}); $dispatch('set-group-open', { value: true }); }" :class="!designMode && !advancedMode ? 'bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'" class="p-1.5 transition-colors" title="Content"><flux:icon name="document-text" class="size-3.5" /></button>
                                <button type="button" @click="if (designMode && !advancedMode) { allGroupsOpen = !allGroupsOpen; $dispatch('set-group-open', { value: allGroupsOpen }); } else { designMode = true; advancedMode = false; allGroupsOpen = true; $wire.resetEmptyClassesFields(); $dispatch('set-group-mode', {}); $dispatch('set-group-open', { value: true }); }" :class="designMode && !advancedMode ? 'bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'" class="p-1.5 transition-colors border-l border-zinc-200 dark:border-zinc-700" title="Design"><flux:icon name="paint-brush" class="size-3.5" /></button>
                                <button type="button" @click="if (advancedMode) { allGroupsOpen = !allGroupsOpen; $dispatch('set-group-open', { value: allGroupsOpen }); } else { advancedMode = true; designMode = false; allGroupsOpen = true; $wire.resetEmptyClassesFields(); $dispatch('set-group-mode', {}); $dispatch('set-group-open', { value: true }); }" :class="advancedMode ? 'bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'" class="p-1.5 transition-colors border-l border-zinc-200 dark:border-zinc-700" title="Advanced"><flux:icon name="code-bracket" class="size-3.5" /></button>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto p-4">
                            @if (! empty($rows[$editingRowIndex]['shared'] ?? false))
                                <div class="mb-3 flex items-start gap-2 px-3 py-2 rounded-lg bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 text-xs text-blue-700 dark:text-blue-300">
                                    <flux:icon name="share" class="size-3.5 mt-0.5 shrink-0" />
                                    <span>Shared row — changes affect all pages using it.</span>
                                </div>
                            @endif

                            {{-- Auto BEM — shown when Advanced tab is active --}}
                            <div x-show="advancedMode" x-cloak class="mb-3">
                                <button
                                    wire:click="applyAutoBem({{ $editingRowIndex }})"
                                    type="button"
                                    class="w-full flex items-center justify-center gap-1.5 text-xs font-medium py-1.5 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 hover:text-primary hover:border-primary transition-colors"
                                    title="Auto-generate BEM IDs for all elements in this row. Uses Section ID (or row name) as the BEM block."
                                >
                                    <flux:icon name="sparkles" class="size-3.5" />
                                    Auto BEM IDs
                                </button>
                            </div>

                            @php
                                $rowItemBlocks = $editingRowIndex !== null ? $this->extractItemBlocks($rows[$editingRowIndex]['blade']) : [];
                            @endphp
                            @if (! empty($rowItemBlocks))
                                {{-- Item-based rows: each item is a collapsible card with fields + actions inside --}}
                                @php $allItemFieldGroups = collect($contentFields)->groupBy('group'); @endphp
                                <div class="space-y-2 mb-4" x-data="{ dragging: null, over: null }">
                                    @foreach ($rowItemBlocks as $item)
                                        @php
                                            $itemFields = $allItemFieldGroups->get($item['prefix'], collect());
                                            $groupShowField = $itemFields->first(fn ($f) => $f['type'] === 'toggle' && str_starts_with($f['key'], 'toggle_'));
                                            $headerToggleField = null;
                                            if ($groupShowField) {
                                                $showPrefix = str_replace('toggle_', '', $groupShowField['key']);
                                                $otherFields = $itemFields->reject(fn ($f) => $f['key'] === $groupShowField['key']);
                                                $isGroupToggle = $otherFields->every(fn ($f) => $f['type'] === 'toggle' || str_ends_with($f['key'], '_new_tab') || str_contains($f['key'], $showPrefix));
                                                if ($isGroupToggle) {
                                                    $headerToggleField = $groupShowField;
                                                }
                                            }
                                            $bodyFields = $headerToggleField
                                                ? $itemFields->reject(fn ($f) => $f['key'] === $headerToggleField['key'])
                                                : $itemFields;
                                            $itemHasContentFields = $bodyFields->contains(fn ($f) => ! in_array($f['type'], ['classes', 'object_fit', 'id', 'attrs']));
                                            $itemHasClassesFields = $bodyFields->contains(fn ($f) => in_array($f['type'], ['classes', 'object_fit']));
                                            $itemHasAdvancedFields = $bodyFields->contains(fn ($f) => in_array($f['type'], ['id', 'attrs']));
                                            $isFirst = $item['index'] === 0;
                                            $isLast = $item['index'] === count($rowItemBlocks) - 1;
                                        @endphp
                                        <div
                                            class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden transition-colors"
                                            x-data="{ open: false, groupMode: null }"
                                            data-group-id="{{ $item['prefix'] }}"
                                            @set-group-open.window="open = $event.detail.value"
                                            @set-group-mode.window="groupMode = null"
                                            @open-group.window="open = ($event.detail.group === '{{ $item['prefix'] }}')"
                                            @sidebar-item-opened.window="if ($event.detail.index !== {{ $item['index'] }}) open = false"
                                            draggable="true"
                                            @dragstart="dragging = {{ $item['index'] }}"
                                            @dragover.prevent="over = {{ $item['index'] }}"
                                            @drop="if (dragging !== null) { $wire.reorderItems(dragging, over); } dragging = null; over = null"
                                            @dragend="dragging = null; over = null"
                                            :style="{
                                                opacity: dragging === {{ $item['index'] }} ? '0.4' : '',
                                                'border-top': over === {{ $item['index'] }} && dragging !== null && dragging > {{ $item['index'] }} ? '2px solid var(--color-primary)' : '',
                                                'border-bottom': over === {{ $item['index'] }} && dragging !== null && dragging < {{ $item['index'] }} ? '2px solid var(--color-primary)' : ''
                                            }"
                                        >
                                            {{-- Card header: click to expand/collapse --}}
                                            @php $itemAccordionIndex = $item['index']; @endphp
                                            <div class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700/50 cursor-pointer select-none" @click="open = !open; if (open) $dispatch('sidebar-item-opened', { index: {{ $itemAccordionIndex }} })">
                                                <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200 flex-1 truncate">{{ $item['name'] }}</span>
                                                @if ($itemHasContentFields)
                                                    <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'content' : (!designMode && !advancedMode); if (isActive && open) { open = false; } else { groupMode = 'content'; open = true; $dispatch('sidebar-item-opened', { index: {{ $itemAccordionIndex }} }); }"
                                                        :class="(groupMode !== null ? groupMode === 'content' : (!designMode && !advancedMode)) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        title="Content"><flux:icon name="document-text" class="size-3.5" /></button>
                                                @endif
                                                @if ($itemHasClassesFields)
                                                    <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode); if (isActive && open) { open = false; } else { groupMode = 'design'; open = true; $dispatch('sidebar-item-opened', { index: {{ $itemAccordionIndex }} }); }"
                                                        :class="(groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode)) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        title="Design"><flux:icon name="paint-brush" class="size-3.5" /></button>
                                                @endif
                                                @if ($itemHasAdvancedFields)
                                                    <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'advanced' : advancedMode; if (isActive && open) { open = false; } else { groupMode = 'advanced'; open = true; $dispatch('sidebar-item-opened', { index: {{ $itemAccordionIndex }} }); }"
                                                        :class="(groupMode !== null ? groupMode === 'advanced' : advancedMode) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        title="Advanced"><flux:icon name="code-bracket" class="size-3.5" /></button>
                                                @endif
                                                @if ($headerToggleField)
                                                    <flux:switch wire:model.live="contentValues.{{ $headerToggleField['key'] }}" @click.stop />
                                                @endif
                                            </div>
                                            {{-- Collapsible body: fields --}}
                                            <div x-show="open" x-collapse>
                                                @if ($bodyFields->isNotEmpty())
                                                    @php
                                                        $flatFields = $bodyFields->filter(fn ($f) => empty($f['subgroup'] ?? null));
                                                        $subgroupedFields = $bodyFields->filter(fn ($f) => ! empty($f['subgroup'] ?? null))->groupBy('subgroup');
                                                    @endphp
                                                    <div class="border-t border-zinc-200 dark:border-zinc-700 p-3 space-y-4">
                                                        @foreach ($flatFields as $field)
                                                            @include('pages.dashboard.pages.partials.content-field', ['field' => $field])
                                                        @endforeach
                                                        @foreach ($subgroupedFields as $subgroupKey => $subFields)
                                                            @php
                                                                $subgroupToggle = $subFields->first(fn ($f) => $f['type'] === 'toggle' && str_starts_with($f['key'], 'toggle_'));
                                                                $subgroupBodyFields = $subgroupToggle
                                                                    ? $subFields->reject(fn ($f) => $f['key'] === $subgroupToggle['key'])
                                                                    : $subFields;
                                                            @endphp
                                                            <div x-data="{ open: true }" class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
                                                                @open-subgroup.window="open = ($event.detail.subgroup === '{{ $subgroupKey }}')">
                                                                <button type="button" @click="open = !open"
                                                                    class="w-full flex items-center justify-between px-3 py-2 bg-zinc-50 dark:bg-zinc-800 text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                                                                    <span>{{ ucwords(str_replace('_', ' ', $subgroupKey)) }}</span>
                                                                    <div class="flex items-center gap-2">
                                                                        @if ($subgroupToggle)
                                                                            <flux:switch wire:model.live="contentValues.{{ $subgroupToggle['key'] }}" @click.stop />
                                                                        @endif
                                                                        <flux:icon name="chevron-down" class="size-3.5 transition-transform duration-200" x-bind:class="open ? '' : '-rotate-90'" />
                                                                    </div>
                                                                </button>
                                                                <div x-show="open" x-collapse class="space-y-4 p-3">
                                                                    @foreach ($subgroupBodyFields as $field)
                                                                        @include('pages.dashboard.pages.partials.content-field', ['field' => $field])
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Action bar: always visible --}}
                                            <div class="relative flex items-center px-2 py-1.5 border-t border-zinc-200 dark:border-zinc-700">
                                                <div class="flex items-center gap-0.5">
                                                    <flux:button
                                                        wire:click="moveItemUp({{ $item['index'] }})"
                                                        variant="ghost" size="sm" icon="arrow-up"
                                                        :disabled="$isFirst"
                                                        :class="$isFirst ? 'opacity-15!' : ''"
                                                        title="Move up" :loading="false"
                                                    />
                                                    <flux:button
                                                        wire:click="moveItemDown({{ $item['index'] }})"
                                                        variant="ghost" size="sm" icon="arrow-down"
                                                        :disabled="$isLast"
                                                        :class="$isLast ? 'opacity-15!' : ''"
                                                        title="Move down" :loading="false"
                                                    />
                                                    <flux:icon name="bars-2" class="size-4 text-zinc-400 dark:text-zinc-500 cursor-grab active:cursor-grabbing mx-2" title="Drag to reorder" />
                                                </div>
                                                <div class="flex items-center gap-0.5 ml-auto">
                                                    <flux:button
                                                        wire:click="openItemPickerAbove({{ $item['index'] }})"
                                                        variant="ghost"
                                                        size="sm"
                                                        title="Insert item above"
                                                        class="px-1!"
                                                        :loading="false"
                                                    >
                                                        <span class="inline-flex items-center">
                                                            <flux:icon name="plus" class="size-3" />
                                                            <flux:icon name="arrow-up" class="size-3" />
                                                        </span>
                                                    </flux:button>
                                                    <flux:button
                                                        wire:click="openItemPickerBelow({{ $item['index'] }})"
                                                        variant="ghost"
                                                        size="sm"
                                                        title="Insert item below"
                                                        class="px-1!"
                                                        :loading="false"
                                                    >
                                                        <span class="inline-flex items-center">
                                                            <flux:icon name="plus" class="size-3" />
                                                            <flux:icon name="arrow-down" class="size-3" />
                                                        </span>
                                                    </flux:button>
                                                    <flux:button
                                                        wire:click="deleteItemFromRow({{ $item['index'] }})"
                                                        wire:confirm="Delete this item?"
                                                        variant="ghost" size="sm" icon="trash"
                                                        class="text-red-500 dark:text-red-400"
                                                        title="Delete item" :loading="false"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif (empty($contentFields))
                                <div class="text-center py-8 text-zinc-400 dark:text-zinc-500">
                                    <flux:icon name="plus-circle" class="size-10 mx-auto mb-2 opacity-40" />
                                    <p class="text-sm">No content yet.</p>
                                    <p class="text-xs mt-1">Add items to build out this section.</p>
                                </div>
                            @else
                                @php
                                    $dlComponents = $this->extractTopLevelComponentsFromBlade($rows[$editingRowIndex]['blade']);
                                    $allComponentFieldKeys = ! empty($dlComponents)
                                        ? array_merge(...array_map(fn ($c) => $c['fieldKeys'], $dlComponents))
                                        : [];
                                    $orphanedFields = collect($contentFields)->filter(fn ($f) => ! in_array($f['key'], $allComponentFieldKeys));
                                @endphp
                                {{-- Design library row: component-level cards with move/delete action bars --}}
                                    @if ($orphanedFields->isNotEmpty())
                                        @php
                                            $orphanHasClassesFields = $orphanedFields->contains(fn ($f) => $f['type'] === 'classes');
                                            $orphanHasAdvancedFields = $orphanedFields->contains(fn ($f) => in_array($f['type'], ['id', 'attrs']));
                                        @endphp
                                        <div
                                            x-data="{ open: false, groupMode: null }"
                                            @set-group-open.window="open = $event.detail.value"
                                            @set-group-mode.window="groupMode = null"
                                            @sidebar-group-opened.window="if ($event.detail.slug === '{{ $rows[$editingRowIndex]['slug'] }}' && $event.detail.id !== 'row-settings') open = false"
                                            class="mb-2 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden"
                                        >
                                            <div class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700/50 cursor-pointer select-none" @click="open = !open; if (open) $dispatch('sidebar-group-opened', { slug: '{{ $rows[$editingRowIndex]['slug'] }}', id: 'row-settings' })">
                                                <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200 flex-1 truncate">Row Settings</span>
                                                @if ($orphanHasClassesFields)
                                                    <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode); if (isActive && open) { open = false; } else { groupMode = 'design'; open = true; $dispatch('sidebar-group-opened', { slug: '{{ $rows[$editingRowIndex]['slug'] }}', id: 'row-settings' }); }"
                                                        :class="(groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode)) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        title="Design"><flux:icon name="paint-brush" class="size-3.5" /></button>
                                                @endif
                                                @if ($orphanHasAdvancedFields)
                                                    <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'advanced' : advancedMode; if (isActive && open) { open = false; } else { groupMode = 'advanced'; open = true; $dispatch('sidebar-group-opened', { slug: '{{ $rows[$editingRowIndex]['slug'] }}', id: 'row-settings' }); }"
                                                        :class="(groupMode !== null ? groupMode === 'advanced' : advancedMode) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        title="Advanced"><flux:icon name="code-bracket" class="size-3.5" /></button>
                                                @endif
                                            </div>
                                            <div x-show="open" x-collapse class="border-t border-zinc-200 dark:border-zinc-700 p-3 space-y-4">
                                                @foreach ($orphanedFields as $field)
                                                    @include('pages.dashboard.pages.partials.content-field', ['field' => $field])
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    <div class="space-y-2" x-data="{ dragging: null, over: null }">
                                        @foreach ($dlComponents as $comp)
                                            @php
                                                $compFields = collect($contentFields)->filter(fn ($f) => in_array($f['key'], $comp['fieldKeys']));
                                                $compShowField = $compFields->first(fn ($f) => $f['type'] === 'toggle' && str_starts_with($f['key'], 'toggle_'));
                                                $headerToggleField = null;
                                                if ($compShowField) {
                                                    $showPrefix = str_replace('toggle_', '', $compShowField['key']);
                                                    $otherFields = $compFields->reject(fn ($f) => $f['key'] === $compShowField['key']);
                                                    $isCompToggle = $otherFields->every(fn ($f) => $f['type'] === 'toggle' || str_ends_with($f['key'], '_new_tab') || str_contains($f['key'], $showPrefix));
                                                    if ($isCompToggle) {
                                                        $headerToggleField = $compShowField;
                                                    }
                                                }
                                                $bodyFields = $headerToggleField
                                                    ? $compFields->reject(fn ($f) => $f['key'] === $headerToggleField['key'])
                                                    : $compFields;
                                                $compHasContentFields = $bodyFields->contains(fn ($f) => ! in_array($f['type'], ['classes', 'object_fit', 'border_radius', 'bg_position', 'bg_size', 'bg_repeat', 'id', 'attrs']));
                                                $compHasClassesFields = $bodyFields->contains(fn ($f) => in_array($f['type'], ['classes', 'object_fit', 'border_radius', 'bg_position', 'bg_size', 'bg_repeat']));
                                                $compHasAdvancedFields = $bodyFields->contains(fn ($f) => in_array($f['type'], ['id', 'attrs']));
                                                $isFirst = $comp['index'] === 0;
                                                $isLast = $comp['index'] === count($dlComponents) - 1;
                                            @endphp
                                            <div
                                                class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden transition-colors"
                                                x-data="{ open: false, groupMode: null }"
                                                data-group-id="{{ $comp['attrs']['prefix'] ?? $comp['slug'] }}"
                                                @set-group-open.window="open = $event.detail.value"
                                                @set-group-mode.window="groupMode = null"
                                                @open-group.window="open = ($event.detail.group === '{{ $comp['attrs']['prefix'] ?? $comp['slug'] }}')"
                                                @sidebar-group-opened.window="if ($event.detail.slug === '{{ $rows[$editingRowIndex]['slug'] }}' && $event.detail.id !== '{{ $comp['attrs']['prefix'] ?? $comp['slug'] }}') open = false"
                                                draggable="true"
                                                @dragstart="dragging = {{ $comp['index'] }}"
                                                @dragover.prevent="over = {{ $comp['index'] }}"
                                                @drop="if (dragging !== null) { $wire.reorderComponents(dragging, over); } dragging = null; over = null"
                                                @dragend="dragging = null; over = null"
                                                :style="{
                                                    opacity: dragging === {{ $comp['index'] }} ? '0.4' : '',
                                                    'border-top': over === {{ $comp['index'] }} && dragging !== null && dragging > {{ $comp['index'] }} ? '2px solid var(--color-primary)' : '',
                                                    'border-bottom': over === {{ $comp['index'] }} && dragging !== null && dragging < {{ $comp['index'] }} ? '2px solid var(--color-primary)' : ''
                                                }"
                                            >
                                                @php $compAccordionId = $comp['attrs']['prefix'] ?? $comp['slug']; $compAccordionSlug = $rows[$editingRowIndex]['slug']; @endphp
                                                <div class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700/50 cursor-pointer select-none" @click="if ({{ $compHasContentFields ? 'true' : 'false' }} || designMode || advancedMode || groupMode !== null) { open = !open; if (open) $dispatch('sidebar-group-opened', { slug: '{{ $compAccordionSlug }}', id: '{{ $compAccordionId }}' }); }">
                                                    <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200 flex-1 truncate">{{ $comp['name'] }}</span>
                                                    @if ($compHasContentFields)
                                                        <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'content' : (!designMode && !advancedMode); if (isActive && open) { open = false; } else { groupMode = 'content'; open = true; $dispatch('sidebar-group-opened', { slug: '{{ $compAccordionSlug }}', id: '{{ $compAccordionId }}' }); }"
                                                            :class="(groupMode !== null ? groupMode === 'content' : (!designMode && !advancedMode)) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                            title="Content"><flux:icon name="document-text" class="size-3.5" /></button>
                                                    @endif
                                                    @if ($compHasClassesFields)
                                                        <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode); if (isActive && open) { open = false; } else { groupMode = 'design'; open = true; $dispatch('sidebar-group-opened', { slug: '{{ $compAccordionSlug }}', id: '{{ $compAccordionId }}' }); }"
                                                            :class="(groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode)) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                            title="Design"><flux:icon name="paint-brush" class="size-3.5" /></button>
                                                    @endif
                                                    @if ($compHasAdvancedFields)
                                                        <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'advanced' : advancedMode; if (isActive && open) { open = false; } else { groupMode = 'advanced'; open = true; $dispatch('sidebar-group-opened', { slug: '{{ $compAccordionSlug }}', id: '{{ $compAccordionId }}' }); }"
                                                            :class="(groupMode !== null ? groupMode === 'advanced' : advancedMode) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                            title="Advanced"><flux:icon name="code-bracket" class="size-3.5" /></button>
                                                    @endif
                                                    @if ($headerToggleField)
                                                        <flux:switch wire:model.live="contentValues.{{ $headerToggleField['key'] }}" @click.stop />
                                                    @endif
                                                </div>
                                                <div x-show="open" x-collapse>
                                                    @if ($bodyFields->isNotEmpty())
                                                        @php
                                                            $flatFields = $bodyFields->filter(fn ($f) => empty($f['subgroup'] ?? null));
                                                            $subgroupedFields = $bodyFields->filter(fn ($f) => ! empty($f['subgroup'] ?? null))->groupBy('subgroup');
                                                        @endphp
                                                        <div class="border-t border-zinc-200 dark:border-zinc-700 p-3 space-y-4">
                                                            @foreach ($flatFields as $field)
                                                                @include('pages.dashboard.pages.partials.content-field', ['field' => $field])
                                                            @endforeach
                                                            @foreach ($subgroupedFields as $subgroupKey => $subFields)
                                                                @php
                                                                    $subgroupToggle = $subFields->first(fn ($f) => $f['type'] === 'toggle' && str_starts_with($f['key'], 'toggle_'));
                                                                    $subgroupBodyFields = $subgroupToggle
                                                                        ? $subFields->reject(fn ($f) => $f['key'] === $subgroupToggle['key'])
                                                                        : $subFields;
                                                                @endphp
                                                                <div x-data="{ open: true }" class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
                                                                    @open-subgroup.window="open = ($event.detail.subgroup === '{{ $subgroupKey }}')">
                                                                    <button type="button" @click="open = !open"
                                                                        class="w-full flex items-center justify-between px-3 py-2 bg-zinc-50 dark:bg-zinc-800 text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                                                                        <span>{{ ucwords(str_replace('_', ' ', $subgroupKey)) }}</span>
                                                                        <div class="flex items-center gap-2">
                                                                            @if ($subgroupToggle)
                                                                                <flux:switch wire:model.live="contentValues.{{ $subgroupToggle['key'] }}" @click.stop />
                                                                            @endif
                                                                            <flux:icon name="chevron-down" class="size-3.5 transition-transform duration-200" x-bind:class="open ? '' : '-rotate-90'" />
                                                                        </div>
                                                                    </button>
                                                                    <div x-show="open" x-collapse class="space-y-4 p-3">
                                                                        @foreach ($subgroupBodyFields as $field)
                                                                            @include('pages.dashboard.pages.partials.content-field', ['field' => $field])
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="relative flex items-center px-2 py-1.5 border-t border-zinc-200 dark:border-zinc-700">
                                                    <div class="flex items-center gap-0.5">
                                                        <flux:button
                                                            wire:click="moveComponentUp({{ $comp['index'] }})"
                                                            variant="ghost" size="sm" icon="arrow-up"
                                                            :disabled="$isFirst"
                                                            :class="$isFirst ? 'opacity-15!' : ''"
                                                            title="Move up" :loading="false"
                                                        />
                                                        <flux:button
                                                            wire:click="moveComponentDown({{ $comp['index'] }})"
                                                            variant="ghost" size="sm" icon="arrow-down"
                                                            :disabled="$isLast"
                                                            :class="$isLast ? 'opacity-15!' : ''"
                                                            title="Move down" :loading="false"
                                                        />
                                                        <flux:icon name="bars-2" class="size-4 text-zinc-400 dark:text-zinc-500 cursor-grab active:cursor-grabbing mx-2" title="Drag to reorder" />
                                                    </div>
                                                    <div class="flex items-center gap-0.5 ml-auto">
                                                        <flux:button
                                                            wire:click="deleteComponent({{ $comp['index'] }})"
                                                            wire:confirm="Delete this component?"
                                                            variant="ghost" size="sm" icon="trash"
                                                            class="text-red-500 dark:text-red-400"
                                                            title="Delete component" :loading="false"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                            @endif

                            <button
                                wire:click="openItemPicker"
                                class="mt-4 w-full py-3 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg text-sm text-zinc-500 dark:text-zinc-400 hover:border-primary hover:text-primary transition-colors flex items-center justify-center gap-2"
                            >
                                <flux:icon name="plus" class="size-4" />
                                {{ __('Add Item') }}
                            </button>
                        </div>

                        <div class="shrink-0 flex gap-2 p-3 border-t border-zinc-200 dark:border-zinc-700">
                            <flux:button @click="editorOpen = false" variant="primary" icon="arrow-left" class="flex-1">
                                {{ __('Back') }}
                            </flux:button>
                            <flux:button wire:click="cancelContentEditor" variant="outline" icon="x-mark" title="Discard changes made since opening this row">
                                {{ __('Cancel') }}
                            </flux:button>
                        </div>
                        @endif
                    </div>

                    {{-- Row list view --}}
                    <div x-show="!editorOpen" class="flex flex-col flex-1">
                        <div class="shrink-0 px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                            <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">{{ __('Page Rows') }}</flux:heading>
                            <div class="flex items-center gap-0.5" x-data="{ allDesignsOpen: false, allAdvancedOpen: false, allBrowseOpen: false }">
                                <flux:tooltip content="Copy all rows" position="bottom">
                                    <button type="button"
                                        x-on:click="localStorage.setItem('webprocms_copied_rows', JSON.stringify($wire.rows)); $dispatch('notify', { message: 'Rows copied.' })"
                                        class="p-1 rounded text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <flux:icon name="clipboard-document" class="size-3.5" />
                                    </button>
                                </flux:tooltip>
                                <flux:tooltip content="Paste all rows" position="bottom">
                                    <button type="button"
                                        x-on:click="
                                            const data = localStorage.getItem('webprocms_copied_rows');
                                            if (data) { $wire.pasteAllRows(JSON.parse(data)); }
                                        "
                                        class="p-1 rounded text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <flux:icon name="clipboard-document-check" class="size-3.5" />
                                    </button>
                                </flux:tooltip>
                                <flux:tooltip content="Remove all rows" position="bottom">
                                    <button type="button"
                                        x-on:click="$flux.modal('confirm-remove-all-rows').show()"
                                        class="p-1 rounded text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <flux:icon name="trash" class="size-3.5" />
                                    </button>
                                </flux:tooltip>
                                <div class="w-px h-3.5 bg-zinc-200 dark:bg-zinc-600 mx-0.5"></div>
                                <flux:tooltip content="Toggle all section designs" position="bottom">
                                    <button type="button"
                                        x-on:click="allAdvancedOpen = false; allBrowseOpen = false; allDesignsOpen = !allDesignsOpen; $dispatch(allDesignsOpen ? 'expand-all-rows' : 'collapse-all-rows')"
                                        :class="allDesignsOpen ? 'text-primary' : 'text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'"
                                        class="p-1 rounded hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <flux:icon name="paint-brush" class="size-3.5" />
                                    </button>
                                </flux:tooltip>
                                <flux:tooltip content="Toggle all section advanced" position="bottom">
                                    <button type="button"
                                        x-on:click="allDesignsOpen = false; allBrowseOpen = false; allAdvancedOpen = !allAdvancedOpen; $dispatch(allAdvancedOpen ? 'expand-all-advanced' : 'collapse-all-rows')"
                                        :class="allAdvancedOpen ? 'text-primary' : 'text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'"
                                        class="p-1 rounded hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <flux:icon name="code-bracket" class="size-3.5" />
                                    </button>
                                </flux:tooltip>
                                <flux:tooltip content="Browse library rows for all" position="bottom">
                                    <button type="button"
                                        x-on:click="allDesignsOpen = false; allAdvancedOpen = false; allBrowseOpen = !allBrowseOpen; if (allBrowseOpen) { $wire.openAllBrowseMode(); $dispatch('expand-all-browse'); } else { $dispatch('collapse-all-rows'); }"
                                        :class="allBrowseOpen ? 'text-primary' : 'text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'"
                                        class="p-1 rounded hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <flux:icon name="rectangle-stack" class="size-3.5" />
                                    </button>
                                </flux:tooltip>
                                <div class="w-px h-4 bg-zinc-200 dark:bg-zinc-700 mx-0.5"></div>
                                <flux:tooltip content="Auto BEM IDs for all rows" position="bottom">
                                    <button type="button"
                                        x-on:click="$flux.modal('confirm-auto-bem').show()"
                                        class="p-1 rounded text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <flux:icon name="sparkles" class="size-3.5" />
                                    </button>
                                </flux:tooltip>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto p-3 space-y-2" x-data="{ dragging: null, over: null }" @click.self="$dispatch('row-deselected')">
                            @forelse ($rows as $index => $row)
                                <div
                                    wire:key="row-item-{{ $row['slug'] }}"
                                    data-row-sidebar-index="{{ $index }}"
                                    x-data="{ panelMode: null, renamingRow: false, rowNameDraft: '' }"
                                    @collapse-all-rows.window="panelMode = null"
                                    @expand-all-rows.window="panelMode = 'design'"
                                    @expand-all-advanced.window="panelMode = 'advanced'"
                                    @expand-all-browse.window="panelMode = 'browse'"
                                    class="rounded-lg border bg-white dark:bg-zinc-900 overflow-hidden transition-colors {{ !empty($row['hidden']) ? 'opacity-60' : '' }}"
                                    :class="editorOpen && {{ $editingRowIndex ?? -1 }} === {{ $index }} ? 'border-primary' : (selectedRowIndex === {{ $index }} ? 'border-primary' : (panelMode !== null ? 'border-primary/50' : 'border-zinc-200 dark:border-zinc-700'))"
                                    @click="selectedRowIndex === {{ $index }} ? $dispatch('row-deselected') : $dispatch('row-selected', { index: {{ $index }} })"
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
                                    {{-- Row header: name + mode icons + visibility toggle --}}
                                    <div class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700/50">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <div x-show="!renamingRow" class="group flex items-center gap-1 min-w-0 flex-1">
                                                    <span
                                                        class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate cursor-pointer hover:text-primary transition-colors"
                                                        title="Click to edit content"
                                                        @click.stop="$wire.openContentEditor({{ $index }})"
                                                    >{{ $row['name'] }}</span>
                                                    <button
                                                        type="button"
                                                        class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity p-0.5 rounded text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                                                        title="Rename row"
                                                        @click.stop="renamingRow = true; rowNameDraft = '{{ addslashes($row['name']) }}'; $nextTick(() => $refs.rowNameInput_{{ $index }}.select())"
                                                    ><flux:icon name="pencil" class="size-3" /></button>
                                                </div>
                                                <input
                                                    x-show="renamingRow"
                                                    x-ref="rowNameInput_{{ $index }}"
                                                    x-model="rowNameDraft"
                                                    type="text"
                                                    class="text-sm font-medium text-zinc-800 dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-primary rounded px-1 py-0 w-full min-w-0 focus:outline-none focus:ring-1 focus:ring-primary"
                                                    @click.stop
                                                    @keydown.enter.stop="$wire.renameRow({{ $index }}, rowNameDraft); renamingRow = false"
                                                    @keydown.escape.stop="renamingRow = false"
                                                    @blur="$wire.renameRow({{ $index }}, rowNameDraft); renamingRow = false"
                                                />
                                                @if (! empty($row['shared']))
                                                    <flux:badge size="sm" color="blue" class="shrink-0">Shared</flux:badge>
                                                @endif
                                            </div>
                                        </div>
                                        @if (isset($rowDesignDefaults[$row['slug']]))
                                            <button type="button" @click.stop="panelMode = panelMode === 'content' ? null : 'content'"
                                                :class="panelMode === 'content' ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                title="Background image"><flux:icon name="document-text" class="size-3.5" /></button>
                                            <button type="button" @click.stop="panelMode = panelMode === 'design' ? null : 'design'"
                                                :class="panelMode === 'design' ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                title="Edit section styles"><flux:icon name="paint-brush" class="size-3.5" /></button>
                                            <button type="button" @click.stop="panelMode = panelMode === 'advanced' ? null : 'advanced'"
                                                :class="panelMode === 'advanced' ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                title="Advanced settings"><flux:icon name="code-bracket" class="size-3.5" /></button>
                                            <button type="button" @click.stop="panelMode = panelMode === 'browse' ? null : 'browse'; if (panelMode === 'browse') $wire.openBrowseMode({{ $index }})"
                                                :class="panelMode === 'browse' ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                title="Browse library rows for this row"><flux:icon name="rectangle-stack" class="size-3.5" /></button>
                                        @endif
                                        <flux:switch
                                            :checked="empty($row['hidden'])"
                                            @click.stop="$wire.toggleRowVisibility({{ $index }})"
                                            title="{{ !empty($row['hidden']) ? 'Row hidden — click to show' : 'Click to hide row' }}"
                                        />
                                    </div>

                                    {{-- Inline panel: design mode (classes) or advanced mode (section id) --}}
                                    @if (isset($rowDesignDefaults[$row['slug']]))
                                        <div x-show="panelMode !== null" x-collapse class="border-t border-zinc-200 dark:border-zinc-700">
                                            {{-- Content mode: background image --}}
                                            <div x-show="panelMode === 'content'" class="p-3 space-y-2">
                                                <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400 block">Background Image</span>
                                                @if (($rowDesignValues[$row['slug']]['section_bg_image'] ?? '') !== '')
                                                    <div class="relative inline-block">
                                                        <img
                                                            src="{{ \Illuminate\Support\Facades\Storage::url($rowDesignValues[$row['slug']]['section_bg_image']) }}"
                                                            alt=""
                                                            class="h-24 w-full rounded-lg object-cover border border-zinc-200 dark:border-zinc-700"
                                                        >
                                                        <button
                                                            wire:click="removeRowDesignImage('{{ $row['slug'] }}')"
                                                            class="absolute -top-2 -right-2 size-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600"
                                                            title="Remove background image"
                                                        >
                                                            <flux:icon name="x-mark" class="size-3" />
                                                        </button>
                                                    </div>
                                                @endif
                                                <button
                                                    wire:click="openRowDesignImagePicker('{{ $row['slug'] }}')"
                                                    type="button"
                                                    class="flex items-center gap-3 px-4 py-3 w-full border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg cursor-pointer hover:border-primary transition-colors"
                                                >
                                                    <flux:icon name="photo" class="size-5 text-zinc-400 shrink-0" />
                                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                                        {{ ($rowDesignValues[$row['slug']]['section_bg_image'] ?? '') ? 'Replace image…' : 'Pick from Media Library…' }}
                                                    </span>
                                                </button>
                                            </div>
                                            {{-- Design mode --}}
                                            <div x-show="panelMode === 'design'" class="p-3 space-y-3">
                                                <div x-data="{ rowSettingsOpen: false }">
                                                    <button type="button" @click="rowSettingsOpen = !rowSettingsOpen" class="flex items-center gap-1.5 w-full text-left">
                                                        <flux:icon name="chevron-right" class="size-3 text-zinc-400 shrink-0 transition-transform duration-150" x-bind:class="rowSettingsOpen ? 'rotate-90' : ''" />
                                                        <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Row Settings</span>
                                                    </button>
                                                    <div x-show="rowSettingsOpen" x-collapse class="mt-2 pl-3">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Disable Alt Row Background</span>
                                                            <flux:switch
                                                                :checked="($rowDesignValues[$row['slug']]['section_no_alt'] ?? '') === '1'"
                                                                wire:click="toggleNoAltRow('{{ $row['slug'] }}')"
                                                                title="Exclude this row from the alternating background pattern"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                                @if (isset($rowDesignDefaults[$row['slug']]['section_animation']))
                                                    <div x-data="{ animOpen: false }">
                                                        <button type="button" @click="animOpen = !animOpen" class="flex items-center gap-1.5 w-full text-left">
                                                            <flux:icon name="chevron-right" class="size-3 text-zinc-400 shrink-0 transition-transform duration-150" x-bind:class="animOpen ? 'rotate-90' : ''" />
                                                            <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Animation</span>
                                                        </button>
                                                        <div x-show="animOpen" x-collapse class="mt-2 pl-3">
                                                            <div class="grid grid-cols-2 gap-2">
                                                                <div>
                                                                    <span class="text-[10px] uppercase font-semibold text-zinc-400 dark:text-zinc-500 mb-1 block">Entrance</span>
                                                                    <select
                                                                        wire:model.live="rowDesignValues.{{ $row['slug'] }}.section_animation"
                                                                        class="w-full text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                                                    >
                                                                        <option value="">— None —</option>
                                                                        <option value="fade-up">Fade Up</option>
                                                                        <option value="fade-down">Fade Down</option>
                                                                        <option value="fade-left">Fade Left</option>
                                                                        <option value="fade-right">Fade Right</option>
                                                                        <option value="zoom-in">Zoom In</option>
                                                                        <option value="fade">Fade</option>
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <span class="text-[10px] uppercase font-semibold text-zinc-400 dark:text-zinc-500 mb-1 block">Delay</span>
                                                                    <select
                                                                        wire:model.live="rowDesignValues.{{ $row['slug'] }}.section_animation_delay"
                                                                        class="w-full text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                                                    >
                                                                        <option value="">— None —</option>
                                                                        <option value="delay-100">100ms</option>
                                                                        <option value="delay-200">200ms</option>
                                                                        <option value="delay-300">300ms</option>
                                                                        <option value="delay-500">500ms</option>
                                                                        <option value="delay-700">700ms</option>
                                                                        <option value="delay-1000">1000ms</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                {{-- Background options collapsible group --}}
                                                <div x-data="{ bgOpen: false }">
                                                    <button type="button" @click="bgOpen = !bgOpen" class="flex items-center gap-1.5 w-full text-left">
                                                        <flux:icon name="chevron-right" class="size-3 text-zinc-400 shrink-0 transition-transform duration-150" x-bind:class="bgOpen ? 'rotate-90' : ''" />
                                                        <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Background Options</span>
                                                    </button>
                                                    <div x-show="bgOpen" x-collapse class="mt-2 space-y-2 pl-3">
                                                        @foreach (['section_bg_position' => 'Position', 'section_bg_size' => 'Size', 'section_bg_repeat' => 'Repeat'] as $bgKey => $bgLabel)
                                                            @if (isset($rowDesignDefaults[$row['slug']][$bgKey]))
                                                                <div>
                                                                    <span class="text-[10px] uppercase font-semibold text-zinc-400 dark:text-zinc-500 mb-1 block">{{ $bgLabel }}</span>
                                                                    <select
                                                                        wire:model.live="rowDesignValues.{{ $row['slug'] }}.{{ $bgKey }}"
                                                                        class="w-full text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                                                    >
                                                                        @if ($bgKey === 'section_bg_position')
                                                                            <option value="">— default —</option>
                                                                            <option value="center">Center</option>
                                                                            <option value="top">Top</option>
                                                                            <option value="bottom">Bottom</option>
                                                                            <option value="left">Left</option>
                                                                            <option value="right">Right</option>
                                                                            <option value="left-top">Top Left</option>
                                                                            <option value="left-bottom">Bottom Left</option>
                                                                            <option value="right-top">Top Right</option>
                                                                            <option value="right-bottom">Bottom Right</option>
                                                                        @elseif ($bgKey === 'section_bg_size')
                                                                            <option value="">— default —</option>
                                                                            <option value="cover">Cover — scale to fill</option>
                                                                            <option value="contain">Contain — show whole image</option>
                                                                            <option value="auto">Auto — original size</option>
                                                                        @else
                                                                            <option value="">— default —</option>
                                                                            <option value="no-repeat">No Repeat</option>
                                                                            <option value="repeat">Repeat (tile)</option>
                                                                            <option value="repeat-x">Repeat Horizontally</option>
                                                                            <option value="repeat-y">Repeat Vertically</option>
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div x-data="{ classesOpen: false }">
                                                    <button type="button" @click="classesOpen = !classesOpen" class="flex items-center gap-1.5 w-full text-left">
                                                        <flux:icon name="chevron-right" class="size-3 text-zinc-400 shrink-0 transition-transform duration-150" x-bind:class="classesOpen ? 'rotate-90' : ''" />
                                                        <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Classes</span>
                                                    </button>
                                                    <div x-show="classesOpen" x-collapse class="mt-2 pl-3 space-y-3">
                                                @foreach (['section_classes' => 'Section Classes', 'section_container_classes' => 'Container Classes'] as $fieldKey => $fieldLabel)
                                                    @if (isset($rowDesignDefaults[$row['slug']][$fieldKey]))
                                                        <div>
                                                            <div class="flex items-center justify-between mb-1.5">
                                                                <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">{{ $fieldLabel }}</span>
                                                                <button wire:click="resetRowDesignField('{{ $row['slug'] }}', '{{ $fieldKey }}')" type="button" class="text-xs text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">Reset</button>
                                                            </div>
                                                            <div x-data="twAutocomplete('{{ $row['slug'] }}_{{ $fieldKey }}')" class="relative">
                                                                <textarea
                                                                    x-ref="input"
                                                                    wire:model.live.debounce.400ms="rowDesignValues.{{ $row['slug'] }}.{{ $fieldKey }}"
                                                                    rows="2"
                                                                    x-on:input="suggest($event)"
                                                                    x-on:keydown="handleKey($event)"
                                                                    x-on:blur="delayClose()"
                                                                    placeholder="{{ $rowDesignDefaults[$row['slug']][$fieldKey] }}"
                                                                    class="w-full font-mono text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                                                ></textarea>
                                                                <div
                                                                    x-show="open"
                                                                    x-transition:enter="transition ease-out duration-75"
                                                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                                                    x-transition:enter-end="opacity-100 translate-y-0"
                                                                    x-bind:style="dropdownStyle"
                                                                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg overflow-y-auto max-h-48"
                                                                >
                                                                    <template x-for="(item, i) in suggestions" :key="item">
                                                                        <button
                                                                            type="button"
                                                                            @mousedown.prevent="pick(item)"
                                                                            :class="i === activeIndex ? 'bg-primary text-white' : 'text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700'"
                                                                            class="block w-full text-left px-3 py-1.5 text-xs font-mono transition-colors"
                                                                            x-text="item"
                                                                        ></button>
                                                                    </template>
                                                                </div>
                                                                <div class="flex items-center gap-1.5 mt-1">
                                                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Tailwind CSS classes. Tab or Enter to complete.</p>
                                                                    <button
                                                                        @click="$flux.modal('tailwind-css-help').show()"
                                                                        type="button"
                                                                        class="text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors shrink-0"
                                                                        title="Tailwind CSS reference"
                                                                    >
                                                                        <flux:icon name="question-mark-circle" class="size-3.5" />
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Advanced mode --}}
                                            <div x-show="panelMode === 'advanced'" class="p-3 space-y-3">
                                                @if (isset($rowDesignDefaults[$row['slug']]['section_id']))
                                                    <div>
                                                        <div class="flex items-center justify-between mb-1.5">
                                                            <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Section ID</span>
                                                            <button wire:click="resetRowDesignField('{{ $row['slug'] }}', 'section_id')" type="button" class="text-xs text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">Reset</button>
                                                        </div>
                                                        <input
                                                            type="text"
                                                            wire:model.live.debounce.400ms="rowDesignValues.{{ $row['slug'] }}.section_id"
                                                            placeholder="e.g. about-us"
                                                            class="w-full font-mono text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                                        />
                                                    </div>
                                                @endif
                                                <button
                                                    wire:click="applyAutoBem({{ $index }})"
                                                    type="button"
                                                    class="w-full flex items-center justify-center gap-1.5 text-xs font-medium py-1.5 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 hover:text-primary hover:border-primary transition-colors"
                                                    title="Auto-generate BEM IDs for all elements in this row. Uses Section ID (or row name) as the BEM block."
                                                >
                                                    <flux:icon name="sparkles" class="size-3.5" />
                                                    Auto BEM IDs
                                                </button>
                                            </div>
                                            {{-- Browse mode info --}}
                                            <div x-show="panelMode === 'browse'" class="px-3 py-2 border-t border-zinc-200 dark:border-zinc-700">
                                                @php $bd = $rowBrowseData[$row['slug']] ?? null; @endphp
                                                @if ($bd)
                                                    <div class="flex items-center gap-2">
                                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate flex-1" title="{{ $bd['rowOptions'][$bd['position']]['name'] ?? $row['name'] }}">
                                                            {{ $bd['rowOptions'][$bd['position']]['name'] ?? $row['name'] }}
                                                        </p>
                                                        <button
                                                            type="button"
                                                            wire:click="reloadRowFromLibrary('{{ $row['slug'] }}')"
                                                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary transition-colors shrink-0"
                                                            title="Reload row from design library"
                                                        >
                                                            <flux:icon name="arrow-path" class="size-3.5" />
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Row actions --}}
                                    <div class="relative flex items-center px-2 py-2">
                                        {{-- Standard action bar --}}
                                        <div x-show="panelMode !== 'browse'" class="contents">
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

                                                <flux:icon name="bars-2" class="size-4 text-zinc-400 dark:text-zinc-500 cursor-grab active:cursor-grabbing mx-2" title="Drag to reorder" />

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
                                                @if (empty($row['shared']))
                                                    <flux:button
                                                        wire:click="makeRowShared({{ $index }})"
                                                        wire:confirm="Make this row shared? It will be available to insert on other pages and changes will affect all pages using it."
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="share"
                                                        title="Make shared"
                                                    />
                                                @endif
                                                <flux:button
                                                    x-on:click="$wire.set('pendingRemoveRowIndex', {{ $index }}); $flux.modal('confirm-remove-row').show()"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="trash"
                                                    class="text-red-500 dark:text-red-400"
                                                    title="Remove row"
                                                    :loading="false"
                                                />
                                            </div>
                                        </div>

                                        {{-- Browse mode action bar --}}
                                        <div x-show="panelMode === 'browse'" class="flex items-center gap-2 w-full">
                                            @php $bd = $rowBrowseData[$row['slug']] ?? null; @endphp
                                            @if ($bd)
                                                <select
                                                    x-on:change="$wire.browseCategoryChange('{{ $row['slug'] }}', $event.target.value)"
                                                    class="w-28 shrink-0 text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                                >
                                                    @foreach ($bd['categoryOptions'] as $cat)
                                                        <option value="{{ $cat['value'] }}" {{ $bd['category'] === $cat['value'] ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="flex items-center gap-1 flex-1 min-w-0">
                                                    <select
                                                        x-on:change="$wire.browseRowJump('{{ $row['slug'] }}', parseInt($event.target.value))"
                                                        class="flex-1 min-w-0 text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                                    >
                                                        @foreach ($bd['rowOptions'] as $i => $opt)
                                                            <option value="{{ $opt['id'] }}" {{ $bd['position'] === $i ? 'selected' : '' }}>{{ $opt['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <flux:button
                                                        wire:click="browseRowStep('{{ $row['slug'] }}', -1)"
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="arrow-left"
                                                        title="Previous row"
                                                        :loading="false"
                                                        :disabled="count($bd['rowOptions']) <= 1"
                                                    />
                                                    <flux:button
                                                        wire:click="browseRowStep('{{ $row['slug'] }}', 1)"
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="arrow-right"
                                                        title="Next row"
                                                        :loading="false"
                                                        :disabled="count($bd['rowOptions']) <= 1"
                                                    />
                                                </div>
                                            @endif
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
                    </div>
                </div>

                {{-- Left panel: iframe preview --}}
                <div class="flex-1 flex flex-col bg-zinc-100 dark:bg-zinc-950 overflow-auto order-first">
@if ($previewUrl)
                        <div
                            class="flex-1 flex flex-col mx-auto w-full transition-all duration-300"
                            :style="previewWidth ? 'max-width: ' + previewWidth : 'max-width: 100%'"
                        >
                            <div
                                class="flex-1 relative"
                                x-init="document.getElementById('page-preview-a').src = {{ Js::from($previewUrl) }}"
                                x-on:refresh-preview.window="refreshPreview($event.detail.url)"
                            >
                                <iframe wire:ignore id="page-preview-a" class="absolute inset-0 w-full h-full border-0" style="opacity:1;z-index:2;transition:opacity 0.15s ease"></iframe>
                                <iframe wire:ignore id="page-preview-b" class="absolute inset-0 w-full h-full border-0" style="opacity:0;z-index:1;transition:opacity 0.15s ease"></iframe>
                            </div>
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

    {{-- Shortcode Reference modal --}}
    @include('partials.shortcode-modal')

    {{-- AI Generate modal --}}
    @include('partials.ai-generate-modal')

    {{-- Lorem Ipsum modal --}}
    @include('partials.lorem-ipsum-modal')

    {{-- SEO / Page Settings modal --}}
    <flux:modal wire:model="showSeoModal" class="w-full max-w-lg">
        <flux:heading size="lg">Page Settings</flux:heading>

        <div class="mt-6 space-y-4">
            {{-- Basic --}}
            <div x-data="{ basicOpen: false }" x-on:open-settings-section.window="basicOpen = ($event.detail === 'basic')">
                <button
                    type="button"
                    @click="basicOpen = !basicOpen"
                    class="w-full flex items-center justify-between text-left"
                >
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-200">Basic</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Status and URL settings.</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-600 dark:text-zinc-300 transition-transform duration-200 shrink-0 ml-3" :class="basicOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="basicOpen" x-transition class="mt-3 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-4">
                    {{-- Page Name --}}
                    @if (preg_match('#^pages/(?!dashboard/).*⚡[^/]+\.blade\.php$#u', $file))
                        <flux:input
                            label="Page Name"
                            wire:model="pageName"
                            placeholder="About Us"
                            description="Shown in the page title banner row. Separate from the SEO title — can be shorter or longer."
                        />
                    @endif

                    {{-- Visibility / Status --}}
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

                    @if (preg_match('#^pages/⚡[^/]+\.blade\.php$#u', $file))
                        {{-- Slug --}}
                        <flux:field>
                            <flux:label>Slug</flux:label>
                            <flux:input wire:model.live.debounce.300ms="pageSlug" placeholder="my-page-slug" />
                            <flux:description>URL path: /{{ $pageSlug ?: '…' }}</flux:description>
                            <flux:error name="pageSlug" />
                        </flux:field>

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
                    @endif
                </div>
            </div>

            {{-- SEO --}}
            <div x-data="{ seoOpen: false }" x-on:open-settings-section.window="seoOpen = ($event.detail === 'seo')" class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
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

                <div x-show="seoOpen" x-transition class="mt-3 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-4">
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

            {{-- Design --}}
            <div x-data="{ designOpen: false }" x-on:open-settings-section.window="designOpen = ($event.detail === 'design')" class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <button
                    type="button"
                    @click="designOpen = !designOpen"
                    class="w-full flex items-center justify-between text-left"
                >
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-200">Design</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Page-level design overrides.</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-600 dark:text-zinc-300 transition-transform duration-200 shrink-0 ml-3" :class="designOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="designOpen" x-transition class="mt-3 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-4">
                    <flux:switch
                        label="Alt Row Backgrounds"
                        description="Apply the alternating background color (--color-alt-row) to every other section on this page. Disable to show all sections with their default backgrounds."
                        wire:model.live="altRowsEnabled"
                    />
                </div>
            </div>

            {{-- Advanced --}}
            @if (preg_match('#^pages/(?!dashboard/).*⚡[^/]+\.blade\.php$#u', $file))
            <div x-data="{ advancedOpen: false }" x-on:open-settings-section.window="advancedOpen = ($event.detail === 'advanced')" class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <button
                    type="button"
                    @click="advancedOpen = !advancedOpen"
                    class="w-full flex items-center justify-between text-left"
                >
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-200">Advanced</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Caching and access control.</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-600 dark:text-zinc-300 transition-transform duration-200 shrink-0 ml-3" :class="advancedOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="advancedOpen" x-transition class="mt-3 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-4">
                    {{-- Cache --}}
                    <flux:switch
                        label="Cache response"
                        :description="'Full-page cache this page for ' . \Carbon\CarbonInterval::seconds(config('responsecache.cache_lifetime_in_seconds'))->cascade()->forHumans() . ' for unauthenticated visitors. Disable for pages with dynamic or user-specific content.'"
                        wire:model="isCachedPage"
                    />

                    {{-- Login Required --}}
                    <div x-data>
                        <flux:switch
                            label="Require login"
                            description="Only authenticated users can access this page."
                            wire:model.live="requiresLogin"
                        />

                        <div x-show="$wire.requiresLogin" x-transition class="mt-3">
                            <flux:field>
                                <flux:label>Required role</flux:label>
                                <flux:select wire:model="requiredRole">
                                    <flux:select.option value="">Any logged-in user</flux:select.option>
                                    <flux:select.option value="manager">Manager or above</flux:select.option>
                                    <flux:select.option value="admin">Admin or above</flux:select.option>
                                    <flux:select.option value="super">Super only</flux:select.option>
                                </flux:select>
                                <flux:description>Restrict access to users with at least this role.</flux:description>
                                <flux:error name="requiredRole" />
                            </flux:field>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Redirect --}}
            <div x-data="{ redirectOpen: false }" x-on:open-settings-section.window="redirectOpen = ($event.detail === 'redirect')" class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
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

                <div x-show="redirectOpen" x-transition class="mt-3 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-3">
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

    {{-- Media Library Picker --}}
    <flux:modal wire:model="showMediaPicker" name="media-picker" class="p-0!" style="max-width: 75vw; width: 75vw;">
        @if ($showMediaPicker)
            <livewire:pages::dashboard.media-library.picker
                :field-key="$mediaPickerKey"
                :default-category-slug="$mediaPickerCategorySlug"
                :key="'media-picker-'.$mediaPickerKey"
            />
        @endif
    </flux:modal>

    {{-- Gallery Picker (multi-select) --}}
    <flux:modal wire:model="showGalleryPicker" name="gallery-picker" class="p-0!" style="max-width: 75vw; width: 75vw;">
        @if ($showGalleryPicker)
            <livewire:pages::dashboard.media-library.picker
                :field-key="$pendingGalleryFieldKey"
                :multi-select="true"
                :key="'gallery-picker-'.$pendingGalleryFieldKey"
            />
        @endif
    </flux:modal>

    <flux:modal name="tailwind-css-help" class="w-full" style="max-width: 75vw;">
        <flux:heading size="lg">Tailwind CSS Reference</flux:heading>
        <flux:text class="mt-1 mb-6">Quick reference for writing classes in the editor. Tab or Enter to autocomplete.</flux:text>

        <div class="grid grid-cols-3 gap-6 text-sm">
            {{-- Column 1: Responsive & Variants --}}
            <div class="space-y-5">
                <div>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Responsive</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Apply at a breakpoint and up:</p>
                    <code class="block font-mono text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1.5 rounded">text-sm md:text-lg lg:text-xl</code>
                </div>
                <div>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Dark mode</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Prefix with <span class="font-mono">dark:</span>:</p>
                    <code class="block font-mono text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1.5 rounded">bg-white dark:bg-zinc-900</code>
                </div>
                <div>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Hover &amp; state</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Prefix with <span class="font-mono">hover:</span>, <span class="font-mono">focus:</span>, etc.:</p>
                    <code class="block font-mono text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1.5 rounded">hover:opacity-80 hover:scale-105</code>
                </div>
                <div>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Force override</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Prefix with <span class="font-mono">!</span> to mark important:</p>
                    <code class="block font-mono text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1.5 rounded">!text-center !mt-0</code>
                </div>
            </div>

            {{-- Column 2: Colors & Tokens --}}
            <div class="space-y-5">
                <div>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Theme colors</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Your brand colors from Branding settings:</p>
                    <code class="block font-mono text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1.5 rounded">text-primary bg-primary border-primary</code>
                </div>
                <div>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Section spacing</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Controlled together via Branding:</p>
                    <code class="block font-mono text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1.5 rounded leading-relaxed">py-section &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;standard rows<br>py-section-banner compact CTAs<br>py-section-hero &nbsp;&nbsp;hero sections</code>
                </div>
                <div>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Other tokens</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Site-wide design tokens:</p>
                    <code class="block font-mono text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1.5 rounded">font-heading rounded-card shadow-card</code>
                </div>
            </div>

            {{-- Column 3: Special cases --}}
            <div class="space-y-5">
                <div>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Background overlays</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Semi-transparent overlays for banners:</p>
                    <code class="block font-mono text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1.5 rounded leading-relaxed">bg-black/50<br>bg-zinc-900/80<br>bg-zinc-600/80<br>bg-[#6b6b6b]/90</code>
                </div>
                <div>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Arbitrary values</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Hardcode any value in square brackets:</p>
                    <code class="block font-mono text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1.5 rounded">text-[1.25rem] w-[320px]</code>
                </div>
                <div>
                    <p class="font-semibold text-zinc-700 dark:text-zinc-200 mb-1">Remove all styles</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Type <span class="font-mono">none</span> to apply no classes:</p>
                    <code class="block font-mono text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 px-2 py-1.5 rounded">none</code>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <flux:modal.close>
                <flux:button>Close</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <flux:modal name="confirm-remove-all-rows" class="w-full max-w-sm">
        <flux:heading size="lg">Remove all rows?</flux:heading>
        <flux:text class="mt-2">This will remove all rows from this page. Any saved content for these rows will also be deleted.</flux:text>
        <div class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:modal.close>
                <flux:button variant="danger" wire:click="removeAllRows">Remove All</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <flux:modal name="confirm-auto-bem" class="w-full max-w-sm">
        <flux:heading size="lg">Auto BEM all rows?</flux:heading>
        <flux:text class="mt-2">Auto-generate BEM IDs for all rows on this page. Existing IDs will be updated.</flux:text>
        <div class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:modal.close>
                <flux:button variant="primary" wire:click="applyAutoBemAllRows">Apply</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <flux:modal name="confirm-remove-row" class="w-full max-w-sm">
        <flux:heading size="lg">Remove row?</flux:heading>
        <flux:text class="mt-2">This will remove the row from the page. Any saved content for this row will also be deleted.</flux:text>
        <div class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:modal.close>
                <flux:button variant="danger" wire:click="removeRow($wire.pendingRemoveRowIndex)">Remove</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

</div>
