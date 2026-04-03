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

    /** @var array<int, array{rows: array<int, array{slug: string, name: string, blade: string}>, overrides: array<string, mixed>}> */
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

    public bool $showAccessibilityModal = false;

    public string $accessibilityKey = '';

    public int $accessibilitySaveCount = 0;

    public int $accessibilityScannedSaveCount = -1;

    /** @var array<int, array{severity: string, type: string, message: string, row: string}> */
    public array $accessibilityIssues = [];

    #[Validate('required|in:draft,published,unlisted,unpublished')]
    public string $pageStatus = 'published';

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

    public ?int $pendingMakeSharedRowIndex = null;

    public string $pendingGridItemSubKey = '';

    public bool $showGalleryPicker = false;

    public string $pendingGalleryFieldKey = '';

    /** @var array<string, array<string, string>> */
    public array $rowDesignValues = [];

    /** @var list<array{id: string, label: string}> */
    public array $rowStylePresets = [];

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
        $textKeys = ['section_id', 'section_animation', 'section_animation_delay', 'section_bg_position', 'section_bg_size', 'section_bg_repeat', 'section_style'];
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
        $textKeys = ['section_id', 'section_animation', 'section_animation_delay', 'section_bg_position', 'section_bg_size', 'section_bg_repeat', 'section_style'];
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
        $this->rowHistory = [['rows' => $this->rows, 'overrides' => session('editor_draft_overrides', [])]];
        $this->historyIndex = 0;
        $this->savedHistoryIndex = 0;
        $this->liveUrl = $service->getRouteForFile($relativePath);
        $this->previewUrl = route('design-library.preview', ['token' => $service->previewToken($relativePath)]);

        $this->showContentEditor = false;
        $this->editingRowIndex = null;

        $this->loadPreviewContextOptions();
        $this->refreshPreview();

        if ($this->pageSlug) {
            $this->accessibilityKey = $this->pageSlug;
        } else {
            $this->accessibilityKey = preg_replace('/[^a-z0-9_-]/', '_', strtolower(str_replace(['.blade.php', '⚡'], ['', ''], $this->file)));
        }

        $this->accessibilitySaveCount = (int) \App\Models\Setting::get("accessibility.{$this->accessibilityKey}.save_count", 0);
        $this->accessibilityScannedSaveCount = (int) \App\Models\Setting::get("accessibility.{$this->accessibilityKey}.scanned_save_count", -1);
        $this->accessibilityIssues = \App\Models\Setting::get("accessibility.{$this->accessibilityKey}.issues", []) ?: [];
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
            fn ($f) => ! in_array($f['key'], ['section_classes', 'section_container_classes', 'section_id', 'section_attrs', 'section_animation', 'section_animation_delay', 'section_bg_image', 'section_bg_position', 'section_bg_size', 'section_bg_repeat', 'section_style'], true)
        ));
        $this->pendingImageKey = '';
        $this->pendingImageUpload = null;

        // Inject per-language variant fields for translatable content.
        $activeLanguages = \App\Models\Setting::get('site.languages', [['code' => 'en']]);
        $nonEnglishLangs = array_values(array_filter($activeLanguages, fn ($l) => $l['code'] !== 'en'));
        $multiLang = count($nonEnglishLangs) > 0;

        if ($multiLang) {
            $expanded = [];
            $translatableTypes = ['text', 'richtext'];
            $nonTranslatableEndings = ['_url', '_htag', '_alt', '_new_tab', '_classes', '_id', '_attrs'];

            foreach ($this->contentFields as $field) {
                $isTranslatable = in_array($field['type'], $translatableTypes, true)
                    && ! str_starts_with($field['key'], 'toggle_')
                    && ! collect($nonTranslatableEndings)->contains(fn ($suffix) => str_ends_with($field['key'], $suffix));

                if ($isTranslatable) {
                    // Rename the English field to include (EN) suffix.
                    $expanded[] = array_merge($field, ['label' => $field['label'] . ' (EN)']);

                    foreach ($nonEnglishLangs as $lang) {
                        $langCode = $lang['code'];
                        $langLabel = strtoupper($langCode);
                        $expanded[] = array_merge($field, [
                            'key' => $field['key'] . '__' . $langCode,
                            'label' => $field['label'] . ' (' . $langLabel . ')',
                            'default' => '',
                            '_lang_code' => $langCode,
                            '_source_key' => $field['key'],
                        ]);
                    }
                } else {
                    $expanded[] = $field;
                }
            }

            $this->contentFields = $expanded;
        }

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

            if ($draft['type'] !== 'text' || ! str_ends_with($key, '_alt')) {
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
                    if ($draft['value'] === '') {
                        ContentOverride::query()->where('row_slug', $affectedSlug)->where('key', $key)->delete();
                    } else {
                        ContentOverride::updateOrCreate(
                            ['row_slug' => $affectedSlug, 'key' => $key],
                            ['type' => 'text', 'value' => $draft['value']]
                        );
                    }
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

        if ($this->accessibilityKey) {
            $this->accessibilitySaveCount++;
            \App\Models\Setting::set("accessibility.{$this->accessibilityKey}.save_count", $this->accessibilitySaveCount);
            $this->runAccessibilityAudit();
        }

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

    public function pushContentHistory(): void
    {
        $this->pushHistory();
        $this->isDirty = true;
    }

    public function updated(string $name): void
    {
        if (str_starts_with($name, 'contentValues.')) {
            if (session('suppress_next_history_push')) {
                session()->forget('suppress_next_history_push');

                return;
            }

            $this->pushHistory();
            $this->isDirty = true;
        }
    }

    public function popContentHistory(): void
    {
        if ($this->historyIndex <= 0) {
            return;
        }

        $this->rowHistory = array_slice($this->rowHistory, 0, $this->historyIndex + 1);
        array_pop($this->rowHistory);
        $this->historyIndex = count($this->rowHistory) - 1;
        $this->isDirty = $this->historyIndex !== $this->savedHistoryIndex;
        session(['suppress_next_history_push' => true]);
    }

    private function pushHistory(): void
    {
        if ($this->historyIndex < count($this->rowHistory) - 1) {
            $this->rowHistory = array_slice($this->rowHistory, 0, $this->historyIndex + 1);
        }

        $this->rowHistory[] = ['rows' => $this->rows, 'overrides' => session('editor_draft_overrides', [])];
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

        $previousEditingIndex = $this->editingRowIndex;
        $this->historyIndex--;
        $snapshot = $this->rowHistory[$this->historyIndex];
        $this->rows = $snapshot['rows'];
        session(['editor_draft_overrides' => $snapshot['overrides']]);
        $this->loadRowDesignValues();
        $this->isDirty = $this->historyIndex !== $this->savedHistoryIndex;

        if ($previousEditingIndex !== null && isset($this->rows[$previousEditingIndex])) {
            $this->openContentEditor($previousEditingIndex);
            $this->dispatchRichtextResets();
        } else {
            $this->showContentEditor = false;
            $this->editingRowIndex = null;
        }

        $this->refreshPreview();
    }

    public function redo(): void
    {
        if ($this->historyIndex >= count($this->rowHistory) - 1) {
            return;
        }

        $previousEditingIndex = $this->editingRowIndex;
        $this->historyIndex++;
        $snapshot = $this->rowHistory[$this->historyIndex];
        $this->rows = $snapshot['rows'];
        session(['editor_draft_overrides' => $snapshot['overrides']]);
        $this->loadRowDesignValues();
        $this->isDirty = $this->historyIndex !== $this->savedHistoryIndex;

        if ($previousEditingIndex !== null && isset($this->rows[$previousEditingIndex])) {
            $this->openContentEditor($previousEditingIndex);
            $this->dispatchRichtextResets();
        } else {
            $this->showContentEditor = false;
            $this->editingRowIndex = null;
        }

        $this->refreshPreview();
    }

    private function dispatchRichtextResets(): void
    {
        foreach ($this->contentFields as $field) {
            if ($field['type'] === 'richtext') {
                $this->dispatch('content-richtext-reset', key: $field['key'], value: (string) ($this->contentValues[$field['key']] ?? ''));
            }
        }
    }

    private function loadRowDesignValues(): void
    {
        $this->rowDesignDefaults = [];

        foreach ($this->rows as $row) {
            $fields = $this->parseContentFields($row['blade'], $row['slug']);

            foreach ($fields as $field) {
                if (in_array($field['key'], ['section_classes', 'section_container_classes', 'section_id', 'section_animation', 'section_animation_delay', 'section_bg_image', 'section_bg_position', 'section_bg_size', 'section_bg_repeat', 'section_style'], true)) {
                    $this->rowDesignDefaults[$row['slug']][$field['key']] = $field['default'];
                }
            }
        }

        $slugs = array_keys($this->rowDesignDefaults);

        if (empty($slugs)) {
            $this->rowDesignValues = [];

            return;
        }

        $overrides = ContentOverride::query()
            ->whereIn('row_slug', $slugs)
            ->whereIn('key', ['section_classes', 'section_container_classes', 'section_id', 'section_animation', 'section_animation_delay', 'section_bg_image', 'section_bg_position', 'section_bg_size', 'section_bg_repeat', 'section_style'])
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

        $this->rowStylePresets = \App\Models\Setting::get('section_style_presets', []) ?: [];
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

    public function runAccessibilityAudit(): void
    {
        if (! $this->accessibilityKey || empty($this->rows)) {
            return;
        }

        $issues = [];
        $headings = [];
        $drafts = session('editor_draft_overrides', []);

        foreach ($this->rows as $row) {
            $fields = $this->parseContentFields($row['blade'], $row['slug']);

            if (empty($fields)) {
                continue;
            }

            $slugs = array_unique(array_column($fields, 'slug'));
            $overrides = ContentOverride::query()
                ->whereIn('row_slug', $slugs)
                ->get()
                ->keyBy(fn (ContentOverride $o) => $o->row_slug.':'.$o->key);

            $values = [];

            foreach ($fields as $field) {
                $dbKey = $field['slug'].':'.$field['key'];
                $dbValue = $overrides->get($dbKey)?->value;
                $draft = $drafts[$dbKey] ?? null;
                $rawValue = $draft !== null ? ($draft['value'] ?? null) : $dbValue;
                $values[$field['key']] = match (true) {
                    $field['type'] === 'toggle' => ($rawValue !== null ? $rawValue === '1' : $field['default'] === '1'),
                    in_array($field['type'], ['classes', 'grid'], true) => $rawValue ?: $field['default'],
                    default => $rawValue ?? '',
                };
            }

            $fieldMap = collect($fields)->keyBy('key');
            $rowName = $row['name'] ?? Str::title(str_replace(['-', '_'], ' ', Str::before($row['slug'], ':')));

            // Resolve alt text from MediaItem for any image fields (same as the editor does).
            $imagePaths = collect($fields)
                ->filter(fn ($f) => $f['type'] === 'image')
                ->map(fn ($f) => $values[$f['key']] ?? '')
                ->filter()
                ->values()
                ->all();

            $mediaAlts = $imagePaths
                ? MediaItem::query()->whereIn('path', $imagePaths)->pluck('alt', 'path')->all()
                : [];

            // Check image alt text
            foreach ($fields as $field) {
                if ($field['type'] !== 'image') {
                    continue;
                }

                $imageValue = $values[$field['key']] ?? '';

                if (empty($imageValue)) {
                    continue;
                }

                $altKey = $field['key'].'_alt';
                // Prefer MediaItem.alt (canonical), fall back to ContentOverride value.
                $altValue = trim($mediaAlts[$imageValue] ?? $values[$altKey] ?? '');

                if ($altValue === '') {
                    $label = $field['label'] ?? $field['key'];
                    $altField = $fieldMap->get($altKey);
                    $issues[] = [
                        'severity' => 'error',
                        'type' => 'missing-alt',
                        'message' => 'Image "'.ucwords(str_replace('_', ' ', $label)).'" is missing alt text.',
                        'row' => $rowName,
                        'row_slug' => $row['slug'],
                        'field_key' => $altKey,
                        'group' => $altField['group'] ?? $field['group'] ?? '',
                    ];
                }
            }

            // Check grid items for images without alt text
            foreach ($fields as $field) {
                if ($field['type'] !== 'grid') {
                    continue;
                }

                $gridValue = $values[$field['key']] ?? '';
                $items = json_decode($gridValue, true) ?: [];

                foreach ($items as $itemIndex => $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    foreach ($item as $subKey => $subValue) {
                        if (! (str_ends_with($subKey, '_image') || $subKey === 'image') || empty($subValue)) {
                            continue;
                        }

                        $altSubKey = $subKey === 'image' ? 'alt' : substr($subKey, 0, -5).'alt';
                        $altValue = trim($item[$altSubKey] ?? $item[$subKey.'_alt'] ?? '');

                        if ($altValue === '') {
                            $fieldLabel = $field['label'] ?? $field['key'];
                            $issues[] = [
                                'severity' => 'error',
                                'type' => 'missing-alt',
                                'message' => 'Image in "'.ucwords(str_replace('_', ' ', $fieldLabel)).'" (item '.($itemIndex + 1).') is missing alt text.',
                                'row' => $rowName,
                                'row_slug' => $row['slug'],
                                'field_key' => $field['key'],
                                'group' => $field['group'] ?? '',
                            ];
                        }
                    }
                }
            }

            // Collect heading levels (in document order)
            foreach ($fields as $field) {
                if (! str_ends_with($field['key'], '_htag')) {
                    continue;
                }

                $prefix = substr($field['key'], 0, -5);
                $toggleKey = 'toggle_'.$prefix;
                $isVisible = ! array_key_exists($toggleKey, $values) || $values[$toggleKey] === true || $values[$toggleKey] === '1';

                if (! $isVisible) {
                    continue;
                }

                $htag = $values[$field['key']] ?? $field['default'] ?? 'h2';

                if (! empty($htag) && preg_match('/^h([1-6])$/', $htag, $m)) {
                    $headings[] = [
                        'level' => (int) $m[1],
                        'row' => $rowName,
                        'row_slug' => $row['slug'],
                        'field_key' => $field['key'],
                        'group' => $field['group'] ?? '',
                    ];
                }
            }

            // Check link labels (fields ending in _url with a value but empty label)
            foreach ($fields as $field) {
                if (! str_ends_with($field['key'], '_url') && $field['key'] !== 'url') {
                    continue;
                }

                $urlValue = trim($values[$field['key']] ?? '');

                if (empty($urlValue)) {
                    continue;
                }

                $prefix = str_ends_with($field['key'], '_url') ? substr($field['key'], 0, -4) : 'link';
                $toggleKey = 'toggle_'.$prefix;
                $isVisible = ! array_key_exists($toggleKey, $values) || $values[$toggleKey] === true || $values[$toggleKey] === '1';

                if (! $isVisible) {
                    continue;
                }

                $labelValue = trim($values[$prefix] ?? '');

                if ($labelValue === '') {
                    $labelField = $fieldMap->get($prefix);
                    $issues[] = [
                        'severity' => 'warning',
                        'type' => 'empty-link-label',
                        'message' => 'Link "'.ucwords(str_replace('_', ' ', $prefix)).'" has a URL but no label text.',
                        'row' => $rowName,
                        'row_slug' => $row['slug'],
                        'field_key' => $prefix,
                        'group' => $labelField['group'] ?? '',
                    ];
                }
            }
        }

        // Check heading hierarchy across whole page
        if (! empty($headings)) {
            $h1Count = count(array_filter($headings, fn ($h) => $h['level'] === 1));

            if ($h1Count === 0) {
                $issues[] = [
                    'severity' => 'warning',
                    'type' => 'no-h1',
                    'message' => 'No H1 heading found on this page. Every page should have one H1.',
                    'row' => 'Page',
                    'row_slug' => '',
                    'field_key' => '',
                    'group' => '',
                ];
            } elseif ($h1Count > 1) {
                $issues[] = [
                    'severity' => 'error',
                    'type' => 'multiple-h1',
                    'message' => "Multiple H1 headings found ({$h1Count}). A page should have only one H1.",
                    'row' => 'Page',
                    'row_slug' => '',
                    'field_key' => '',
                    'group' => '',
                ];
            }

            $prevLevel = 0;

            foreach ($headings as $heading) {
                if ($prevLevel > 0 && $heading['level'] > $prevLevel + 1) {
                    $skipped = $prevLevel + 1;
                    $issues[] = [
                        'severity' => 'warning',
                        'type' => 'heading-skip',
                        'message' => "Heading jumps from H{$prevLevel} to H{$heading['level']} (skipping H{$skipped}).",
                        'row' => $heading['row'],
                        'row_slug' => $heading['row_slug'],
                        'field_key' => $heading['field_key'],
                        'group' => $heading['group'],
                    ];
                }

                $prevLevel = $heading['level'];
            }
        }

        // Sort: errors first, then warnings
        usort($issues, fn ($a, $b) => ($a['severity'] === 'error' ? 0 : 1) <=> ($b['severity'] === 'error' ? 0 : 1));

        $this->accessibilityIssues = $issues;
        $this->accessibilityScannedSaveCount = $this->accessibilitySaveCount;

        \App\Models\Setting::set("accessibility.{$this->accessibilityKey}.issues", $issues);
        \App\Models\Setting::set("accessibility.{$this->accessibilityKey}.scanned_save_count", $this->accessibilitySaveCount);

        $this->dispatch('notify', message: count($issues) === 0 ? 'No accessibility issues found.' : count($issues).' accessibility issue(s) found.');
    }

    public function navigateToIssue(string $rowSlug, string $fieldKey, string $group): void
    {
        $this->showAccessibilityModal = false;
        $this->dispatch('navigate-to-accessibility-issue', rowSlug: $rowSlug, fieldKey: $fieldKey, group: $group);
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
     * Parse the @previewContext frontmatter comment from a blade file.
     *
     * Expects a line like:
     *   {{-- @previewContext model=\App\Models\Event label=title value=slug routeParam=slug orderBy=start_date --}}
     *
     * @return array<string, string>|null
     */
    private function parsePreviewContextFrontmatter(): ?array
    {
        $path = resource_path('views/'.$this->file);

        if (! file_exists($path)) {
            return null;
        }

        $head = file_get_contents($path, false, null, 0, 512);

        if (! preg_match('/\{\{--\s*@previewContext\s+(.+?)\s*--\}\}/s', $head, $match)) {
            return null;
        }

        $attrs = [];
        preg_match_all('/(\w+)=([^\s]+)/', $match[1], $pairs, PREG_SET_ORDER);

        foreach ($pairs as $pair) {
            $attrs[$pair[1]] = $pair[2];
        }

        foreach (['model', 'label', 'value', 'routeParam', 'orderBy'] as $required) {
            if (empty($attrs[$required])) {
                return null;
            }
        }

        return $attrs;
    }

    /**
     * Populate $previewContextOptions from the file's @previewContext frontmatter.
     * When options are available, auto-selects the first if none is already chosen.
     */
    private function loadPreviewContextOptions(): void
    {
        $this->previewContextOptions = [];

        $meta = $this->parsePreviewContextFrontmatter();

        if (! $meta) {
            $this->previewContext = '';

            return;
        }

        [$orderColumn, $orderDir] = array_pad(explode(':', $meta['orderBy'], 2), 2, 'asc');

        $query = $meta['model']::query();

        if (! empty($meta['where'])) {
            [$whereCol, $whereVal] = explode(':', $meta['where'], 2);
            $query->where($whereCol, $whereVal);
        }

        $this->previewContextOptions = $query
            ->orderBy($orderColumn, $orderDir)
            ->limit(50)
            ->pluck($meta['label'], $meta['value'])
            ->all();

        if (! $this->previewContext || ! isset($this->previewContextOptions[$this->previewContext])) {
            $this->previewContext = (string) (array_key_first($this->previewContextOptions) ?? '');
        }
    }

    /**
     * Resolve the previewContext string into a keyed array for the mount injector.
     *
     * @return array<string, string>
     */
    private function resolvePreviewContext(): array
    {
        if (! $this->previewContext) {
            return [];
        }

        $meta = $this->parsePreviewContextFrontmatter();

        if (! $meta) {
            return [];
        }

        return [$meta['routeParam'] => $this->previewContext];
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


    public function generateAiContent(string $fieldKey, string $prompt, string $fieldType, string $currentClasses = '', bool $useHtml = true): void
    {
        $provider = \App\Models\Setting::get('ai.text_provider', 'google');
        $isSeo = $fieldType === 'seo';
        $isRichText = $fieldType === 'richtext';
        $isClasses = $fieldType === 'classes';

        if ($isSeo) {
            $systemPrompt = 'You are an SEO expert. Generate an optimized page title and meta description for a web page. Page title: 50–60 characters, compelling, naturally includes the main keyword. Meta description: 150–160 characters, concise summary with a call to action. If page content is limited or uses placeholder text, base your output on the page name and any guidance provided — always make a reasonable attempt. You MUST respond with ONLY valid JSON in this exact format, no matter what: {"title":"...","description":"..."} — no markdown, no code fences, no explanation, no refusals.';

            // Extract readable text from the page's saved content fields
            $rowSlugs = array_column($this->rows, 'slug');
            $contentSnippets = [];

            if (! empty($rowSlugs)) {
                $overrides = \App\Models\ContentOverride::whereIn('row_slug', $rowSlugs)
                    ->get(['key', 'value']);

                foreach ($overrides as $override) {
                    $key = $override->key;
                    $value = trim(strip_tags((string) $override->value));

                    if (
                        empty($value) || mb_strlen($value) < 4 ||
                        str_ends_with($key, '_classes') ||
                        str_ends_with($key, '_image') || $key === 'image' ||
                        str_ends_with($key, '_alt') || $key === 'alt' ||
                        str_ends_with($key, '_url') ||
                        str_ends_with($key, '_new_tab') ||
                        str_ends_with($key, '_id') ||
                        str_ends_with($key, '_attrs') ||
                        str_starts_with($key, 'toggle_') ||
                        str_starts_with($key, 'grid_') ||
                        preg_match('/__[a-z]{2,10}$/', $key)
                    ) {
                        continue;
                    }

                    $contentSnippets[] = $value;
                }
            }

            $contentContext = ! empty($contentSnippets)
                ? "\n\nExisting page content:\n" . implode("\n", array_slice($contentSnippets, 0, 30))
                : '';

            $extraContext = trim($prompt) !== '' ? "\n\nAdditional guidance: {$prompt}" : '';
            $userMessage = "Page name: {$this->pageName}{$contentContext}{$extraContext}";
        } elseif ($isClasses) {
            $systemPrompt = 'You are a Tailwind CSS expert. You will be given a set of Tailwind CSS classes and an instruction to modify them. Return only the updated class string — space-separated Tailwind classes — with no explanation, no quotes, no backticks, and no markdown.';
            $userMessage = "Current classes: {$currentClasses}\n\nInstruction: {$prompt}";
        } else {
            $systemPrompt = $isRichText && $useHtml
                ? 'You are a content writer. Generate clean, concise HTML content based on the user\'s request. Use only simple inline and block tags such as <p>, <strong>, <em>, <a>, <ul>, <ol>, <li>. Do not use heading tags (h1–h6). Do not produce empty tags or unnecessary whitespace between tags. Return only the HTML, no markdown code fences, no explanation.'
                : 'You are a content writer. Generate concise, well-written text based on the user\'s request. Return only the text, no HTML tags, no markdown, no quotes, no explanation.';
            $userMessage = $prompt;
        }

        try {
            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => \App\Models\Setting::get('ai.openai_model'),
                    'max_tokens' => 1024,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'OpenAI API error.');
                }

                $content = $response->json('choices.0.message.content', '');
            } elseif ($provider === 'google') {
                $apiKey = \App\Models\Setting::get('ai.google_key', '');
                $model = \App\Models\Setting::get('ai.google_model', 'gemini-2.0-flash');
                $response = Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => [['parts' => [['text' => $userMessage]]]],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Google AI API error.');
                }

                $content = $response->json('candidates.0.content.parts.0.text', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => \App\Models\Setting::get('ai.claude_model'),
                    'max_tokens' => 1024,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Claude API error.');
                }

                $content = $response->json('content.0.text', '');
            }

            if ($isSeo) {
                // Strip markdown code fences if the AI wrapped the JSON
                $stripped = trim(preg_replace('/^```(?:json)?\s*/i', '', preg_replace('/\s*```$/i', '', trim($content))));
                $data = json_decode($stripped, true);

                if (! is_array($data) || empty($data['title'])) {
                    // AI returned a conversational response instead of JSON — surface it so the user can add context
                    $this->dispatch('seo-ai-complete', title: '', description: '', error: $content);
                } else {
                    $this->seoTitle = $data['title'];
                    $this->seoDescription = $data['description'] ?? '';
                    $this->dispatch('seo-ai-complete',
                        title: $data['title'],
                        description: $data['description'] ?? '',
                        error: '',
                    );
                }
            } else {
                $this->dispatch('ai-content-generated', fieldKey: $fieldKey, content: $content);
            }
        } catch (\Exception $e) {
            $this->dispatch('ai-generate-error', fieldKey: $fieldKey, message: $e->getMessage());
        }
    }

    public function translateField(string $fieldKey, string $sourceLang, string $targetLang): void
    {
        // The source key is the field key without the language suffix.
        $sourceKey = preg_replace('/__[a-z]{2,10}$/', '', $fieldKey);
        $sourceContent = $this->contentValues[$sourceKey] ?? '';
        $sourceField = collect($this->contentFields)->firstWhere('key', $sourceKey);
        $fieldType = $sourceField['type'] ?? 'text';

        if (empty(trim(strip_tags((string) $sourceContent)))) {
            $this->dispatch('ai-generate-error', fieldKey: $fieldKey, message: 'No source content to translate.');

            return;
        }

        $langNames = collect(\App\Models\Setting::get('site.languages', []))->keyBy('code');
        $sourceLangName = $langNames->get($sourceLang, ['label' => strtoupper($sourceLang)])['label'];
        $targetLangName = $langNames->get($targetLang, ['label' => strtoupper($targetLang)])['label'];

        $isRichText = $fieldType === 'richtext';
        $systemPrompt = "You are a professional translator. Translate content from {$sourceLangName} to {$targetLangName}. Return only the translated text with no explanation. " . ($isRichText ? 'Preserve all HTML tags exactly as they appear.' : 'Do not add HTML tags.');
        $userMessage = (string) $sourceContent;

        try {
            $provider = \App\Models\Setting::get('ai.text_provider', 'google');

            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => \App\Models\Setting::get('ai.openai_model'),
                    'max_tokens' => 2048,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'OpenAI API error.');
                }

                $translated = $response->json('choices.0.message.content', '');
            } elseif ($provider === 'google') {
                $apiKey = \App\Models\Setting::get('ai.google_key', '');
                $model = \App\Models\Setting::get('ai.google_model', 'gemini-2.0-flash');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => [['parts' => [['text' => $userMessage]]]],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Google AI API error.');
                }

                $translated = $response->json('candidates.0.content.parts.0.text', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => \App\Models\Setting::get('ai.claude_model'),
                    'max_tokens' => 2048,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Claude API error.');
                }

                $translated = $response->json('content.0.text', '');
            }

            $translated = trim($translated);
            $this->contentValues[$fieldKey] = $translated;

            $targetField = collect($this->contentFields)->firstWhere('key', $fieldKey);

            if ($targetField) {
                session()->put(
                    'editor_draft_overrides.' . $targetField['slug'] . ':' . $fieldKey,
                    ['type' => $fieldType, 'value' => $translated]
                );
            }

            $this->dispatch('ai-content-generated', fieldKey: $fieldKey, content: $translated);
            $this->refreshPreview();
        } catch (\Exception $e) {
            $this->dispatch('ai-generate-error', fieldKey: $fieldKey, message: $e->getMessage());
        }
    }

    public function translateAllSectionFields(int $rowIndex, string $targetLang): void
    {
        $this->openContentEditor($rowIndex);

        $langNames = collect(\App\Models\Setting::get('site.languages', []))->keyBy('code');
        $targetLangName = $langNames->get($targetLang, ['label' => strtoupper($targetLang)])['label'];

        $translatableTypes = ['text', 'richtext'];
        $nonTranslatableEndings = ['_url', '_htag', '_alt', '_new_tab', '_classes', '_id', '_attrs'];

        $toTranslate = array_filter(
            $this->contentFields,
            fn ($f) => in_array($f['type'], $translatableTypes, true)
                && ! str_starts_with($f['key'], 'toggle_')
                && ! collect($nonTranslatableEndings)->contains(fn ($suffix) => str_ends_with($f['key'], $suffix))
                && str_ends_with($f['key'], '__' . $targetLang)
        );

        $translated = 0;

        foreach ($toTranslate as $field) {
            $sourceKey = preg_replace('/__[a-z]{2,10}$/', '', $field['key']);
            $sourceContent = $this->contentValues[$sourceKey] ?? '';

            if (empty(trim(strip_tags((string) $sourceContent)))) {
                continue;
            }

            $isRichText = $field['type'] === 'richtext';
            $systemPrompt = "You are a professional translator. Translate content to {$targetLangName}. Return only the translated text with no explanation. " . ($isRichText ? 'Preserve all HTML tags exactly as they appear.' : 'Do not add HTML tags.');

            try {
                $provider = \App\Models\Setting::get('ai.text_provider', 'google');

                if ($provider === 'openai') {
                    $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ])->post('https://api.openai.com/v1/chat/completions', [
                        'model' => \App\Models\Setting::get('ai.openai_model'),
                        'max_tokens' => 2048,
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => (string) $sourceContent],
                        ],
                    ]);

                    $result = $response->failed() ? '' : $response->json('choices.0.message.content', '');
                } elseif ($provider === 'google') {
                    $apiKey = \App\Models\Setting::get('ai.google_key', '');
                    $model = \App\Models\Setting::get('ai.google_model', 'gemini-2.0-flash');
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'x-goog-api-key' => $apiKey,
                        'Content-Type' => 'application/json',
                    ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                        'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                        'contents' => [['parts' => [['text' => (string) $sourceContent]]]],
                    ]);

                    $result = $response->failed() ? '' : $response->json('candidates.0.content.parts.0.text', '');
                } else {
                    $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'x-api-key' => $apiKey,
                        'anthropic-version' => '2023-06-01',
                        'content-type' => 'application/json',
                    ])->post('https://api.anthropic.com/v1/messages', [
                        'model' => \App\Models\Setting::get('ai.claude_model'),
                        'max_tokens' => 2048,
                        'system' => $systemPrompt,
                        'messages' => [['role' => 'user', 'content' => (string) $sourceContent]],
                    ]);

                    $result = $response->failed() ? '' : $response->json('content.0.text', '');
                }

                $result = trim($result);

                if ($result) {
                    $this->contentValues[$field['key']] = $result;
                    session()->put(
                        'editor_draft_overrides.' . $field['slug'] . ':' . $field['key'],
                        ['type' => $field['type'], 'value' => $result]
                    );
                    $translated++;
                }
            } catch (\Exception $e) {
                // Continue on individual field failures.
            }
        }

        $this->refreshPreview();
        $this->dispatch('notify', message: $translated > 0 ? "Translated {$translated} field(s) to {$targetLangName}." : 'No translatable content found.');
    }

    public function triggerPreviewRefresh(): void
    {
        $this->refreshPreview();
    }

    /** @return list<string> */
    public function prepareTranslation(int $rowIndex, string $targetLang, bool $skipFilled): array
    {
        $this->openContentEditor($rowIndex);

        $translatableTypes = ['text', 'richtext'];
        $nonTranslatableEndings = ['_url', '_htag', '_alt', '_new_tab', '_classes', '_id', '_attrs'];
        $suffix = '__' . $targetLang;

        // Regular text/richtext field keys.
        $fieldKeys = array_values(array_column(array_values(array_filter(
            $this->contentFields,
            function ($f) use ($targetLang, $skipFilled, $nonTranslatableEndings, $translatableTypes, $suffix): bool {
                if (! in_array($f['type'], $translatableTypes, true)) {
                    return false;
                }

                if (str_starts_with($f['key'], 'toggle_')) {
                    return false;
                }

                foreach ($nonTranslatableEndings as $ending) {
                    if (str_ends_with($f['key'], $ending)) {
                        return false;
                    }
                }

                if (! str_ends_with($f['key'], $suffix)) {
                    return false;
                }

                $sourceKey = substr($f['key'], 0, -strlen($suffix));
                $sourceContent = $this->contentValues[$sourceKey] ?? '';

                if (empty(trim(strip_tags((string) $sourceContent)))) {
                    // Fall back to the field default when no value has been saved yet.
                    $sourceField = collect($this->contentFields)->firstWhere('key', $sourceKey);
                    $sourceContent = $sourceField ? ($sourceField['default'] ?? '') : '';
                }

                if (empty(trim(strip_tags((string) $sourceContent)))) {
                    return false;
                }

                if ($skipFilled && ! empty(trim(strip_tags((string) ($this->contentValues[$f['key']] ?? ''))))) {
                    return false;
                }

                return true;
            }
        )), 'key'));

        // Grid item sub-field translation paths.
        $gridPaths = [];
        $nonTranslatableGridKey = function (string $k): bool {
            if (in_array($k, ['icon', 'image', 'alt', 'url'], true)) {
                return true;
            }
            if (str_starts_with($k, 'toggle_')) {
                return true;
            }
            if (str_ends_with($k, '_image') || str_ends_with($k, '_alt') || str_ends_with($k, '_url')) {
                return true;
            }
            return false;
        };

        foreach ($this->contentFields as $gf) {
            if ($gf['type'] !== 'grid') {
                continue;
            }
            $items = json_decode($this->contentValues[$gf['key']] ?? '[]', true) ?: [];
            foreach ($items as $idx => $item) {
                foreach (array_keys($item) as $subKey) {
                    if (str_contains($subKey, '__')) {
                        continue; // skip existing lang variant keys
                    }
                    if ($nonTranslatableGridKey($subKey)) {
                        continue;
                    }
                    $sourceContent = (string) ($item[$subKey] ?? '');
                    if (empty(trim(strip_tags($sourceContent)))) {
                        continue;
                    }
                    if ($skipFilled && ! empty(trim(strip_tags((string) ($item[$subKey . '__' . $targetLang] ?? ''))))) {
                        continue;
                    }
                    $gridPaths[] = '@grid:' . $gf['key'] . ':' . $idx . ':' . $subKey;
                }
            }
        }

        return array_merge($fieldKeys, $gridPaths);
    }

    public function translateNextField(string $fieldKey, string $targetLang): void
    {
        // Handle grid item sub-field translation paths.
        if (str_starts_with($fieldKey, '@grid:')) {
            $this->translateGridItemField($fieldKey, $targetLang);

            return;
        }

        $field = collect($this->contentFields)->firstWhere('key', $fieldKey);

        if (! $field) {
            return;
        }

        $suffix = '__' . $targetLang;
        $sourceKey = substr($fieldKey, 0, -strlen($suffix));
        $sourceContent = $this->contentValues[$sourceKey] ?? '';

        if (empty(trim(strip_tags((string) $sourceContent)))) {
            // Fall back to the field default when no value has been saved yet.
            $sourceField = collect($this->contentFields)->firstWhere('key', $sourceKey);
            $sourceContent = $sourceField ? ($sourceField['default'] ?? '') : '';
        }

        if (empty(trim(strip_tags((string) $sourceContent)))) {
            return;
        }

        $langNames = collect(\App\Models\Setting::get('site.languages', []))->keyBy('code');
        $targetLangName = $langNames->get($targetLang, ['label' => strtoupper($targetLang)])['label'];

        $isRichText = $field['type'] === 'richtext';
        $systemPrompt = 'You are a professional translator. Translate content to ' . $targetLangName . '. Return only the translated text with no explanation. ' . ($isRichText ? 'Preserve all HTML tags exactly as they appear.' : 'Do not add HTML tags.');

        try {
            $provider = \App\Models\Setting::get('ai.text_provider', 'google');

            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => \App\Models\Setting::get('ai.openai_model'),
                    'max_tokens' => 2048,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => (string) $sourceContent],
                    ],
                ]);
                $result = $response->failed() ? '' : $response->json('choices.0.message.content', '');
            } elseif ($provider === 'google') {
                $apiKey = \App\Models\Setting::get('ai.google_key', '');
                $model = \App\Models\Setting::get('ai.google_model', 'gemini-2.0-flash');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => [['parts' => [['text' => (string) $sourceContent]]]],
                ]);
                $result = $response->failed() ? '' : $response->json('candidates.0.content.parts.0.text', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => \App\Models\Setting::get('ai.claude_model'),
                    'max_tokens' => 2048,
                    'system' => $systemPrompt,
                    'messages' => [['role' => 'user', 'content' => (string) $sourceContent]],
                ]);
                $result = $response->failed() ? '' : $response->json('content.0.text', '');
            }

            $result = trim($result);

            if ($result) {
                $this->contentValues[$fieldKey] = $result;
                session()->put(
                    'editor_draft_overrides.' . $field['slug'] . ':' . $fieldKey,
                    ['type' => $field['type'], 'value' => $result]
                );
            }
        } catch (\Exception $e) {
            // Continue on individual field failures.
        }
    }

    private function translateGridItemField(string $path, string $targetLang): void
    {
        // Format: @grid:{gridFieldKey}:{itemIndex}:{subKey}
        $parts = explode(':', $path, 4);
        if (count($parts) !== 4) {
            return;
        }

        [, $gridFieldKey, $rawIndex, $subKey] = $parts;
        $itemIndex = (int) $rawIndex;

        $rawJson = $this->contentValues[$gridFieldKey] ?? '[]';
        $items = json_decode($rawJson ?: '[]', true) ?: [];

        if (! isset($items[$itemIndex][$subKey])) {
            return;
        }

        $sourceContent = (string) $items[$itemIndex][$subKey];
        if (empty(trim(strip_tags($sourceContent)))) {
            return;
        }

        $langNames = collect(\App\Models\Setting::get('site.languages', []))->keyBy('code');
        $targetLangName = $langNames->get($targetLang, ['label' => strtoupper($targetLang)])['label'];
        $systemPrompt = 'You are a professional translator. Translate content to ' . $targetLangName . '. Return only the translated text with no explanation. Do not add HTML tags.';

        try {
            $provider = \App\Models\Setting::get('ai.text_provider', 'google');

            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => \App\Models\Setting::get('ai.openai_model'),
                    'max_tokens' => 2048,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $sourceContent],
                    ],
                ]);
                $result = $response->failed() ? '' : $response->json('choices.0.message.content', '');
            } elseif ($provider === 'google') {
                $apiKey = \App\Models\Setting::get('ai.google_key', '');
                $model = \App\Models\Setting::get('ai.google_model', 'gemini-2.0-flash');
                $response = Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => [['parts' => [['text' => $sourceContent]]]],
                ]);
                $result = $response->failed() ? '' : $response->json('candidates.0.content.parts.0.text', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => \App\Models\Setting::get('ai.claude_model'),
                    'max_tokens' => 2048,
                    'system' => $systemPrompt,
                    'messages' => [['role' => 'user', 'content' => $sourceContent]],
                ]);
                $result = $response->failed() ? '' : $response->json('content.0.text', '');
            }

            $result = trim($result);

            if ($result) {
                $items[$itemIndex][$subKey . '__' . $targetLang] = $result;
                $newJson = json_encode(array_values($items));
                $this->contentValues[$gridFieldKey] = $newJson;

                $gridField = collect($this->contentFields)->firstWhere('key', $gridFieldKey);
                if ($gridField) {
                    session()->put(
                        'editor_draft_overrides.' . $gridField['slug'] . ':' . $gridFieldKey,
                        ['type' => 'grid', 'value' => $newJson]
                    );
                }
                $this->dispatch('content-grid-reset', key: $gridFieldKey, value: $newJson);
            }
        } catch (\Exception) {
            // Continue on individual field failures.
        }
    }

    public function rewriteAiContent(string $fieldKey, string $fieldType, string $tone): void
    {
        $currentContent = $this->contentValues[$fieldKey] ?? '';

        if (empty(trim(strip_tags((string) $currentContent)))) {
            $this->dispatch('ai-generate-error', fieldKey: $fieldKey, message: 'No content to rewrite. Add some text first.');

            return;
        }

        $provider = \App\Models\Setting::get('ai.text_provider', 'google');
        $isRichText = $fieldType === 'richtext';

        $toneInstruction = match ($tone) {
            'proof' => 'Proofread and lightly edit the following text to fix grammar, spelling, punctuation, and clarity issues. Preserve the original meaning, voice, and length as closely as possible.',
            'professional' => 'Rewrite the following text in a professional, polished tone suitable for a business audience. Keep the core message.',
            'casual' => 'Rewrite the following text in a casual, conversational tone. Make it feel approachable and natural.',
            'playful' => 'Rewrite the following text in a playful, fun, and energetic tone. Add personality while keeping the core message.',
            default => 'Rewrite the following text.',
        };

        $systemPrompt = $isRichText
            ? "You are a content editor. {$toneInstruction} The content is HTML — preserve the HTML structure and tags, only modify the text nodes. Return only the HTML, no markdown code fences, no explanation."
            : "You are a content editor. {$toneInstruction} Return only the rewritten text, no quotes, no explanation.";

        try {
            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => \App\Models\Setting::get('ai.openai_model'),
                    'max_tokens' => 1024,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $currentContent],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'OpenAI API error.');
                }

                $content = $response->json('choices.0.message.content', '');
            } elseif ($provider === 'google') {
                $apiKey = \App\Models\Setting::get('ai.google_key', '');
                $model = \App\Models\Setting::get('ai.google_model', 'gemini-2.0-flash');
                $response = Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => [['parts' => [['text' => $currentContent]]]],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Google AI API error.');
                }

                $content = $response->json('candidates.0.content.parts.0.text', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => \App\Models\Setting::get('ai.claude_model'),
                    'max_tokens' => 1024,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $currentContent],
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

    public function generateAiGridItemAltText(string $gridKey, int $idx, string $altKey, string $imagePath): void
    {
        if (empty($imagePath)) {
            $this->dispatch('ai-grid-alt-error', gridKey: $gridKey, idx: $idx, altKey: $altKey, message: 'No image found.');

            return;
        }

        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
            $this->dispatch('ai-grid-alt-error', gridKey: $gridKey, idx: $idx, altKey: $altKey, message: 'Image file not found in storage.');

            return;
        }

        $imageContents = \Illuminate\Support\Facades\Storage::disk('public')->get($imagePath);
        $mimeType = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($imagePath) ?: 'image/jpeg';
        $base64 = base64_encode($imageContents);

        $provider = \App\Models\Setting::get('ai.text_provider', 'google');
        $prompt = 'Generate a concise, descriptive alt text for this image suitable for screen readers. Maximum 10 words. Return only the alt text, no quotes, no trailing punctuation, no explanation.';

        try {
            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => \App\Models\Setting::get('ai.openai_model'),
                    'max_tokens' => 100,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'image_url',
                                    'image_url' => ['url' => 'data:' . $mimeType . ';base64,' . $base64],
                                ],
                                ['type' => 'text', 'text' => $prompt],
                            ],
                        ],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'OpenAI API error.');
                }

                $content = $response->json('choices.0.message.content', '');
            } elseif ($provider === 'google') {
                $apiKey = \App\Models\Setting::get('ai.google_key', '');
                $model = \App\Models\Setting::get('ai.google_model', 'gemini-2.0-flash');
                $response = Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'contents' => [['parts' => [
                        ['inline_data' => ['mime_type' => $mimeType, 'data' => $base64]],
                        ['text' => $prompt],
                    ]]],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Google AI API error.');
                }

                $content = $response->json('candidates.0.content.parts.0.text', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => \App\Models\Setting::get('ai.claude_model'),
                    'max_tokens' => 100,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'image',
                                    'source' => [
                                        'type' => 'base64',
                                        'media_type' => $mimeType,
                                        'data' => $base64,
                                    ],
                                ],
                                ['type' => 'text', 'text' => $prompt],
                            ],
                        ],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Claude API error.');
                }

                $content = $response->json('content.0.text', '');
            }

            $this->dispatch('ai-grid-item-alt-generated', gridKey: $gridKey, idx: $idx, altKey: $altKey, content: ucfirst(trim($content)));
        } catch (\Exception $e) {
            $this->dispatch('ai-grid-alt-error', gridKey: $gridKey, idx: $idx, altKey: $altKey, message: $e->getMessage());
        }
    }

    public function generateAiAltText(string $altFieldKey, string $imageFieldKey): void
    {
        $imagePath = $this->contentValues[$imageFieldKey] ?? '';

        if (empty($imagePath)) {
            $this->dispatch('ai-generate-error', fieldKey: $altFieldKey, message: 'No image found. Please upload an image first.');

            return;
        }

        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
            $this->dispatch('ai-generate-error', fieldKey: $altFieldKey, message: 'Image file not found in storage.');

            return;
        }

        $imageContents = \Illuminate\Support\Facades\Storage::disk('public')->get($imagePath);
        $mimeType = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($imagePath) ?: 'image/jpeg';
        $base64 = base64_encode($imageContents);

        $provider = \App\Models\Setting::get('ai.text_provider', 'google');
        $prompt = 'Generate a concise, descriptive alt text for this image suitable for screen readers. Maximum 10 words. Return only the alt text, no quotes, no trailing punctuation, no explanation.';

        try {
            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => \App\Models\Setting::get('ai.openai_model'),
                    'max_tokens' => 100,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'image_url',
                                    'image_url' => ['url' => 'data:' . $mimeType . ';base64,' . $base64],
                                ],
                                ['type' => 'text', 'text' => $prompt],
                            ],
                        ],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'OpenAI API error.');
                }

                $content = $response->json('choices.0.message.content', '');
            } elseif ($provider === 'google') {
                $apiKey = \App\Models\Setting::get('ai.google_key', '');
                $model = \App\Models\Setting::get('ai.google_model', 'gemini-2.0-flash');
                $response = Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'contents' => [['parts' => [
                        ['inline_data' => ['mime_type' => $mimeType, 'data' => $base64]],
                        ['text' => $prompt],
                    ]]],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Google AI API error.');
                }

                $content = $response->json('candidates.0.content.parts.0.text', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => \App\Models\Setting::get('ai.claude_model'),
                    'max_tokens' => 100,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'image',
                                    'source' => [
                                        'type' => 'base64',
                                        'media_type' => $mimeType,
                                        'data' => $base64,
                                    ],
                                ],
                                ['type' => 'text', 'text' => $prompt],
                            ],
                        ],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Claude API error.');
                }

                $content = $response->json('content.0.text', '');
            }

            $this->dispatch('ai-content-generated', fieldKey: $altFieldKey, content: ucfirst(trim($content)));
        } catch (\Exception $e) {
            $this->dispatch('ai-generate-error', fieldKey: $altFieldKey, message: $e->getMessage());
        }
    }

    public function loadAiImageContext(string $fieldKey, string $rowSlug, int $gridItemIdx = -1): void
    {
        $snippets = $this->buildAiImageContextSnippets($rowSlug, $fieldKey, $gridItemIdx);
        $this->dispatch('ai-image-context', fieldKey: $fieldKey, snippets: $snippets);
    }

    public function generateAiImage(string $fieldKey, string $prompt, string $rowSlug = '', bool $includeContext = true, int $gridItemIdx = -1): void
    {
        $provider = \App\Models\Setting::get('ai.image_provider', 'google');

        $fullPrompt = $prompt;

        if ($includeContext && $rowSlug !== '') {
            $snippets = $this->buildAiImageContextSnippets($rowSlug, $fieldKey, $gridItemIdx);

            if (! empty($snippets)) {
                $contextString = implode(' | ', $snippets);
                $fullPrompt = $prompt !== ''
                    ? $prompt . "\n\nContext from surrounding content: " . $contextString
                    : 'Generate an image appropriate for this content: ' . $contextString;
            } elseif ($prompt === '') {
                $fullPrompt = 'Generate an appropriate image.';
            }
        }

        try {
            $imageContents = match ($provider) {
                'fal' => $this->generateImageViaFal($fullPrompt),
                'stability' => $this->generateImageViaStability($fullPrompt),
                'google' => $this->generateImageViaGoogle($fullPrompt),
                default => $this->generateImageViaOpenAi($fullPrompt),
            };
        } catch (\Exception $e) {
            $this->dispatch('ai-generate-error', fieldKey: $fieldKey, message: $e->getMessage());

            return;
        }

        $tempPath = 'tmp/ai-' . now()->format('YmdHis') . '-' . substr(md5($prompt), 0, 6) . '.jpg';
        \Illuminate\Support\Facades\Storage::disk('public')->put($tempPath, $imageContents);

        $this->dispatch('ai-image-preview',
            fieldKey: $fieldKey,
            tempPath: $tempPath,
            url: \Illuminate\Support\Facades\Storage::url($tempPath),
            fullPrompt: $fullPrompt,
        );
    }

    public function saveAiImagePreview(string $fieldKey, string $tempPath, string $prompt): void
    {
        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($tempPath)) {
            return;
        }

        $isRowBg = str_starts_with($fieldKey, 'row-design-bg:');

        if ($isRowBg) {
            $category = \App\Models\MediaCategory::where('slug', 'backgrounds')->first()
                ?? \App\Models\MediaCategory::where('is_default', true)->first()
                ?? \App\Models\MediaCategory::first();
        } else {
            $category = \App\Models\MediaCategory::where('is_default', true)->first()
                ?? \App\Models\MediaCategory::first();
        }

        $categorySlug = $category?->slug ?? 'uncategorized';
        $filename = basename($tempPath);
        $storagePath = $categorySlug . '/' . $filename;

        \Illuminate\Support\Facades\Storage::disk('public')->move($tempPath, $storagePath);

        \App\Models\MediaItem::create([
            'media_category_id' => $category?->id,
            'path' => $storagePath,
            'filename' => $filename,
            'alt' => $prompt,
            'size' => \Illuminate\Support\Facades\Storage::disk('public')->size($storagePath),
            'mime_type' => 'image/jpeg',
        ]);

        if ($isRowBg) {
            $slug = substr($fieldKey, 14);
            $this->rowDesignValues[$slug]['section_bg_image'] = $storagePath;

            $drafts = session('editor_draft_overrides', []);
            $drafts[$slug.':section_bg_image'] = ['type' => 'image', 'value' => $storagePath];
            session(['editor_draft_overrides' => $drafts]);
        } else {
            $this->contentValues[$fieldKey] = $storagePath;

            $field = collect($this->contentFields)->firstWhere('key', $fieldKey);
            if ($field) {
                session()->put('editor_draft_overrides.'.$field['slug'].':'.$fieldKey, ['type' => 'image', 'value' => $storagePath]);
            }
        }

        $this->isDirty = true;
        $this->dispatch('ai-image-generated', fieldKey: $fieldKey, path: $storagePath);
        $this->refreshPreview();
    }

    public function saveAiGridItemImagePreview(string $gridFieldKey, string $tempPath, string $prompt, int $idx, string $itemKey): void
    {
        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($tempPath)) {
            return;
        }

        $category = \App\Models\MediaCategory::where('is_default', true)->first()
            ?? \App\Models\MediaCategory::first();

        $categorySlug = $category?->slug ?? 'uncategorized';
        $filename = basename($tempPath);
        $storagePath = $categorySlug . '/' . $filename;

        \Illuminate\Support\Facades\Storage::disk('public')->move($tempPath, $storagePath);

        \App\Models\MediaItem::create([
            'media_category_id' => $category?->id,
            'path' => $storagePath,
            'filename' => $filename,
            'alt' => $prompt,
            'size' => \Illuminate\Support\Facades\Storage::disk('public')->size($storagePath),
            'mime_type' => 'image/jpeg',
        ]);

        $this->isDirty = true;
        $this->dispatch('ai-grid-item-image-generated', gridKey: $gridFieldKey, idx: $idx, itemKey: $itemKey, path: $storagePath);
        $this->dispatch('ai-image-generated', fieldKey: $gridFieldKey, path: $storagePath);
        $this->refreshPreview();
    }

    public function discardAiImagePreview(string $tempPath): void
    {
        if ($tempPath) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($tempPath);
        }
    }

    /** @return string[] */
    private function buildAiImageContextSnippets(string $rowSlug, string $fieldKey, int $gridItemIdx): array
    {
        $overrides = \App\Models\ContentOverride::where('row_slug', $rowSlug)->get(['key', 'value']);
        $snippets = [];

        foreach ($overrides as $override) {
            $key = $override->key;
            $value = trim(strip_tags((string) $override->value));

            if (str_starts_with($key, 'grid_') && $gridItemIdx >= 0) {
                $items = json_decode($value, true) ?? [];
                $item = $items[$gridItemIdx] ?? null;

                if ($item) {
                    foreach ($item as $itemKey => $itemValue) {
                        $itemValue = trim(strip_tags((string) $itemValue));

                        if (
                            empty($itemValue) || mb_strlen($itemValue) < 4 ||
                            str_ends_with($itemKey, '_classes') ||
                            str_ends_with($itemKey, '_image') || $itemKey === 'image' ||
                            str_ends_with($itemKey, '_url') ||
                            str_ends_with($itemKey, '_new_tab') ||
                            str_ends_with($itemKey, '_alt') || $itemKey === 'alt' ||
                            str_ends_with($itemKey, '_id') ||
                            str_starts_with($itemKey, 'toggle_') ||
                            $itemKey === 'icon' ||
                            preg_match('/__[a-z]{2,10}$/', $itemKey)
                        ) {
                            continue;
                        }

                        $snippets[] = $itemValue;
                    }
                }

                continue;
            }

            if (
                empty($value) || mb_strlen($value) < 4 ||
                str_ends_with($key, '_classes') ||
                str_ends_with($key, '_image') || $key === 'image' ||
                str_ends_with($key, '_alt') || $key === 'alt' ||
                str_ends_with($key, '_url') ||
                str_ends_with($key, '_new_tab') ||
                str_ends_with($key, '_id') ||
                str_ends_with($key, '_attrs') ||
                str_starts_with($key, 'toggle_') ||
                str_starts_with($key, 'grid_') ||
                preg_match('/__[a-z]{2,10}$/', $key)
            ) {
                continue;
            }

            $snippets[] = $value;
        }

        return $snippets;
    }

    protected function generateImageViaOpenAi(string $prompt): string
    {
        $apiKey = \App\Models\Setting::get('ai.openai_key', '');

        if (empty($apiKey)) {
            throw new \Exception('No OpenAI API key configured.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.openai.com/v1/images/generations', [
            'model' => 'dall-e-3',
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1792x1024',
            'response_format' => 'url',
        ]);

        if ($response->failed()) {
            throw new \Exception($response->json('error.message') ?? 'OpenAI image generation failed.');
        }

        $imageUrl = $response->json('data.0.url');

        if (empty($imageUrl)) {
            throw new \Exception('No image URL returned from OpenAI.');
        }

        $imageResponse = Http::timeout(30)->get($imageUrl);

        if ($imageResponse->failed()) {
            throw new \Exception('Failed to download generated image from OpenAI.');
        }

        return $imageResponse->body();
    }

    protected function generateImageViaFal(string $prompt): string
    {
        $apiKey = \App\Models\Setting::get('ai.fal_key', '');

        if (empty($apiKey)) {
            throw new \Exception('No fal.ai API key configured.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Key ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://fal.run/fal-ai/flux/schnell', [
            'prompt' => $prompt,
            'image_size' => 'landscape_16_9',
            'num_inference_steps' => 4,
            'num_images' => 1,
        ]);

        if ($response->failed()) {
            throw new \Exception($response->json('detail') ?? $response->json('error') ?? 'fal.ai image generation failed.');
        }

        $imageUrl = $response->json('images.0.url');

        if (empty($imageUrl)) {
            throw new \Exception('No image URL returned from fal.ai.');
        }

        $imageResponse = Http::timeout(30)->get($imageUrl);

        if ($imageResponse->failed()) {
            throw new \Exception('Failed to download generated image from fal.ai.');
        }

        return $imageResponse->body();
    }

    protected function generateImageViaStability(string $prompt): string
    {
        $apiKey = \App\Models\Setting::get('ai.stability_key', '');

        if (empty($apiKey)) {
            throw new \Exception('No Stability AI API key configured.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ])->timeout(60)->asMultipart()->post('https://api.stability.ai/v2beta/stable-image/generate/core', [
            ['name' => 'prompt', 'contents' => $prompt],
            ['name' => 'output_format', 'contents' => 'jpeg'],
            ['name' => 'aspect_ratio', 'contents' => '16:9'],
        ]);

        if ($response->failed()) {
            throw new \Exception($response->json('errors.0') ?? $response->json('message') ?? 'Stability AI image generation failed.');
        }

        $base64 = $response->json('image');

        if (empty($base64)) {
            throw new \Exception('No image data returned from Stability AI.');
        }

        return base64_decode($base64);
    }

    protected function generateImageViaGoogle(string $prompt): string
    {
        $apiKey = \App\Models\Setting::get('ai.google_key', '');

        if (empty($apiKey)) {
            throw new \Exception('No Google AI API key configured.');
        }

        $response = Http::withHeaders([
            'x-goog-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://generativelanguage.googleapis.com/v1beta/models/imagen-4.0-generate-001:predict', [
            'instances' => [['prompt' => $prompt]],
            'parameters' => ['numberOfImages' => 1, 'aspectRatio' => '16:9'],
        ]);

        if ($response->failed()) {
            throw new \Exception($response->json('error.message') ?? 'Google Imagen image generation failed.');
        }

        $base64 = $response->json('predictions.0.bytesBase64Encoded');

        if (empty($base64)) {
            throw new \Exception('No image data returned from Google Imagen.');
        }

        return base64_decode($base64);
    }

    public function generateAiAllRowText(int $rowIndex, string $prompt, bool $overwriteAll, bool $useHtml): array
    {
        if (! isset($this->rows[$rowIndex])) {
            return ['updated' => 0, 'rowSlug' => '', 'imageFields' => [], 'error' => null];
        }

        $row = $this->rows[$rowIndex];
        $rowSlug = $row['slug'];

        $skipKeys = [
            'section_classes', 'section_container_classes', 'section_id', 'section_attrs',
            'section_animation', 'section_animation_delay', 'section_bg_image',
            'section_bg_position', 'section_bg_size', 'section_bg_repeat', 'section_style',
        ];

        $isStructuralGridSubKey = function (string $k): bool {
            if (in_array($k, ['icon', 'image', 'alt', 'url'], true)) {
                return true;
            }
            if (str_starts_with($k, 'toggle_')) {
                return true;
            }
            if (
                str_ends_with($k, '_image') ||
                str_ends_with($k, '_alt') ||
                str_ends_with($k, '_url') ||
                str_ends_with($k, '_new_tab') ||
                str_ends_with($k, '_classes') ||
                str_ends_with($k, '_htag') ||
                str_ends_with($k, '_id') ||
                str_ends_with($k, '_attrs')
            ) {
                return true;
            }

            return false;
        };

        $allFields = $this->parseContentFields($row['blade'], $rowSlug);
        $drafts = session('editor_draft_overrides', []);

        $imageFields = [];
        foreach ($allFields as $field) {
            if ($field['type'] !== 'image') {
                continue;
            }
            if (in_array($field['key'], $skipKeys, true)) {
                continue;
            }
            $draftKey = $field['slug'].':'.$field['key'];
            $draft = $drafts[$draftKey] ?? null;
            $currentValue = $draft !== null
                ? ($draft['value'] ?? '')
                : (ContentOverride::query()->where('row_slug', $rowSlug)->where('key', $field['key'])->value('value') ?? '');
            if (! $overwriteAll && ! empty($currentValue)) {
                continue;
            }
            $imageFields[] = $field['key'];
        }

        // Also collect image sub-fields within grid items.
        foreach ($allFields as $field) {
            if ($field['type'] !== 'grid' || ! str_starts_with($field['key'], 'grid_')) {
                continue;
            }
            $draftKey = $field['slug'].':'.$field['key'];
            $draft = $drafts[$draftKey] ?? null;
            $savedJson = $draft !== null
                ? ($draft['value'] ?? '')
                : (ContentOverride::query()->where('row_slug', $rowSlug)->where('key', $field['key'])->value('value') ?? '');
            $gridItems = json_decode((string) ($savedJson ?: ($field['default'] ?? '')), true) ?: [];
            foreach ($gridItems as $idx => $item) {
                foreach (array_keys($item) as $subKey) {
                    if ($subKey !== 'image' && ! str_ends_with($subKey, '_image')) {
                        continue;
                    }
                    if (! $overwriteAll && ! empty($item[$subKey])) {
                        continue;
                    }
                    $imageFields[] = '@grid:'.$field['key'].':'.$idx.':'.$subKey;
                }
            }
        }

        $gridFields = [];
        foreach ($allFields as $field) {
            if ($field['type'] !== 'grid' || ! str_starts_with($field['key'], 'grid_')) {
                continue;
            }
            $draftKey = $field['slug'].':'.$field['key'];
            $draft = $drafts[$draftKey] ?? null;
            $savedJson = $draft !== null
                ? ($draft['value'] ?? '')
                : (ContentOverride::query()->where('row_slug', $rowSlug)->where('key', $field['key'])->value('value') ?? '');
            $hasSavedItems = ! empty($savedJson);
            $currentJson = $hasSavedItems ? $savedJson : ($field['default'] ?? '');
            $items = json_decode((string) $currentJson, true);
            if (! is_array($items) || empty($items)) {
                $items = json_decode((string) ($field['default'] ?? ''), true) ?: [];
            }
            if (empty($items)) {
                continue;
            }
            if (! $overwriteAll && $hasSavedItems) {
                $hasContent = false;
                foreach ($items as $item) {
                    foreach ($item as $subKey => $subVal) {
                        if (! $isStructuralGridSubKey($subKey) && ! empty(trim(strip_tags((string) $subVal)))) {
                            $hasContent = true;
                            break 2;
                        }
                    }
                }
                if ($hasContent) {
                    continue;
                }
            }
            $gridFields[] = [
                'slug' => $field['slug'],
                'key' => $field['key'],
                'label' => $field['label'],
                'items' => $items,
            ];
        }

        $textFields = array_values(array_filter($allFields, function (array $field) use ($skipKeys, $overwriteAll, $rowSlug, $drafts): bool {
            if (in_array($field['key'], $skipKeys, true)) {
                return false;
            }
            if (! in_array($field['type'], ['text', 'richtext'], true)) {
                return false;
            }
            if (
                str_ends_with($field['key'], '_classes') ||
                str_ends_with($field['key'], '_url') ||
                str_ends_with($field['key'], '_alt') ||
                str_ends_with($field['key'], '_new_tab') ||
                str_ends_with($field['key'], '_id') ||
                str_ends_with($field['key'], '_attrs') ||
                str_ends_with($field['key'], '_htag') ||
                str_starts_with($field['key'], 'toggle_')
            ) {
                return false;
            }
            if (! $overwriteAll) {
                $draftKey = $field['slug'].':'.$field['key'];
                $draft = $drafts[$draftKey] ?? null;
                // Only check explicitly saved values — don't fall back to $field['default'],
                // as default placeholder text should be treated as empty for "fill empty only" mode.
                $savedValue = $draft !== null
                    ? ($draft['value'] ?? '')
                    : (ContentOverride::query()->where('row_slug', $rowSlug)->where('key', $field['key'])->value('value') ?? '');
                if (! empty(trim(strip_tags((string) $savedValue)))) {
                    return false;
                }
            }

            return true;
        }));

        if (empty($textFields) && empty($gridFields)) {
            return ['updated' => 0, 'rowSlug' => $rowSlug, 'imageFields' => $imageFields, 'error' => null];
        }

        $provider = \App\Models\Setting::get('ai.text_provider', 'google');

        $overrides = ContentOverride::query()
            ->where('row_slug', $rowSlug)
            ->whereIn('key', array_column($textFields, 'key'))
            ->get()
            ->keyBy('key');

        $fieldSchema = [];
        foreach ($textFields as $field) {
            $draftKey = $field['slug'].':'.$field['key'];
            $draft = $drafts[$draftKey] ?? null;
            $currentValue = $draft !== null
                ? ($draft['value'] ?? '')
                : ($overrides->get($field['key'])?->value ?? '');
            $fieldSchema[] = [
                'key' => $field['key'],
                'label' => $field['label'],
                'type' => $field['type'],
                'current' => strip_tags((string) $currentValue),
            ];
        }

        $htmlInstruction = $useHtml
            ? 'For richtext fields, use simple HTML tags only: <p>, <strong>, <em>, <ul>, <ol>, <li>, <br>. For text fields, return plain text only.'
            : 'Return plain text for all fields — no HTML tags.';

        $systemPrompt = 'You are a web content writer. You will be given a list of text fields for a website section and a description of what the section should contain. Generate appropriate content for each field. Respond ONLY with valid JSON where each key is a field key and each value is the content string or array. '.$htmlInstruction.' For grid fields (type="grid"), return a JSON array of the same length as the items provided — update only the text sub-fields in each item and preserve all structural sub-fields (icon, image, alt, url, toggle_*, *_image, *_url, *_alt, *_new_tab, *_classes) exactly as given. Keep content concise and professional. No explanations, no markdown code fences, no extra keys.';

        $aiFieldsList = array_map(fn (array $f): array => ['key' => $f['key'], 'label' => $f['label'], 'type' => $f['type']], $fieldSchema);
        foreach ($gridFields as $gf) {
            $aiFieldsList[] = ['key' => $gf['key'], 'label' => $gf['label'], 'type' => 'grid', 'items' => $gf['items']];
        }
        $fieldsJson = json_encode($aiFieldsList);
        $userMessage = "Section name: {$row['name']}\nSection fields (JSON): {$fieldsJson}\n\nInstruction: {$prompt}";

        $currentContent = collect($fieldSchema)
            ->filter(fn (array $f): bool => ! empty($f['current']))
            ->map(fn (array $f): string => $f['label'].': '.$f['current'])
            ->implode("\n");

        if ($currentContent) {
            $userMessage .= "\n\nExisting content for reference:\n{$currentContent}";
        }

        try {
            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => \App\Models\Setting::get('ai.openai_model'),
                    'max_tokens' => 4096,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'OpenAI API error.');
                }

                $content = $response->json('choices.0.message.content', '');
            } elseif ($provider === 'google') {
                $apiKey = \App\Models\Setting::get('ai.google_key', '');
                $model = \App\Models\Setting::get('ai.google_model', 'gemini-2.0-flash');
                $response = Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => [['parts' => [['text' => $userMessage]]]],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Google AI API error.');
                }

                $content = $response->json('candidates.0.content.parts.0.text', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => \App\Models\Setting::get('ai.claude_model'),
                    'max_tokens' => 4096,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Claude API error.');
                }

                $content = $response->json('content.0.text', '');
            }

            $stripped = trim(preg_replace('/^```(?:json)?\s*/i', '', preg_replace('/\s*```$/i', '', trim($content))));
            $data = json_decode($stripped, true);

            if (! is_array($data)) {
                throw new \Exception('AI returned an unexpected response for section '.$row['name'].'.');
            }

            $updated = 0;

            foreach ($textFields as $field) {
                $key = $field['key'];
                if (! array_key_exists($key, $data)) {
                    continue;
                }
                $value = (string) $data[$key];
                $draftKey = $field['slug'].':'.$key;
                $drafts[$draftKey] = ['type' => $field['type'], 'value' => $value];
                $updated++;
                if (
                    $this->editingRowIndex !== null &&
                    isset($this->rows[$this->editingRowIndex]) &&
                    $this->rows[$this->editingRowIndex]['slug'] === $rowSlug
                ) {
                    $this->contentValues[$key] = $value;
                }
            }

            foreach ($gridFields as $gf) {
                $key = $gf['key'];
                if (! array_key_exists($key, $data) || ! is_array($data[$key])) {
                    continue;
                }
                $mergedItems = [];
                foreach ($gf['items'] as $idx => $existingItem) {
                    $generated = $data[$key][$idx] ?? [];
                    $merged = $existingItem;
                    foreach ($generated as $subKey => $subVal) {
                        if (! $isStructuralGridSubKey($subKey)) {
                            $merged[$subKey] = (string) $subVal;
                        }
                    }
                    $mergedItems[] = $merged;
                }
                $gridJson = json_encode($mergedItems);
                $draftKey = $gf['slug'].':'.$key;
                $drafts[$draftKey] = ['type' => 'grid', 'value' => $gridJson];
                $updated++;
                if (
                    $this->editingRowIndex !== null &&
                    isset($this->rows[$this->editingRowIndex]) &&
                    $this->rows[$this->editingRowIndex]['slug'] === $rowSlug
                ) {
                    $this->contentValues[$key] = $gridJson;
                }
            }

            session(['editor_draft_overrides' => $drafts]);
            $this->isDirty = true;
            $this->dispatch('ai-section-content-generated', rowSlug: $rowSlug, values: array_column($textFields, 'key'));

            return ['updated' => $updated, 'rowSlug' => $rowSlug, 'imageFields' => $imageFields, 'error' => null];
        } catch (\Exception $e) {
            return ['updated' => 0, 'rowSlug' => $rowSlug, 'imageFields' => $imageFields, 'error' => $e->getMessage()];
        }
    }

    public function generateAiAllRowImage(string $rowSlug, string $fieldKey, string $imagePrompt): array
    {
        $apiKey = \App\Models\Setting::get('ai.openai_key', '');

        if (empty($apiKey)) {
            return ['path' => null, 'error' => 'No OpenAI API key configured.'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/images/generations', [
                'model' => 'dall-e-3',
                'prompt' => $imagePrompt,
                'n' => 1,
                'size' => '1792x1024',
                'response_format' => 'url',
            ]);

            if ($response->failed()) {
                throw new \Exception($response->json('error.message') ?? 'OpenAI image generation failed.');
            }

            $imageUrl = $response->json('data.0.url');

            if (empty($imageUrl)) {
                throw new \Exception('No image URL returned from OpenAI.');
            }

            $imageResponse = Http::timeout(30)->get($imageUrl);

            if ($imageResponse->failed()) {
                throw new \Exception('Failed to download generated image.');
            }

            $category = MediaItem::query()->whereNull('media_category_id')->first()?->mediaCategory
                ?? \App\Models\MediaCategory::where('is_default', true)->first()
                ?? \App\Models\MediaCategory::first();

            $categorySlug = $category?->slug ?? 'uncategorized';
            $filename = 'ai-'.now()->format('YmdHis').'-'.substr(md5($imagePrompt), 0, 6).'.jpg';
            $storagePath = $categorySlug.'/'.$filename;

            Storage::disk('public')->put($storagePath, $imageResponse->body());

            MediaItem::create([
                'media_category_id' => $category?->id,
                'path' => $storagePath,
                'filename' => $filename,
                'alt' => Str::limit($imagePrompt, 191),
                'size' => strlen($imageResponse->body()),
                'mime_type' => 'image/jpeg',
            ]);

            $drafts = session('editor_draft_overrides', []);

            if (str_starts_with($fieldKey, '@grid:')) {
                // @grid:grid_cards:0:image — update the sub-field inside the grid JSON.
                [, $gridKey, $itemIdx, $subKey] = explode(':', $fieldKey, 4);
                $itemIdx = (int) $itemIdx;
                $gridDraftKey = $rowSlug.':'.$gridKey;
                $existingJson = $drafts[$gridDraftKey]['value']
                    ?? ContentOverride::query()->where('row_slug', $rowSlug)->where('key', $gridKey)->value('value')
                    ?? '';
                $items = json_decode((string) $existingJson, true) ?: [];
                if (isset($items[$itemIdx])) {
                    $items[$itemIdx][$subKey] = $storagePath;
                }
                $drafts[$gridDraftKey] = ['type' => 'grid', 'value' => json_encode($items)];

                if (
                    $this->editingRowIndex !== null &&
                    isset($this->rows[$this->editingRowIndex]) &&
                    $this->rows[$this->editingRowIndex]['slug'] === $rowSlug
                ) {
                    $this->contentValues[$gridKey] = json_encode($items);
                }
            } else {
                $drafts[$rowSlug.':'.$fieldKey] = ['type' => 'image', 'value' => $storagePath];

                if (
                    $this->editingRowIndex !== null &&
                    isset($this->rows[$this->editingRowIndex]) &&
                    $this->rows[$this->editingRowIndex]['slug'] === $rowSlug
                ) {
                    $this->contentValues[$fieldKey] = $storagePath;
                }
            }

            session(['editor_draft_overrides' => $drafts]);

            $this->isDirty = true;
            $this->dispatch('ai-section-content-generated', rowSlug: $rowSlug, values: [$fieldKey]);

            return ['path' => $storagePath, 'error' => null];
        } catch (\Exception $e) {
            return ['path' => null, 'error' => $e->getMessage()];
        }
    }

    public function generateAiSectionContent(string $rowSlug, string $prompt): void
    {
        $row = collect($this->rows)->firstWhere('slug', $rowSlug);

        if (! $row) {
            $this->dispatch('ai-section-content-error', rowSlug: $rowSlug, message: 'Row not found.');

            return;
        }

        $skipKeys = [
            'section_classes', 'section_container_classes', 'section_id', 'section_attrs',
            'section_animation', 'section_animation_delay', 'section_bg_image',
            'section_bg_position', 'section_bg_size', 'section_bg_repeat', 'section_style',
        ];

        $isStructuralGridSubKey = function (string $k): bool {
            if (in_array($k, ['icon', 'image', 'alt', 'url'], true)) {
                return true;
            }
            if (str_starts_with($k, 'toggle_')) {
                return true;
            }
            if (
                str_ends_with($k, '_image') ||
                str_ends_with($k, '_alt') ||
                str_ends_with($k, '_url') ||
                str_ends_with($k, '_new_tab') ||
                str_ends_with($k, '_classes') ||
                str_ends_with($k, '_htag') ||
                str_ends_with($k, '_id') ||
                str_ends_with($k, '_attrs')
            ) {
                return true;
            }

            return false;
        };

        $allFields = $this->parseContentFields($row['blade'], $rowSlug);
        $drafts = session('editor_draft_overrides', []);

        $gridFields = [];
        foreach ($allFields as $field) {
            if ($field['type'] !== 'grid' || ! str_starts_with($field['key'], 'grid_')) {
                continue;
            }
            $draftKey = $field['slug'].':'.$field['key'];
            $draft = $drafts[$draftKey] ?? null;
            $currentJson = $draft !== null
                ? ($draft['value'] ?? '')
                : (\App\Models\ContentOverride::query()->where('row_slug', $rowSlug)->where('key', $field['key'])->value('value') ?? $field['default'] ?? '');
            $items = json_decode((string) $currentJson, true);
            if (! is_array($items) || empty($items)) {
                $items = json_decode((string) ($field['default'] ?? ''), true) ?: [];
            }
            if (! empty($items)) {
                $gridFields[] = [
                    'slug' => $field['slug'],
                    'key' => $field['key'],
                    'label' => $field['label'],
                    'items' => $items,
                ];
            }
        }

        $textFields = array_values(array_filter($allFields, function (array $field) use ($skipKeys): bool {
            if (in_array($field['key'], $skipKeys, true)) {
                return false;
            }
            if (! in_array($field['type'], ['text', 'richtext'], true)) {
                return false;
            }
            if (
                str_ends_with($field['key'], '_classes') ||
                str_ends_with($field['key'], '_url') ||
                str_ends_with($field['key'], '_alt') ||
                str_ends_with($field['key'], '_new_tab') ||
                str_ends_with($field['key'], '_id') ||
                str_ends_with($field['key'], '_attrs') ||
                str_ends_with($field['key'], '_htag') ||
                str_starts_with($field['key'], 'toggle_')
            ) {
                return false;
            }

            return true;
        }));

        if (empty($textFields) && empty($gridFields)) {
            $this->dispatch('ai-section-content-error', rowSlug: $rowSlug, message: 'No editable text fields found in this section.');

            return;
        }

        $overrides = \App\Models\ContentOverride::query()
            ->where('row_slug', $rowSlug)
            ->whereIn('key', array_column($textFields, 'key'))
            ->get()
            ->keyBy('key');

        $fieldSchema = [];
        foreach ($textFields as $field) {
            $draftKey = $field['slug'].':'.$field['key'];
            $draft = $drafts[$draftKey] ?? null;
            $currentValue = $draft !== null
                ? ($draft['value'] ?? '')
                : ($overrides->get($field['key'])?->value ?? '');
            $fieldSchema[] = [
                'key' => $field['key'],
                'label' => $field['label'],
                'type' => $field['type'],
                'current' => strip_tags((string) $currentValue),
            ];
        }

        $provider = \App\Models\Setting::get('ai.text_provider', 'google');

        $currentContent = collect($fieldSchema)
            ->filter(fn (array $f): bool => ! empty($f['current']))
            ->map(fn (array $f): string => $f['label'].': '.$f['current'])
            ->implode("\n");

        $aiFieldsList = array_map(fn (array $f): array => ['key' => $f['key'], 'label' => $f['label'], 'type' => $f['type']], $fieldSchema);
        foreach ($gridFields as $gf) {
            $aiFieldsList[] = ['key' => $gf['key'], 'label' => $gf['label'], 'type' => 'grid', 'items' => $gf['items']];
        }
        $fieldsJson = json_encode($aiFieldsList);

        $systemPrompt = 'You are a web content writer. You will be given a list of text fields for a website section and a description of what the section should contain. Generate appropriate content for each field. Respond ONLY with valid JSON where each key is a field key and each value is the content string or array. For richtext fields, use simple HTML (p, strong, em tags only). For text fields, return plain text only. For grid fields (type="grid"), return a JSON array of the same length as the items provided — update only the text sub-fields in each item and preserve all structural sub-fields (icon, image, alt, url, toggle_*, *_image, *_url, *_alt, *_new_tab, *_classes) exactly as given. Keep content concise and professional. No explanations, no markdown code fences, no extra keys.';
        $userMessage = "Section fields (JSON): {$fieldsJson}\n\nInstruction: {$prompt}";

        if ($currentContent) {
            $userMessage .= "\n\nCurrent content for reference:\n{$currentContent}";
        }

        try {
            if ($provider === 'openai') {
                $apiKey = \App\Models\Setting::get('ai.openai_key', '');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => \App\Models\Setting::get('ai.openai_model'),
                    'max_tokens' => 4096,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'OpenAI API error.');
                }

                $content = $response->json('choices.0.message.content', '');
            } elseif ($provider === 'google') {
                $apiKey = \App\Models\Setting::get('ai.google_key', '');
                $model = \App\Models\Setting::get('ai.google_model', 'gemini-2.0-flash');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => [['parts' => [['text' => $userMessage]]]],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Google AI API error.');
                }

                $content = $response->json('candidates.0.content.parts.0.text', '');
            } else {
                $apiKey = \App\Models\Setting::get('ai.claude_key', '');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => \App\Models\Setting::get('ai.claude_model'),
                    'max_tokens' => 4096,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                ]);

                if ($response->failed()) {
                    throw new \Exception($response->json('error.message') ?? 'Claude API error.');
                }

                $content = $response->json('content.0.text', '');
            }

            $stripped = trim(preg_replace('/^```(?:json)?\s*/i', '', preg_replace('/\s*```$/i', '', trim($content))));
            $data = json_decode($stripped, true);

            if (! is_array($data)) {
                throw new \Exception('AI returned an unexpected response. Please try again with more detail.');
            }

            $updatedValues = [];

            foreach ($textFields as $field) {
                $key = $field['key'];

                if (! array_key_exists($key, $data)) {
                    continue;
                }

                $value = (string) $data[$key];
                $draftKey = $field['slug'].':'.$key;
                $drafts[$draftKey] = ['type' => $field['type'], 'value' => $value];
                $updatedValues[$key] = $value;

                if (
                    $this->editingRowIndex !== null &&
                    isset($this->rows[$this->editingRowIndex]) &&
                    $this->rows[$this->editingRowIndex]['slug'] === $rowSlug
                ) {
                    $this->contentValues[$key] = $value;
                }
            }

            foreach ($gridFields as $gf) {
                $key = $gf['key'];
                if (! array_key_exists($key, $data) || ! is_array($data[$key])) {
                    continue;
                }
                $mergedItems = [];
                foreach ($gf['items'] as $idx => $existingItem) {
                    $generated = $data[$key][$idx] ?? [];
                    $merged = $existingItem;
                    foreach ($generated as $subKey => $subVal) {
                        if (! $isStructuralGridSubKey($subKey)) {
                            $merged[$subKey] = (string) $subVal;
                        }
                    }
                    $mergedItems[] = $merged;
                }
                $gridJson = json_encode($mergedItems);
                $draftKey = $gf['slug'].':'.$key;
                $drafts[$draftKey] = ['type' => 'grid', 'value' => $gridJson];
                $updatedValues[$key] = $gridJson;

                if (
                    $this->editingRowIndex !== null &&
                    isset($this->rows[$this->editingRowIndex]) &&
                    $this->rows[$this->editingRowIndex]['slug'] === $rowSlug
                ) {
                    $this->contentValues[$key] = $gridJson;
                }
            }

            session(['editor_draft_overrides' => $drafts]);
            $this->isDirty = true;
            $this->refreshPreview();

            $this->dispatch('ai-section-content-generated', rowSlug: $rowSlug, values: $updatedValues);
        } catch (\Exception $e) {
            $this->dispatch('ai-section-content-error', rowSlug: $rowSlug, message: $e->getMessage());
        }
    }
}; ?>

<div id="editor-component-root">
    {{-- Item Picker --}}
    <flux:modal wire:model="showItemPicker" class="w-full max-w-sm">
        <flux:heading size="lg" class="mb-1">{{ __('Add Item') }}</flux:heading>
        <flux:subheading class="mb-4">{{ __('Choose an item to add to this section.') }}</flux:subheading>
        <div id="editor-item-picker-grid" class="grid grid-cols-2 gap-2">
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
        <div id="editor-library-tabs" class="flex gap-1 mb-4 border-b border-zinc-200 dark:border-zinc-700">
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

        <div id="editor-library-content" class="min-h-[40rem]">
        @if ($libraryTab === 'rows')
            <div id="editor-library-rows-toolbar" class="flex gap-3 mb-4">
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
                <div id="editor-library-rows-empty" class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="squares-2x2" class="size-10 mx-auto mb-3 opacity-40" />
                    <p class="text-sm">No rows found.</p>
                </div>
            @else
                <div id="editor-library-rows-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[40rem] overflow-y-auto pr-1">
                    @foreach ($this->libraryRows as $libRow)
                        <div wire:key="lib-{{ $libRow->id }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden hover:border-primary/40 transition-colors flex flex-col">
                            {{-- Live preview --}}
                            <div id="editor-library-row-preview-{{ $libRow->id }}" class="relative h-44 overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border-b border-zinc-200 dark:border-zinc-700">
                                <div id="editor-library-row-preview-spinner-{{ $libRow->id }}" class="absolute inset-0 flex items-center justify-center animate-pulse pointer-events-none">
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
                            <div id="editor-library-row-body-{{ $libRow->id }}" class="p-3 flex flex-col flex-1">
                                <div id="editor-library-row-info-{{ $libRow->id }}" class="flex-1 min-w-0">
                                    <div id="editor-library-row-name-{{ $libRow->id }}" class="font-medium text-zinc-900 dark:text-white text-sm truncate">{{ $libRow->name }}</div>
                                    @if ($libRow->description)
                                        <div id="editor-library-row-desc-{{ $libRow->id }}" class="text-xs text-zinc-500 dark:text-zinc-400 truncate mt-0.5">{{ $libRow->description }}</div>
                                    @endif
                                    <flux:badge size="sm" class="mt-1">{{ $libRow->category->label() }}</flux:badge>
                                </div>
                                <div id="editor-library-row-actions-{{ $libRow->id }}" class="mt-2 pt-2 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-2">
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
                <div id="editor-library-shared-empty" class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="share" class="size-10 mx-auto mb-3 opacity-40" />
                    <p class="text-sm">No shared rows yet.</p>
                    <p class="text-xs mt-1">Use the "Make Shared" action on any row to share it.</p>
                </div>
            @else
                <div id="editor-library-shared-list" class="space-y-2 max-h-[40rem] overflow-y-auto">
                    @foreach ($this->sharedLibraryRows as $sharedRow)
                        <div wire:key="shared-{{ $sharedRow->slug }}" class="flex items-center gap-3 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors">
                            <div id="editor-library-shared-info-{{ $sharedRow->slug }}" class="flex-1 min-w-0">
                                <div id="editor-library-shared-name-{{ $sharedRow->slug }}" class="font-medium text-zinc-900 dark:text-white text-sm truncate">{{ $sharedRow->name }}</div>
                                <div id="editor-library-shared-slug-{{ $sharedRow->slug }}" class="text-[10px] font-mono text-zinc-400 dark:text-zinc-500 truncate mt-0.5">{{ $sharedRow->slug }}</div>
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
            <div id="editor-library-groups-toolbar" class="flex gap-3 mb-4">
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
                <div id="editor-library-groups-empty" class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="document-text" class="size-10 mx-auto mb-3 opacity-40" />
                    <p class="text-sm">No row groups found.</p>
                    <p class="text-xs mt-1">Create groups in the Design Library to quickly insert multiple rows at once.</p>
                </div>
            @else
                <div id="editor-library-groups-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[40rem] overflow-y-auto pr-1">
                    @foreach ($this->libraryPages as $libPage)
                        <div wire:key="page-{{ $libPage->id }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden hover:border-primary/40 transition-colors flex flex-col">
                            {{-- Live preview --}}
                            <div id="editor-library-page-preview-{{ $libPage->id }}" class="relative h-44 overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border-b border-zinc-200 dark:border-zinc-700">
                                <div id="editor-library-page-preview-spinner-{{ $libPage->id }}" class="absolute inset-0 flex items-center justify-center animate-pulse pointer-events-none">
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
                            <div id="editor-library-page-body-{{ $libPage->id }}" class="p-3 flex flex-col flex-1">
                                <div id="editor-library-page-info-{{ $libPage->id }}" class="flex-1 min-w-0">
                                    <div id="editor-library-page-name-{{ $libPage->id }}" class="font-medium text-zinc-900 dark:text-white text-sm truncate">{{ $libPage->name }}</div>
                                    @if ($libPage->description)
                                        <div id="editor-library-page-desc-{{ $libPage->id }}" class="text-xs text-zinc-500 dark:text-zinc-400 truncate mt-0.5">{{ $libPage->description }}</div>
                                    @endif
                                    <flux:badge size="sm" class="mt-1 shrink-0">{{ $libPage->website_category->label() }}</flux:badge>
                                </div>
                                @if (! empty($libPage->row_names))
                                    <div id="editor-library-page-row-names-{{ $libPage->id }}" class="flex flex-wrap gap-1 mt-2">
                                        @foreach ($libPage->row_names as $rowName)
                                            <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400">
                                                <flux:icon name="rectangle-stack" class="size-2.5" />
                                                {{ $rowName }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                <div id="editor-library-page-actions-{{ $libPage->id }}" class="mt-2 pt-2 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-2">
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
        id="editor-root"
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
                    try { const s = next.contentDocument.createElement('style'); s.textContent = 'html { background: #fdfdfc !important; }'; next.contentDocument.head.appendChild(s); } catch (e) {}
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
                if ($event.data.editorItemIndex !== null && $event.data.editorItemIndex !== undefined) { $dispatch('pending-item-index', { itemIndex: $event.data.editorItemIndex }); }
                selectRowBySlug($event.data.editorRowSlug, !!$event.data.editorGroup);
            }
            else if ($event.origin === window.location.origin && $event.data && $event.data.type === 'editor-save-page' && $wire.file) { $wire.saveFile(); }
        "
        @navigate-to-accessibility-issue.window="
            const { rowSlug, fieldKey, group } = $event.detail;
            if (group) $dispatch('pending-group', { group: group });
            selectRowBySlug(rowSlug, true);
            if (fieldKey) {
                setTimeout(() => {
                    const el = document.querySelector('[data-field-key=\'' + fieldKey + '\']');
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        el.style.transition = 'box-shadow 0.2s';
                        el.style.boxShadow = '0 0 0 2px var(--color-primary)';
                        el.style.borderRadius = '6px';
                        setTimeout(() => { el.style.boxShadow = ''; el.style.borderRadius = ''; }, 1800);
                    }
                }, 400);
            }
        "
        class="flex flex-col h-screen overflow-hidden bg-white dark:bg-zinc-900"
    >
        {{-- Editor toolbar --}}
        <div id="editor-toolbar" class="sticky top-0 z-30 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 px-6 py-3 flex items-center gap-3">
            <div id="editor-toolbar-left" class="flex-1 flex items-center gap-3">
                <flux:tooltip content="{{ $this->isLayoutPartial ? 'Back to Templates' : 'Go to page list' }}" position="bottom">
                    <flux:button href="{{ $this->isLayoutPartial ? route('dashboard.templates') : route('dashboard.pages') }}" variant="outline" size="sm" icon="arrow-left" wire:navigate />
                </flux:tooltip>

                @if ($liveUrl)
                    <flux:tooltip content="Go to website frontend" position="bottom">
                        <flux:button href="{{ $liveUrl }}" variant="outline" size="sm" icon="globe-alt" />
                    </flux:tooltip>
                @endif

                @if (! $this->isLayoutPartial)
                    <flux:tooltip content="Selected Page">
                        <flux:select wire:model.live="file" placeholder="Select a page to edit…" size="sm" class="w-36">
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
                <div id="editor-status-badges" class="flex-1 flex items-center justify-center gap-1.5">
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

                    <flux:tooltip content="{{ $seoTitle ? 'Page title is set.' : 'No page title set. Click to generate with AI.' }}" position="bottom">
                        <span
                            x-data
                            x-bind:class="$wire.seoTitle
                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'"
                            class="text-xs font-medium px-2 py-0.5 rounded-full cursor-pointer"
                            x-text="$wire.seoTitle ? 'Meta Tags' : 'No Meta Tags'"
                            @click="$wire.showSeoModal = true; $dispatch('open-settings-section', 'seo')"
                        ></span>
                    </flux:tooltip>

                    @if ($accessibilityScannedSaveCount >= 0)
                        <flux:tooltip content="{{ count($accessibilityIssues) > 0 ? count($accessibilityIssues).' accessibility issue(s) found.' : 'No accessibility issues found.' }}" position="bottom">
                            <span
                                class="text-xs font-medium px-2 py-0.5 rounded-full cursor-pointer {{ count($accessibilityIssues) > 0 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}"
                                wire:click="$set('showAccessibilityModal', true)"
                            >{{ count($accessibilityIssues) > 0 ? 'A11y Failed' : 'A11y Passed' }}</span>
                        </flux:tooltip>
                    @endif

                    <flux:tooltip content="{{ $seoNoindex ? 'Search engines are prevented from indexing this page.' : 'Search engines can index this page.' }}" position="bottom">
                        <span
                            x-data
                            x-bind:class="$wire.seoNoindex
                                ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                                : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'"
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
                                : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'"
                            class="text-xs font-medium px-2 py-0.5 rounded-full cursor-pointer"
                            x-text="$wire.redirectUrl ? 'Redirect' : 'No Redirect'"
                            @click="$wire.showSeoModal = true; $dispatch('open-settings-section', 'redirect')"
                        ></span>
                    </flux:tooltip>
                </div>
            @endif

            {{-- Center: preview width controls --}}
            <div id="editor-preview-controls" class="flex-1 flex justify-center items-center gap-1">
                @if ($file)
                    <div id="editor-breakpoint-standard" x-show="! showAllBreakpoints" class="flex items-center gap-0.5">
                        <flux:tooltip content="Mobile (390px)" position="bottom">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="device-phone-mobile"
                                x-on:click="setWidth('390px')"
                                x-bind:class="previewWidth === '390px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                                :loading="false"
                            />
                        </flux:tooltip>
                        <flux:tooltip content="Tablet (768px)" position="bottom">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="device-tablet"
                                x-on:click="setWidth('768px')"
                                x-bind:class="previewWidth === '768px' && ! showAllBreakpoints ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                                :loading="false"
                            />
                        </flux:tooltip>
                        <flux:tooltip content="Desktop (full width)" position="bottom">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="computer-desktop"
                                x-on:click="setWidth(null)"
                                x-bind:class="previewWidth === null ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                                :loading="false"
                            />
                        </flux:tooltip>
                    </div>

                    <div id="editor-breakpoint-extended" x-show="showAllBreakpoints" class="flex items-center gap-0.5" style="display: none">
                        <flux:tooltip content="Mobile (375px)" position="bottom">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="device-phone-mobile"
                                x-on:click="setWidth('375px')"
                                x-bind:class="previewWidth === '375px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                                :loading="false"
                            />
                        </flux:tooltip>
                        <flux:tooltip content="SM — 640px" position="bottom">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                x-on:click="setWidth('640px')"
                                x-bind:class="previewWidth === '640px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                                :loading="false"
                            >sm</flux:button>
                        </flux:tooltip>
                        <flux:tooltip content="MD — 768px" position="bottom">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                x-on:click="setWidth('768px')"
                                x-bind:class="previewWidth === '768px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                                :loading="false"
                            >md</flux:button>
                        </flux:tooltip>
                        <flux:tooltip content="LG — 1024px" position="bottom">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                x-on:click="setWidth('1024px')"
                                x-bind:class="previewWidth === '1024px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                                :loading="false"
                            >lg</flux:button>
                        </flux:tooltip>
                        <flux:tooltip content="XL — 1280px" position="bottom">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                x-on:click="setWidth('1280px')"
                                x-bind:class="previewWidth === '1280px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                                :loading="false"
                            >xl</flux:button>
                        </flux:tooltip>
                        <flux:tooltip content="2XL — 1536px" position="bottom">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                x-on:click="setWidth('1536px')"
                                x-bind:class="previewWidth === '1536px' ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                                :loading="false"
                            >2xl</flux:button>
                        </flux:tooltip>
                        <flux:tooltip content="Desktop (full width)" position="bottom">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="computer-desktop"
                                x-on:click="setWidth(null)"
                                x-bind:class="previewWidth === null ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                                :loading="false"
                            />
                        </flux:tooltip>
                    </div>

                    <div id="editor-toolbar-breakpoint-separator" class="w-px h-4 bg-zinc-200 dark:bg-zinc-700 mx-1"></div>

                    <flux:tooltip content="Toggle breakpoint mode" position="bottom">
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="arrows-right-left"
                            x-on:click="showAllBreakpoints = ! showAllBreakpoints"
                            x-bind:class="showAllBreakpoints ? 'bg-zinc-200! dark:bg-zinc-700!' : ''"
                            :loading="false"
                        />
                    </flux:tooltip>
                @endif
            </div>

            <div id="editor-toolbar-right" class="flex-1 flex items-center justify-end gap-2">
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
            <div id="editor-no-file-placeholder" class="flex items-center justify-center h-96 text-zinc-500 dark:text-zinc-400">
                <div id="editor-no-file-placeholder-inner" class="text-center">
                    <flux:icon name="document-text" class="size-16 mx-auto mb-4 opacity-30" />
                    <flux:heading class="text-zinc-500">Select a page to edit</flux:heading>
                    <flux:text class="mt-2 text-sm">Choose a volt file from the dropdown above to get started.</flux:text>
                </div>
            </div>
        @else
            <div id="editor-main-layout" class="flex flex-1 overflow-hidden">
                {{-- Right panel: row list / inline content editor --}}
                <div
                    id="editor-sidebar"
                    class="w-96 shrink-0 order-last border-l border-zinc-200 dark:border-zinc-700 flex flex-col overflow-hidden"
                    x-data="{ editorOpen: false, designMode: false, advancedMode: false, groupMode: null, allGroupsOpen: false, selectedRowIndex: null, pendingGroup: null, pendingSubgroup: null, pendingItemIndex: null }"
                    x-on:pending-group.window="pendingGroup = $event.detail.group"
                    x-on:pending-subgroup.window="pendingSubgroup = $event.detail.subgroup"
                    x-on:pending-item-index.window="pendingItemIndex = $event.detail.itemIndex"
                    x-on:content-editor-opened.window="
                        editorOpen = true; designMode = false; advancedMode = false;
                        if (pendingGroup) {
                            const g = pendingGroup; pendingGroup = null;
                            const sg = pendingSubgroup; pendingSubgroup = null;
                            const gi = pendingItemIndex; pendingItemIndex = null;
                            $nextTick(() => {
                                $dispatch('open-group', { group: g });
                                $nextTick(() => {
                                    if (sg) { $dispatch('open-subgroup', { subgroup: sg }); }
                                    if (gi !== null) { $dispatch('open-grid-item', { group: g, itemIndex: gi }); }
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
                    <div id="editor-content-editor-view" x-show="editorOpen" class="flex flex-col flex-1 min-h-0" style="display: none">
                        @if ($editingRowIndex !== null && isset($rows[$editingRowIndex]))
                        <div id="editor-content-editor-header" class="shrink-0 flex items-center gap-2 p-3 border-b border-zinc-200 dark:border-zinc-700">
                            <flux:button
                                @click="editorOpen = false"
                                variant="ghost"
                                size="sm"
                                icon="arrow-left"
                                title="Back to rows"
                            />
                            <div id="editor-content-editor-row-name" class="min-w-0 flex-1">
                                <div id="editor-content-editor-row-name-text" class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $rows[$editingRowIndex]['name'] }}</div>
                            </div>
                            <div id="editor-content-mode-switcher" class="flex rounded-md border border-zinc-200 dark:border-zinc-700 overflow-hidden shrink-0">
                                <flux:tooltip content="Content" position="bottom">
                                    <button type="button" @click="if (!designMode && !advancedMode) { allGroupsOpen = !allGroupsOpen; $dispatch('set-group-open', { value: allGroupsOpen }); } else { designMode = false; advancedMode = false; allGroupsOpen = true; $wire.resetEmptyClassesFields(); $dispatch('set-group-mode', {}); $dispatch('set-group-open', { value: true }); }" :class="!designMode && !advancedMode ? 'bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'" class="p-1.5 transition-colors"><flux:icon name="document-text" class="size-3.5" /></button>
                                </flux:tooltip>
                                <flux:tooltip content="Design" position="bottom">
                                    <button type="button" @click="if (designMode && !advancedMode) { allGroupsOpen = !allGroupsOpen; $dispatch('set-group-open', { value: allGroupsOpen }); } else { designMode = true; advancedMode = false; allGroupsOpen = true; $wire.resetEmptyClassesFields(); $dispatch('set-group-mode', {}); $dispatch('set-group-open', { value: true }); }" :class="designMode && !advancedMode ? 'bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'" class="p-1.5 transition-colors border-l border-zinc-200 dark:border-zinc-700"><flux:icon name="paint-brush" class="size-3.5" /></button>
                                </flux:tooltip>
                                <flux:tooltip content="Advanced" position="bottom">
                                    <button type="button" @click="if (advancedMode) { allGroupsOpen = !allGroupsOpen; $dispatch('set-group-open', { value: allGroupsOpen }); } else { advancedMode = true; designMode = false; allGroupsOpen = true; $wire.resetEmptyClassesFields(); $dispatch('set-group-mode', {}); $dispatch('set-group-open', { value: true }); }" :class="advancedMode ? 'bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'" class="p-1.5 transition-colors border-l border-zinc-200 dark:border-zinc-700"><flux:icon name="code-bracket" class="size-3.5" /></button>
                                </flux:tooltip>
                            </div>
                        </div>

                        <div id="editor-content-editor-body" class="flex-1 overflow-y-auto p-4">
                            @if (! empty($rows[$editingRowIndex]['shared'] ?? false))
                                <div id="editor-shared-row-notice" class="mb-3 flex items-start gap-2 px-3 py-2 rounded-lg bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 text-xs text-blue-700 dark:text-blue-300">
                                    <flux:icon name="share" class="size-3.5 mt-0.5 shrink-0" />
                                    <span>Shared row — changes affect all pages using it.</span>
                                </div>
                            @endif

                            {{-- Auto BEM — shown when Advanced tab is active --}}
                            <div id="editor-auto-bem-panel" x-show="advancedMode" x-cloak class="mb-3">
                                <button
                                    wire:click="applyAutoBem({{ $editingRowIndex }})"
                                    type="button"
                                    class="w-full flex items-center justify-center gap-1.5 text-xs font-medium py-1.5 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 hover:text-primary hover:border-primary transition-colors"
                                    title="Auto-generate BEM IDs for all elements in this row. Uses Section ID (or row name) as the BEM block."
                                >
                                    <flux:icon name="finger-print" class="size-3.5" />
                                    Auto BEM IDs
                                </button>
                            </div>

                            @php
                                $rowItemBlocks = $editingRowIndex !== null ? $this->extractItemBlocks($rows[$editingRowIndex]['blade']) : [];
                            @endphp
                            @if (! empty($rowItemBlocks))
                                {{-- Item-based rows: each item is a collapsible card with fields + actions inside --}}
                                @php $allItemFieldGroups = collect($contentFields)->groupBy('group'); @endphp
                                <div id="editor-item-list" class="space-y-2 mb-4" x-data="{ dragging: null, over: null }">
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
                                            x-data="{ open: false, groupMode: null, _dragTarget: null }"
                                            data-group-id="{{ $item['prefix'] }}"
                                            @set-group-open.window="open = $event.detail.value"
                                            @set-group-mode.window="groupMode = null"
                                            @open-group.window="open = ($event.detail.group === '{{ $item['prefix'] }}')"
                                            @sidebar-item-opened.window="if ($event.detail.index !== {{ $item['index'] }}) open = false"
                                            @mousedown.capture="_dragTarget = $event.target"
                                            draggable="true"
                                            @dragstart="if (_dragTarget?.closest('textarea, input, select, [contenteditable]')) { $event.preventDefault(); return; } dragging = {{ $item['index'] }}"
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
                                            <div id="editor-item-header-{{ $item['index'] }}" class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700/50 cursor-pointer select-none" @click="open = !open; if (open) $dispatch('sidebar-item-opened', { index: {{ $itemAccordionIndex }} })">
                                                <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200 flex-1 truncate">{{ $item['name'] }}</span>
                                                @if ($itemHasContentFields)
                                                    <flux:tooltip content="Item content">
                                                        <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'content' : (!designMode && !advancedMode); if (isActive && open) { open = false; } else { groupMode = 'content'; open = true; $dispatch('sidebar-item-opened', { index: {{ $itemAccordionIndex }} }); }"
                                                            :class="(groupMode !== null ? groupMode === 'content' : (!designMode && !advancedMode)) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        ><flux:icon name="document-text" class="size-3.5" /></button>
                                                    </flux:tooltip>
                                                @endif
                                                @if ($itemHasClassesFields)
                                                    <flux:tooltip content="Item design">
                                                        <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode); if (isActive && open) { open = false; } else { groupMode = 'design'; open = true; $dispatch('sidebar-item-opened', { index: {{ $itemAccordionIndex }} }); }"
                                                            :class="(groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode)) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        ><flux:icon name="paint-brush" class="size-3.5" /></button>
                                                    </flux:tooltip>
                                                @endif
                                                @if ($itemHasAdvancedFields)
                                                    <flux:tooltip content="Item settings">
                                                        <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'advanced' : advancedMode; if (isActive && open) { open = false; } else { groupMode = 'advanced'; open = true; $dispatch('sidebar-item-opened', { index: {{ $itemAccordionIndex }} }); }"
                                                            :class="(groupMode !== null ? groupMode === 'advanced' : advancedMode) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        ><flux:icon name="code-bracket" class="size-3.5" /></button>
                                                    </flux:tooltip>
                                                @endif
                                                @if ($headerToggleField)
                                                    <flux:switch wire:model.live="contentValues.{{ $headerToggleField['key'] }}" @click.stop />
                                                @endif
                                            </div>
                                            {{-- Collapsible body: fields --}}
                                            <div id="editor-item-body-{{ $item['index'] }}" x-show="open" x-collapse>
                                                @if ($bodyFields->isNotEmpty())
                                                    @php
                                                        $flatFields = $bodyFields->filter(fn ($f) => empty($f['subgroup'] ?? null));
                                                        $subgroupedFields = $bodyFields->filter(fn ($f) => ! empty($f['subgroup'] ?? null))->groupBy('subgroup');
                                                    @endphp
                                                    <div id="editor-item-fields-{{ $item['index'] }}" class="border-t border-zinc-200 dark:border-zinc-700 p-3 space-y-4">
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
                                                                    <div id="editor-item-subgroup-actions-{{ $item['index'] }}" class="flex items-center gap-2">
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
                                            <div id="editor-item-actions-{{ $item['index'] }}" class="relative flex items-center px-2 py-1.5 border-t border-zinc-200 dark:border-zinc-700">
                                                <div id="editor-item-primary-actions-{{ $item['index'] }}" class="flex items-center gap-0.5">
                                                    <flux:button
                                                        wire:click="moveItemUp({{ $item['index'] }})"
                                                        variant="ghost" size="sm" icon="arrow-up"
                                                        :disabled="$isFirst"
                                                        :class="$isFirst ? 'opacity-15!' : ''"
                                                        tooltip="Move up" :loading="false"
                                                    />
                                                    <flux:button
                                                        wire:click="moveItemDown({{ $item['index'] }})"
                                                        variant="ghost" size="sm" icon="arrow-down"
                                                        :disabled="$isLast"
                                                        :class="$isLast ? 'opacity-15!' : ''"
                                                        tooltip="Move down" :loading="false"
                                                    />
                                                    <flux:tooltip content="Drag to reorder">
                                                        <flux:icon name="bars-2" class="size-4 text-zinc-400 dark:text-zinc-500 cursor-grab active:cursor-grabbing mx-2" />
                                                    </flux:tooltip>
                                                </div>
                                                <div id="editor-item-secondary-actions-{{ $item['index'] }}" class="flex items-center gap-0.5 ml-auto">
                                                    <flux:button
                                                        wire:click="openItemPickerAbove({{ $item['index'] }})"
                                                        variant="ghost"
                                                        size="sm"
                                                        tooltip="Insert item above"
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
                                                        tooltip="Insert item below"
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
                                                        tooltip="Delete item" :loading="false"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif (empty($contentFields))
                                <div id="editor-no-content-fields" class="text-center py-8 text-zinc-400 dark:text-zinc-500">
                                    <flux:icon name="plus-circle" class="size-10 mx-auto mb-2 opacity-40" />
                                    <p class="text-sm">No content yet.</p>
                                    <p class="text-xs mt-1">Add items to build out this section.</p>
                                </div>
                            @else
                                @php
                                    $dlComponents = $this->extractTopLevelComponentsFromBlade($rows[$editingRowIndex]['blade']);

                                    // Detect language suffixes present in the injected variant fields (e.g. '__es', '__fr').
                                    $injectedLangSuffixes = collect($contentFields)
                                        ->pluck('key')
                                        ->filter(fn ($k) => (bool) preg_match('/__[a-z]{2,10}$/', $k))
                                        ->map(fn ($k) => preg_replace('/^.+(?=__[a-z]{2,10}$)/', '', $k))
                                        ->unique()
                                        ->values()
                                        ->all();

                                    // Expand each component's fieldKeys to include language variant keys so that
                                    // injected __es / __fr fields appear in the correct sidebar group.
                                    if (! empty($injectedLangSuffixes)) {
                                        foreach ($dlComponents as &$dlComp) {
                                            $expanded = $dlComp['fieldKeys'];
                                            foreach ($dlComp['fieldKeys'] as $baseKey) {
                                                foreach ($injectedLangSuffixes as $suffix) {
                                                    $expanded[] = $baseKey . $suffix;
                                                }
                                            }
                                            $dlComp['fieldKeys'] = array_unique($expanded);
                                        }
                                        unset($dlComp);
                                    }

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
                                            id="editor-orphaned-fields-group"
                                            x-data="{ open: false, groupMode: null }"
                                            @set-group-open.window="open = $event.detail.value"
                                            @set-group-mode.window="groupMode = null"
                                            @sidebar-group-opened.window="if ($event.detail.slug === '{{ $rows[$editingRowIndex]['slug'] }}' && $event.detail.id !== 'row-settings') open = false"
                                            class="mb-2 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden"
                                        >
                                            <div id="editor-orphaned-fields-header" class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700/50 cursor-pointer select-none" @click="open = !open; if (open) $dispatch('sidebar-group-opened', { slug: '{{ $rows[$editingRowIndex]['slug'] }}', id: 'row-settings' })">
                                                <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200 flex-1 truncate">Section Settings</span>
                                                @if ($orphanHasClassesFields)
                                                    <flux:tooltip content="Component design">
                                                        <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode); if (isActive && open) { open = false; } else { groupMode = 'design'; open = true; $dispatch('sidebar-group-opened', { slug: '{{ $rows[$editingRowIndex]['slug'] }}', id: 'row-settings' }); }"
                                                            :class="(groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode)) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        ><flux:icon name="paint-brush" class="size-3.5" /></button>
                                                    </flux:tooltip>
                                                @endif
                                                @if ($orphanHasAdvancedFields)
                                                    <flux:tooltip content="Component settings">
                                                        <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'advanced' : advancedMode; if (isActive && open) { open = false; } else { groupMode = 'advanced'; open = true; $dispatch('sidebar-group-opened', { slug: '{{ $rows[$editingRowIndex]['slug'] }}', id: 'row-settings' }); }"
                                                            :class="(groupMode !== null ? groupMode === 'advanced' : advancedMode) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        ><flux:icon name="code-bracket" class="size-3.5" /></button>
                                                    </flux:tooltip>
                                                @endif
                                            </div>
                                            <div x-show="open" x-collapse class="border-t border-zinc-200 dark:border-zinc-700 p-3 space-y-4">
                                                @foreach ($orphanedFields as $field)
                                                    @include('pages.dashboard.pages.partials.content-field', ['field' => $field])
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    <div id="editor-components-list" class="space-y-2" x-data="{ dragging: null, over: null }">
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
                                                x-data="{ open: false, groupMode: null, _dragTarget: null }"
                                                data-group-id="{{ $comp['attrs']['prefix'] ?? $comp['slug'] }}"
                                                @set-group-open.window="open = $event.detail.value"
                                                @set-group-mode.window="groupMode = null"
                                                @open-group.window="open = ($event.detail.group === '{{ $comp['attrs']['prefix'] ?? $comp['slug'] }}')"
                                                @sidebar-group-opened.window="if ($event.detail.slug === '{{ $rows[$editingRowIndex]['slug'] }}' && $event.detail.id !== '{{ $comp['attrs']['prefix'] ?? $comp['slug'] }}') open = false"
                                                @mousedown.capture="_dragTarget = $event.target"
                                                draggable="true"
                                                @dragstart="if (_dragTarget?.closest('textarea, input, select, [contenteditable]')) { $event.preventDefault(); return; } dragging = {{ $comp['index'] }}"
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
                                                <div id="editor-comp-header-{{ $comp['index'] }}" class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700/50 cursor-pointer select-none" @click="if ({{ $compHasContentFields ? 'true' : 'false' }} || designMode || advancedMode || groupMode !== null) { open = !open; if (open) $dispatch('sidebar-group-opened', { slug: '{{ $compAccordionSlug }}', id: '{{ $compAccordionId }}' }); }">
                                                    <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200 flex-1 truncate">{{ $comp['name'] }}</span>
                                                    @if ($compHasContentFields)
                                                        <flux:tooltip content="Component content">
                                                            <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'content' : (!designMode && !advancedMode); if (isActive && open) { open = false; } else { groupMode = 'content'; open = true; $dispatch('sidebar-group-opened', { slug: '{{ $compAccordionSlug }}', id: '{{ $compAccordionId }}' }); }"
                                                                :class="(groupMode !== null ? groupMode === 'content' : (!designMode && !advancedMode)) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                            ><flux:icon name="document-text" class="size-3.5" /></button>
                                                        </flux:tooltip>
                                                    @endif
                                                    @if ($compHasClassesFields)
                                                        <flux:tooltip content="Component design">
                                                            <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode); if (isActive && open) { open = false; } else { groupMode = 'design'; open = true; $dispatch('sidebar-group-opened', { slug: '{{ $compAccordionSlug }}', id: '{{ $compAccordionId }}' }); }"
                                                                :class="(groupMode !== null ? groupMode === 'design' : (designMode && !advancedMode)) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                            ><flux:icon name="paint-brush" class="size-3.5" /></button>
                                                        </flux:tooltip>
                                                    @endif
                                                    @if ($compHasAdvancedFields)
                                                        <flux:tooltip content="Component settings">
                                                            <button type="button" @click.stop="const isActive = groupMode !== null ? groupMode === 'advanced' : advancedMode; if (isActive && open) { open = false; } else { groupMode = 'advanced'; open = true; $dispatch('sidebar-group-opened', { slug: '{{ $compAccordionSlug }}', id: '{{ $compAccordionId }}' }); }"
                                                                :class="(groupMode !== null ? groupMode === 'advanced' : advancedMode) ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                            ><flux:icon name="code-bracket" class="size-3.5" /></button>
                                                        </flux:tooltip>
                                                    @endif
                                                    @if ($headerToggleField)
                                                        <flux:switch wire:model.live="contentValues.{{ $headerToggleField['key'] }}" @click.stop />
                                                    @endif
                                                </div>
                                                <div id="editor-comp-body-{{ $comp['index'] }}" x-show="open" x-collapse>
                                                    @if ($bodyFields->isNotEmpty())
                                                        @php
                                                            $flatFields = $bodyFields->filter(fn ($f) => empty($f['subgroup'] ?? null));
                                                            $subgroupedFields = $bodyFields->filter(fn ($f) => ! empty($f['subgroup'] ?? null))->groupBy('subgroup');
                                                        @endphp
                                                        <div id="editor-comp-fields-{{ $comp['index'] }}" class="border-t border-zinc-200 dark:border-zinc-700 p-3 space-y-4">
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
                                                                        <div id="editor-comp-subgroup-actions-{{ $comp['index'] }}" class="flex items-center gap-2">
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
                                                <div id="editor-comp-actions-{{ $comp['index'] }}" class="relative flex items-center px-2 py-1.5 border-t border-zinc-200 dark:border-zinc-700">
                                                    <div id="editor-comp-primary-actions-{{ $comp['index'] }}" class="flex items-center gap-0.5">
                                                        <flux:button
                                                            wire:click="moveComponentUp({{ $comp['index'] }})"
                                                            variant="ghost" size="sm" icon="arrow-up"
                                                            :disabled="$isFirst"
                                                            :class="$isFirst ? 'opacity-15!' : ''"
                                                            tooltip="Move up" :loading="false"
                                                        />
                                                        <flux:button
                                                            wire:click="moveComponentDown({{ $comp['index'] }})"
                                                            variant="ghost" size="sm" icon="arrow-down"
                                                            :disabled="$isLast"
                                                            :class="$isLast ? 'opacity-15!' : ''"
                                                            tooltip="Move down" :loading="false"
                                                        />
                                                        <flux:tooltip content="Drag to reorder">
                                                            <flux:icon name="bars-2" class="size-4 text-zinc-400 dark:text-zinc-500 cursor-grab active:cursor-grabbing mx-2" />
                                                        </flux:tooltip>
                                                    </div>
                                                    <div id="editor-comp-secondary-actions-{{ $comp['index'] }}" class="flex items-center gap-0.5 ml-auto">
                                                        <flux:button
                                                            wire:click="deleteComponent({{ $comp['index'] }})"
                                                            wire:confirm="Delete this component?"
                                                            variant="ghost" size="sm" icon="trash"
                                                            class="text-red-500 dark:text-red-400"
                                                            tooltip="Delete component" :loading="false"
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

                        <div id="editor-content-editor-footer" class="shrink-0 flex gap-2 p-3 border-t border-zinc-200 dark:border-zinc-700">
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
                    <div id="editor-row-list-view" x-show="!editorOpen" class="flex flex-col flex-1 min-h-0">
                        <div id="editor-row-list-header" class="shrink-0 px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                            <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">{{ __('Page Sections') }}</flux:heading>
                            <div id="editor-row-list-toolbar" class="flex items-center gap-0.5" x-data="{ allDesignsOpen: false, allAdvancedOpen: false, allBrowseOpen: false }">
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
                                <div id="editor-row-list-toolbar-separator-1" class="w-px h-3.5 bg-zinc-200 dark:bg-zinc-600 mx-0.5"></div>
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
                                <div id="editor-row-list-toolbar-separator-2" class="w-px h-4 bg-zinc-200 dark:bg-zinc-700 mx-0.5"></div>
                                <flux:tooltip content="Auto BEM IDs for all rows" position="bottom">
                                    <button type="button"
                                        x-on:click="$flux.modal('confirm-auto-bem').show()"
                                        class="p-1 rounded text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <flux:icon name="finger-print" class="size-3.5" />
                                    </button>
                                </flux:tooltip>
                                @if (\App\Models\Setting::get('ai.claude_key') || \App\Models\Setting::get('ai.openai_key'))
                                    <flux:tooltip content="Generate content for all sections" position="bottom">
                                        <button type="button"
                                            x-on:click="$dispatch('open-generate-all-modal', { rowCount: {{ count($rows) }} }); $flux.modal('generate-all-content').show()"
                                            class="p-1 rounded text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                        >
                                            <flux:icon name="sparkles" class="size-3.5" />
                                        </button>
                                    </flux:tooltip>
                                @endif
                                @php
                                    $globalMultiLangs = array_values(array_filter(
                                        \App\Models\Setting::get('site.languages', [['code' => 'en']]),
                                        fn ($l) => $l['code'] !== 'en'
                                    ));
                                @endphp
                                @if (! empty($globalMultiLangs) && (\App\Models\Setting::get('ai.claude_key') || \App\Models\Setting::get('ai.openai_key')))
                                    <flux:tooltip content="Translate all sections" position="bottom">
                                        <button type="button"
                                            x-on:click="$dispatch('open-translate-modal', { rowIndex: null, lang: '', rowCount: {{ count($rows) }} }); $flux.modal('translate-options').show()"
                                            class="p-1 rounded text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                        >
                                            <flux:icon name="language" class="size-3.5" />
                                        </button>
                                    </flux:tooltip>
                                @endif
                            </div>
                        </div>

                        @php
                            $rcAiEnabled = (bool) (\App\Models\Setting::get('ai.claude_key') || \App\Models\Setting::get('ai.openai_key'));
                            $rcThemeColorNames = [];
                            try {
                                $rcPubCss = resource_path('css/public.css');
                                if (file_exists($rcPubCss)) {
                                    preg_match('/@theme\s*\{([^}]+)\}/s', file_get_contents($rcPubCss), $rcThemeBlock);
                                    if (! empty($rcThemeBlock[1])) {
                                        preg_match_all('/--color-([\w]+):/', $rcThemeBlock[1], $rcCm);
                                        $rcThemeColorNames = array_values(array_unique($rcCm[1]));
                                    }
                                }
                            } catch (\Throwable) {}
                        @endphp
                        <div id="editor-row-list-body" class="flex-1 overflow-y-auto p-3 space-y-2" x-data="{ dragging: null, over: null }" @click.self="$dispatch('row-deselected')">
                            @forelse ($rows as $index => $row)
                                <div
                                    wire:key="row-item-{{ $row['slug'] }}"
                                    data-row-sidebar-index="{{ $index }}"
                                    x-data="{ panelMode: null, renamingRow: false, rowNameDraft: '', _dragTarget: null }"
                                    @collapse-all-rows.window="panelMode = null"
                                    @expand-all-rows.window="panelMode = 'design'"
                                    @expand-all-advanced.window="panelMode = 'advanced'"
                                    @expand-all-browse.window="panelMode = 'browse'"
                                    class="rounded-lg border bg-white dark:bg-zinc-900 overflow-hidden transition-colors {{ !empty($row['hidden']) ? 'opacity-60' : '' }}"
                                    :class="editorOpen && {{ $editingRowIndex ?? -1 }} === {{ $index }} ? 'border-primary' : (selectedRowIndex === {{ $index }} ? 'border-primary' : (panelMode !== null ? 'border-primary/50' : 'border-zinc-200 dark:border-zinc-700'))"
                                    @click="selectedRowIndex === {{ $index }} ? $dispatch('row-deselected') : $dispatch('row-selected', { index: {{ $index }} })"
                                    @mousedown.capture="_dragTarget = $event.target"
                                    draggable="true"
                                    @dragstart="if (_dragTarget?.closest('textarea, input, select, [contenteditable]')) { $event.preventDefault(); return; } dragging = {{ $index }}"
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
                                    <div id="editor-row-header-{{ $index }}" class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700/50">
                                        <div id="editor-row-name-area-{{ $index }}" class="flex-1 min-w-0">
                                            <div id="editor-row-name-flex-{{ $index }}" class="flex items-center gap-1.5">
                                                <div x-show="!renamingRow" class="group flex items-center gap-1 min-w-0 flex-1">
                                                    <span
                                                        class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate cursor-pointer hover:text-primary transition-colors"
                                                        title="Click to edit content"
                                                        @click.stop="$wire.openContentEditor({{ $index }})"
                                                    >{{ $row['name'] }}</span>
                                                    <flux:tooltip content="Rename row">
                                                    <button
                                                        type="button"
                                                        class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity p-0.5 rounded text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                                                        @click.stop="renamingRow = true; rowNameDraft = '{{ addslashes($row['name']) }}'; $nextTick(() => $refs.rowNameInput_{{ $index }}.select())"
                                                    ><flux:icon name="pencil" class="size-3" /></button>
                                                </flux:tooltip>
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
                                            </div>
                                        </div>
                                        @if (isset($rowDesignDefaults[$row['slug']]))
                                            <flux:tooltip content="Section content">
                                                <button type="button" @click.stop="panelMode = panelMode === 'content' ? null : 'content'"
                                                    :class="panelMode === 'content' ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                ><flux:icon name="document-text" class="size-3.5" /></button>
                                            </flux:tooltip>
                                            <flux:tooltip content="Section design">
                                                <button type="button" @click.stop="panelMode = panelMode === 'design' ? null : 'design'"
                                                    :class="panelMode === 'design' ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                ><flux:icon name="paint-brush" class="size-3.5" /></button>
                                            </flux:tooltip>
                                            <flux:tooltip content="Section settings">
                                                <button type="button" @click.stop="panelMode = panelMode === 'advanced' ? null : 'advanced'"
                                                    :class="panelMode === 'advanced' ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                ><flux:icon name="code-bracket" class="size-3.5" /></button>
                                            </flux:tooltip>
                                            <flux:tooltip content="Section design library">
                                                <button type="button" @click.stop="panelMode = panelMode === 'browse' ? null : 'browse'; if (panelMode === 'browse') $wire.openBrowseMode({{ $index }})"
                                                    :class="panelMode === 'browse' ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                ><flux:icon name="rectangle-stack" class="size-3.5" /></button>
                                            </flux:tooltip>
                                            @php
                                                $rowMultiLangs = array_values(array_filter(
                                                    \App\Models\Setting::get('site.languages', [['code' => 'en']]),
                                                    fn ($l) => $l['code'] !== 'en'
                                                ));
                                            @endphp
                                            @if (! empty($rowMultiLangs) && (\App\Models\Setting::get('ai.claude_key') || \App\Models\Setting::get('ai.openai_key')))
                                                <div id="editor-row-translate-{{ $index }}" class="relative" x-data="{ tlOpen: false }" @click.outside="tlOpen = false">
                                                    <flux:tooltip content="Translate section">
                                                        <button type="button" @click.stop="tlOpen = !tlOpen"
                                                            :class="tlOpen ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors'"
                                                        ><flux:icon name="language" class="size-3.5" /></button>
                                                    </flux:tooltip>
                                                    <div
                                                        x-show="tlOpen"
                                                        x-transition:enter="transition ease-out duration-100"
                                                        x-transition:enter-start="opacity-0 scale-95"
                                                        x-transition:enter-end="opacity-100 scale-100"
                                                        x-transition:leave="transition ease-in duration-75"
                                                        x-transition:leave-start="opacity-100 scale-100"
                                                        x-transition:leave-end="opacity-0 scale-95"
                                                        id="editor-row-translate-dropdown-{{ $index }}"
                                                        class="absolute right-0 top-full mt-1 z-20 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg py-1 min-w-40"
                                                    >
                                                        <p class="px-3 pt-1 pb-0.5 text-[10px] font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wide">Translate to</p>
                                                        @foreach ($rowMultiLangs as $rl)
                                                            <button
                                                                type="button"
                                                                @click.stop="tlOpen = false; $dispatch('open-translate-modal', { rowIndex: {{ $index }}, lang: '{{ $rl['code'] }}', rowCount: 1 }); $flux.modal('translate-options').show()"
                                                                class="w-full text-left px-3 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors flex items-center gap-2"
                                                            >
                                                                <span>{{ $rl['flag'] }}</span>
                                                                <span>{{ $rl['label'] }}</span>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                        <flux:tooltip :content="!empty($row['hidden']) ? 'Row hidden — click to show' : 'Click to hide row'">
                                            <flux:switch
                                                :checked="empty($row['hidden'])"
                                                @click.stop="$wire.toggleRowVisibility({{ $index }})"
                                            />
                                        </flux:tooltip>
                                    </div>

                                    {{-- Inline panel: design mode (classes) or advanced mode (section id) --}}
                                    @if (isset($rowDesignDefaults[$row['slug']]))
                                        <div id="editor-row-panel-{{ $index }}" x-show="panelMode !== null" x-collapse class="border-t border-zinc-200 dark:border-zinc-700">
                                            {{-- Content mode: background image --}}
                                            <div id="editor-row-panel-content-{{ $index }}" x-show="panelMode === 'content'" class="p-3 space-y-2">
                                                @php $aiRowBgEnabled = (bool) (\App\Models\Setting::get('ai.openai_key') || \App\Models\Setting::get('ai.fal_key') || \App\Models\Setting::get('ai.stability_key')); @endphp
                                                <div id="editor-row-bg-image-header-{{ $index }}" class="flex items-center justify-between">
                                                    <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Background Image</span>
                                                    @if ($aiRowBgEnabled)
                                                        <button
                                                            type="button"
                                                            onclick="window.dispatchEvent(new CustomEvent('open-ai-generate', { detail: { fieldKey: 'row-design-bg:{{ $row['slug'] }}', fieldType: 'image', fieldLabel: 'Background Image' } }))"
                                                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                                                            title="Generate background image with AI"
                                                        ><flux:icon name="sparkles" class="size-3.5" /></button>
                                                    @endif
                                                </div>
                                                @if (($rowDesignValues[$row['slug']]['section_bg_image'] ?? '') !== '')
                                                    <div id="editor-row-bg-image-preview-{{ $index }}" class="relative inline-block">
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

                                                {{-- Generate Content with AI --}}
                                                @php $aiSectionEnabled = (bool) (\App\Models\Setting::get('ai.claude_key') || \App\Models\Setting::get('ai.openai_key')); @endphp
                                                @if ($aiSectionEnabled)
                                                    <div id="editor-row-ai-generate-{{ $index }}" class="border-t border-zinc-200 dark:border-zinc-700 pt-2"
                                                        x-data="{
                                                            aiOpen: false,
                                                            aiPrompt: '',
                                                            aiGenerating: false,
                                                            aiError: '',
                                                            aiSuccess: false,
                                                            rowSlug: @js($row['slug']),
                                                            generate() {
                                                                if (!this.aiPrompt.trim()) return;
                                                                this.aiGenerating = true;
                                                                this.aiError = '';
                                                                this.aiSuccess = false;
                                                                $wire.generateAiSectionContent(this.rowSlug, this.aiPrompt);
                                                            }
                                                        }"
                                                        x-on:ai-section-content-generated.window="
                                                            if ($event.detail.rowSlug === rowSlug) {
                                                                aiGenerating = false;
                                                                aiSuccess = true;
                                                                aiPrompt = '';
                                                                setTimeout(() => aiSuccess = false, 4000);
                                                            }
                                                        "
                                                        x-on:ai-section-content-error.window="
                                                            if ($event.detail.rowSlug === rowSlug) {
                                                                aiGenerating = false;
                                                                aiError = $event.detail.message;
                                                            }
                                                        "
                                                    >
                                                        <button type="button" @click="aiOpen = !aiOpen" class="flex items-center gap-1.5 w-full text-left">
                                                            <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Generate Content with AI</span>
                                                            <flux:icon name="chevron-right" class="size-3 text-zinc-400 shrink-0 transition-transform duration-150 ml-auto" x-bind:class="aiOpen ? 'rotate-90' : ''" />
                                                        </button>
                                                        <div x-show="aiOpen" x-collapse class="mt-2 space-y-2">
                                                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed">Describe what this section should be about and AI will write all text fields at once.</p>
                                                            <textarea
                                                                x-model="aiPrompt"
                                                                placeholder="e.g. A hero section for a plumbing company in Austin, TX highlighting 24/7 emergency service"
                                                                rows="3"
                                                                class="w-full text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none placeholder-zinc-400"
                                                                @keydown.cmd.enter.prevent="generate()"
                                                                @keydown.ctrl.enter.prevent="generate()"
                                                            ></textarea>
                                                            <div x-show="aiError" class="text-[11px] text-red-500 dark:text-red-400 leading-snug" x-text="aiError"></div>
                                                            <div x-show="aiSuccess" class="flex items-center gap-1.5 text-[11px] text-green-600 dark:text-green-400">
                                                                <flux:icon name="check-circle" class="size-3 shrink-0" />
                                                                <span>Content generated — open fields to review.</span>
                                                            </div>
                                                            <button
                                                                type="button"
                                                                @click="generate()"
                                                                :disabled="aiGenerating || !aiPrompt.trim()"
                                                                class="flex items-center justify-center gap-1.5 w-full px-3 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                            >
                                                                <flux:icon name="sparkles" class="size-3 shrink-0" x-show="!aiGenerating" />
                                                                <flux:icon name="arrow-path" class="size-3 shrink-0 animate-spin" x-show="aiGenerating" />
                                                                <span x-text="aiGenerating ? 'Generating…' : 'Generate'"></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Design mode --}}
                                            <div id="editor-row-panel-design-{{ $index }}" x-show="panelMode === 'design'" class="p-3 space-y-3" x-data="{ openGroup: 'appearance' }">
                                                {{-- Appearance (section style preset) --}}
                                                <div>
                                                    <button type="button" @click="openGroup = openGroup === 'appearance' ? null : 'appearance'" class="flex items-center gap-1.5 w-full text-left">
                                                        <flux:icon name="chevron-right" class="size-3 text-zinc-400 shrink-0 transition-transform duration-150" x-bind:class="openGroup === 'appearance' ? 'rotate-90' : ''" />
                                                        <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Appearance</span>
                                                    </button>
                                                    <div x-show="openGroup === 'appearance'" x-collapse class="mt-2 pl-3">
                                                        <span class="text-[10px] uppercase font-semibold text-zinc-400 dark:text-zinc-500 mb-1 block">Section Style</span>
                                                        @if (! empty($rowStylePresets))
                                                            <select
                                                                wire:model.live="rowDesignValues.{{ $row['slug'] }}.section_style"
                                                                class="w-full text-xs rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                                            >
                                                                <option value="">— Default —</option>
                                                                @foreach ($rowStylePresets as $stylePreset)
                                                                    <option value="{{ $stylePreset['id'] }}">{{ $stylePreset['label'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <p class="text-xs text-zinc-400 dark:text-zinc-500">No section styles defined. <a href="{{ route('dashboard.settings.section-colors') }}" class="text-primary underline hover:text-primary/80 transition-colors" wire:navigate>Add styles →</a></p>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if (isset($rowDesignDefaults[$row['slug']]['section_animation']))
                                                    <div>
                                                        <button type="button" @click="openGroup = openGroup === 'anim' ? null : 'anim'" class="flex items-center gap-1.5 w-full text-left">
                                                            <flux:icon name="chevron-right" class="size-3 text-zinc-400 shrink-0 transition-transform duration-150" x-bind:class="openGroup === 'anim' ? 'rotate-90' : ''" />
                                                            <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Animation</span>
                                                        </button>
                                                        <div x-show="openGroup === 'anim'" x-collapse class="mt-2 pl-3">
                                                            <div id="editor-row-animation-grid-{{ $index }}" class="grid grid-cols-2 gap-2">
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
                                                <div>
                                                    <button type="button" @click="openGroup = openGroup === 'bg' ? null : 'bg'" class="flex items-center gap-1.5 w-full text-left">
                                                        <flux:icon name="chevron-right" class="size-3 text-zinc-400 shrink-0 transition-transform duration-150" x-bind:class="openGroup === 'bg' ? 'rotate-90' : ''" />
                                                        <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Background Options</span>
                                                    </button>
                                                    <div x-show="openGroup === 'bg'" x-collapse class="mt-2 space-y-2 pl-3">
                                                        @foreach (['section_bg_position' => 'Position', 'section_bg_size' => 'Size', 'section_bg_repeat' => 'Repeat'] as $bgKey => $bgLabel)
                                                            @if (isset($rowDesignDefaults[$row['slug']][$bgKey]))
                                                                <div id="editor-row-bg-{{ $bgKey }}-{{ $index }}">
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
                                                <div>
                                                    <button type="button" @click="openGroup = openGroup === 'classes' ? null : 'classes'" class="flex items-center gap-1.5 w-full text-left">
                                                        <flux:icon name="chevron-right" class="size-3 text-zinc-400 shrink-0 transition-transform duration-150" x-bind:class="openGroup === 'classes' ? 'rotate-90' : ''" />
                                                        <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">Classes</span>
                                                    </button>
                                                    <div x-show="openGroup === 'classes'" x-collapse class="mt-2 pl-3 space-y-3">
                                                @foreach (['section_classes' => 'Section Classes', 'section_container_classes' => 'Container Classes'] as $fieldKey => $fieldLabel)
                                                    @if (isset($rowDesignDefaults[$row['slug']][$fieldKey]))
                                                        @php $rcKey = $row['slug'] . '_' . $fieldKey; @endphp
                                                        <div id="editor-row-classes-field-{{ $fieldKey }}-{{ $index }}">
                                                            <div id="editor-row-classes-header-{{ $fieldKey }}-{{ $index }}" class="flex items-center justify-between mb-1.5">
                                                                <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-500 dark:text-zinc-400">{{ $fieldLabel }}</span>
                                                                <div id="editor-row-classes-actions-{{ $fieldKey }}-{{ $index }}" class="flex items-center gap-2">
                                                                    <div id="editor-row-token-picker-{{ $fieldKey }}-{{ $index }}" class="relative" x-data="{
                                                                        open: false,
                                                                        fieldKey: @js($rcKey),
                                                                        insert(cls) {
                                                                            const ta = document.querySelector('[data-row-classes-key=\'' + this.fieldKey + '\']');
                                                                            if (ta) {
                                                                                const v = ta.value.trimEnd();
                                                                                ta.value = v ? v + ' ' + cls : cls;
                                                                                ta.dispatchEvent(new Event('input', { bubbles: true }));
                                                                                ta.focus();
                                                                            }
                                                                            this.open = false;
                                                                        }
                                                                    }">
                                                                        <button type="button"
                                                                            @click="open = !open"
                                                                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                                                                            title="Insert theme token"
                                                                        ><flux:icon name="bolt" class="size-3.5" /></button>
                                                                        <div
                                                                            x-show="open"
                                                                            @click.outside="open = false"
                                                                            x-transition:enter="transition ease-out duration-75"
                                                                            x-transition:enter-start="opacity-0 scale-95"
                                                                            x-transition:enter-end="opacity-100 scale-100"
                                                                            class="absolute right-0 top-full mt-1 z-50 w-56 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg overflow-hidden text-left"
                                                                        >
                                                                            @if (! empty($rcThemeColorNames))
                                                                                <div id="editor-token-colors-{{ $index }}" class="px-3 pt-2.5 pb-2">
                                                                                    <p class="text-[10px] uppercase tracking-wider font-semibold text-zinc-400 dark:text-zinc-500 mb-1.5">Colors</p>
                                                                                    <div id="editor-token-colors-swatches-{{ $index }}" class="flex flex-wrap gap-1">
                                                                                        @foreach ($rcThemeColorNames as $rcColorName)
                                                                                            <button type="button" @click="insert('bg-{{ $rcColorName }}')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">bg-{{ $rcColorName }}</button>
                                                                                            <button type="button" @click="insert('text-{{ $rcColorName }}')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">text-{{ $rcColorName }}</button>
                                                                                            <button type="button" @click="insert('border-{{ $rcColorName }}')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">border-{{ $rcColorName }}</button>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </div>
                                                                                <div id="editor-token-colors-spacing-divider-{{ $index }}" class="border-t border-zinc-100 dark:border-zinc-700/60"></div>
                                                                            @endif
                                                                            <div id="editor-token-spacing-{{ $index }}" class="px-3 pt-2 pb-2">
                                                                                <p class="text-[10px] uppercase tracking-wider font-semibold text-zinc-400 dark:text-zinc-500 mb-1.5">Spacing</p>
                                                                                <div id="editor-token-spacing-tokens-{{ $index }}" class="flex flex-wrap gap-1">
                                                                                    <button type="button" @click="insert('py-section')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">py-section</button>
                                                                                    <button type="button" @click="insert('py-section-banner')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">py-section-banner</button>
                                                                                    <button type="button" @click="insert('py-section-hero')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">py-section-hero</button>
                                                                                </div>
                                                                            </div>
                                                                            <div id="editor-token-spacing-utilities-divider-{{ $index }}" class="border-t border-zinc-100 dark:border-zinc-700/60"></div>
                                                                            <div id="editor-token-utilities-{{ $index }}" class="px-3 pt-2 pb-2.5">
                                                                                <p class="text-[10px] uppercase tracking-wider font-semibold text-zinc-400 dark:text-zinc-500 mb-1.5">Utilities</p>
                                                                                <div id="editor-token-utilities-tokens-{{ $index }}" class="flex flex-wrap gap-1">
                                                                                    <button type="button" @click="insert('rounded-card')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">rounded-card</button>
                                                                                    <button type="button" @click="insert('shadow-card')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">shadow-card</button>
                                                                                    <button type="button" @click="insert('font-heading')" class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-primary hover:text-white transition-colors">font-heading</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @if ($rcAiEnabled)
                                                                        <button type="button"
                                                                            onclick="(function() { var ta = document.querySelector('[data-row-classes-key=\'{{ $rcKey }}\']'); window.dispatchEvent(new CustomEvent('open-ai-generate', { detail: { fieldKey: '{{ $rcKey }}', fieldType: 'classes', fieldLabel: '{{ addslashes($fieldLabel) }}', currentClasses: ta ? ta.value : '' }, bubbles: true })); })()"
                                                                            class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                                                                            title="Generate with AI"
                                                                        ><flux:icon name="sparkles" class="size-3.5" /></button>
                                                                    @endif
                                                                    <button wire:click="resetRowDesignField('{{ $row['slug'] }}', '{{ $fieldKey }}')" type="button" class="text-xs text-zinc-400 dark:text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">Reset</button>
                                                                </div>
                                                            </div>
                                                            <div x-data="twAutocomplete('{{ $row['slug'] }}_{{ $fieldKey }}')" class="relative"
                                                                x-on:ai-content-generated.window="
                                                                    if ($event.detail.fieldKey === '{{ $rcKey }}') {
                                                                        $refs.input.value = $event.detail.content;
                                                                        $refs.input.dispatchEvent(new Event('input', { bubbles: true }));
                                                                        $refs.input.focus();
                                                                    }
                                                                ">
                                                                <textarea
                                                                    x-ref="input"
                                                                    data-row-classes-key="{{ $rcKey }}"
                                                                    wire:model.live.debounce.400ms="rowDesignValues.{{ $row['slug'] }}.{{ $fieldKey }}"
                                                                    rows="2"
                                                                    x-on:input="suggest($event)"
                                                                    x-on:keydown="handleKey($event)"
                                                                    x-on:blur="delayClose()"
                                                                    @mousedown.stop
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
                                                                <div id="editor-tw-autocomplete-hint" class="flex items-center gap-1.5 mt-1">
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
                                            <div id="editor-row-panel-advanced-{{ $index }}" x-show="panelMode === 'advanced'" class="p-3 space-y-3">
                                                @if (isset($rowDesignDefaults[$row['slug']]['section_id']))
                                                    <div id="editor-row-section-id-field-{{ $index }}">
                                                        <div id="editor-row-section-id-header-{{ $index }}" class="flex items-center justify-between mb-1.5">
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
                                                    <flux:icon name="finger-print" class="size-3.5" />
                                                    Auto BEM IDs
                                                </button>
                                            </div>
                                            {{-- Browse mode info --}}
                                            <div id="editor-row-panel-browse-{{ $index }}" x-show="panelMode === 'browse'" class="px-3 py-2 border-t border-zinc-200 dark:border-zinc-700">
                                                @php $bd = $rowBrowseData[$row['slug']] ?? null; @endphp
                                                @if ($bd)
                                                    <div id="editor-row-browse-info-{{ $index }}" class="flex items-center gap-2">
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
                                    <div id="editor-row-actions-{{ $index }}" class="relative flex items-center px-2 py-2">
                                        {{-- Standard action bar --}}
                                        <div x-show="panelMode !== 'browse'" class="contents">
                                            <div id="editor-row-primary-actions-{{ $index }}" class="flex items-center gap-0.5">
                                                <flux:button
                                                    wire:click="moveRowUp({{ $index }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="arrow-up"
                                                    :disabled="$index === 0"
                                                    :class="$index === 0 ? 'opacity-15!' : ''"
                                                    tooltip="Move up"
                                                    :loading="false"
                                                />
                                                <flux:button
                                                    wire:click="moveRowDown({{ $index }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="arrow-down"
                                                    :disabled="$index === count($rows) - 1"
                                                    :class="$index === count($rows) - 1 ? 'opacity-15!' : ''"
                                                    tooltip="Move down"
                                                    :loading="false"
                                                />

                                                <flux:tooltip content="Drag to reorder">
                                                    <flux:icon name="bars-2" class="size-4 text-zinc-400 dark:text-zinc-500 cursor-grab active:cursor-grabbing mx-2" />
                                                </flux:tooltip>

                                            </div>
                                            <div id="editor-row-secondary-actions-{{ $index }}" class="flex items-center gap-0.5 ml-auto">
                                                <flux:button
                                                    wire:click="openLibraryDrawer({{ $index }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    tooltip="Insert section above"
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
                                                    tooltip="Insert section below"
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
                                                        x-on:click="$wire.set('pendingMakeSharedRowIndex', {{ $index }}); $flux.modal('confirm-make-shared-row').show()"
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="share"
                                                        tooltip="Make shared"
                                                        class="opacity-60 hover:opacity-100"
                                                    />
                                                @else
                                                    <flux:tooltip content="Shared row">
                                                        <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-primary cursor-default">
                                                            <flux:icon name="share" variant="mini" class="size-5" />
                                                        </button>
                                                    </flux:tooltip>
                                                @endif
                                                <flux:button
                                                    x-on:click="$wire.set('pendingRemoveRowIndex', {{ $index }}); $flux.modal('confirm-remove-row').show()"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="trash"
                                                    class="opacity-60 hover:opacity-100"
                                                    tooltip="Remove section"
                                                    :loading="false"
                                                />
                                            </div>
                                        </div>

                                        {{-- Browse mode action bar --}}
                                        <div id="editor-row-browse-actions-{{ $index }}" x-show="panelMode === 'browse'" class="flex items-center gap-2 w-full">
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
                                                <div id="editor-row-browse-selector-{{ $index }}" class="flex items-center gap-1 flex-1 min-w-0">
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
                                <div id="editor-row-list-empty" class="text-center py-8 text-zinc-400 dark:text-zinc-500">
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
                <div id="editor-preview-panel" class="flex-1 flex flex-col bg-white dark:bg-zinc-950 overflow-auto order-first">
@if ($previewUrl)
                        <div
                            id="editor-preview-container"
                            class="flex-1 flex flex-col mx-auto w-full transition-all duration-300"
                            :style="previewWidth ? 'max-width: ' + previewWidth : 'max-width: 100%'"
                        >
                            <div
                                id="editor-preview-frame-wrapper"
                                class="flex-1 relative"
                                x-init="document.getElementById('page-preview-a').src = {{ Js::from($previewUrl) }}"
                                x-on:refresh-preview.window="refreshPreview($event.detail.url)"
                            >
                                <iframe wire:ignore id="page-preview-a" class="absolute inset-0 w-full h-full border-0" style="opacity:1;z-index:2;transition:opacity 0.15s ease"></iframe>
                                <iframe wire:ignore id="page-preview-b" class="absolute inset-0 w-full h-full border-0" style="opacity:0;z-index:1;transition:opacity 0.15s ease"></iframe>
                            </div>
                        </div>
                    @else
                        <div id="editor-preview-unavailable" class="flex-1 flex items-center justify-center text-zinc-400 dark:text-zinc-600">
                            <div id="editor-preview-unavailable-inner" class="text-center">
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
    <flux:modal wire:model="showSeoModal" class="w-full max-w-2xl">
        <flux:heading size="lg">Page Settings</flux:heading>

        <div id="editor-seo-modal-sections" class="mt-6 space-y-4">
            {{-- Basic --}}
            <div x-data="{ basicOpen: false }" x-on:open-settings-section.window="basicOpen = ($event.detail === 'basic')">
                <button
                    type="button"
                    @click="$dispatch('open-settings-section', basicOpen ? null : 'basic')"
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

                <div id="editor-seo-basic-content" x-show="basicOpen" x-transition class="mt-3 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-4">
                    {{-- Page Name --}}
                    @if (preg_match('#^pages/(?!dashboard/).*⚡[^/]+\.blade\.php$#u', $file))
                        <flux:input
                            label="Page Name"
                            wire:model="pageName"
                            placeholder="About Us"
                            description="Shown in the page title banner row. Separate from the SEO title — can be shorter or longer."
                        />
                    @endif

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
                            <div id="editor-seo-slug-redirect-box" class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 space-y-3">
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
                </div>
            </div>

            {{-- SEO --}}
            <div
                x-data="{ seoOpen: false, aiOpen: false, aiPrompt: '', aiGenerating: false, aiError: '' }"
                x-on:open-settings-section.window="seoOpen = ($event.detail === 'seo')"
                x-on:seo-ai-complete.window="
                    aiGenerating = false; aiOpen = false; aiPrompt = '';
                    if ($event.detail.error) { aiError = $event.detail.error; aiOpen = true; return; }
                    if ($event.detail.title) {
                        let inp = $el.querySelector('input[name=seoTitle]');
                        if (inp) { inp.value = $event.detail.title; inp.dispatchEvent(new Event('change', {bubbles: true})); }
                    }
                    if ($event.detail.description) {
                        let ta = $el.querySelector('textarea[name=seoDescription]');
                        if (ta) { ta.value = $event.detail.description; ta.dispatchEvent(new Event('change', {bubbles: true})); }
                    }
                "
                x-on:ai-generate-error.window="if ($event.detail.fieldKey === 'seo') { aiError = $event.detail.message; aiGenerating = false; }"
                class="pt-4 border-t border-zinc-200 dark:border-zinc-700"
            >
                <div id="editor-seo-section-header" class="w-full flex items-center justify-between">
                    <button
                        type="button"
                        @click="$dispatch('open-settings-section', seoOpen ? null : 'seo')"
                        class="flex-1 text-left"
                    >
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-200">SEO</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Page title, meta description, indexing, and social sharing.</p>
                    </button>
                    <div id="editor-seo-header-actions" class="flex items-center gap-2 shrink-0 ml-3">
                        @if (\App\Models\Setting::get('ai.claude_key') || \App\Models\Setting::get('ai.openai_key'))
                            <button type="button"
                                @click="seoOpen = true; aiOpen = !aiOpen"
                                class="text-zinc-400 dark:text-zinc-500 hover:text-primary dark:hover:text-primary transition-colors"
                                title="Generate SEO metadata with AI"
                            ><flux:icon name="sparkles" class="size-3.5" /></button>
                        @endif
                        <button type="button" @click="$dispatch('open-settings-section', seoOpen ? null : 'seo')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-600 dark:text-zinc-300 transition-transform duration-200" :class="seoOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="editor-seo-content" x-show="seoOpen" x-transition class="mt-3 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-4">
                    {{-- Inline AI prompt --}}
                    <div id="editor-seo-ai-prompt" x-show="aiOpen" x-transition class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-3 space-y-2">
                        <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Generate SEO with AI</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">Page content is read automatically. Optionally add any extra context, keywords, or tone guidance.</p>
                        <textarea
                            x-model="aiPrompt"
                            rows="2"
                            placeholder="Optional — e.g. focus on emergency plumbing, target homeowners in Austin TX"
                            @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); if (!aiGenerating) { aiGenerating = true; aiError = ''; $wire.generateAiContent('seo', aiPrompt, 'seo'); } }"
                            :disabled="aiGenerating"
                            class="w-full text-xs rounded border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary transition resize-none disabled:opacity-60"
                        ></textarea>
                        <div x-show="aiError" x-transition class="text-xs text-red-600 dark:text-red-400" x-text="aiError"></div>
                        <div id="editor-seo-ai-prompt-actions" class="flex items-center justify-end gap-2">
                            <button type="button" @click="aiOpen = false; aiPrompt = ''; aiError = ''" class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">Cancel</button>
                            <button type="button"
                                @click="if (!aiGenerating) { aiGenerating = true; aiError = ''; $wire.generateAiContent('seo', aiPrompt, 'seo'); }"
                                :disabled="aiGenerating"
                                class="flex items-center gap-1.5 px-3 py-1 bg-primary text-white text-xs font-medium rounded hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <svg x-show="aiGenerating" class="size-3 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <flux:icon x-show="!aiGenerating" name="sparkles" class="size-3" />
                                <span x-text="aiGenerating ? 'Generating…' : 'Generate'"></span>
                            </button>
                        </div>
                    </div>
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
                    @click="$dispatch('open-settings-section', designOpen ? null : 'design')"
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

                <div id="editor-seo-design-content" x-show="designOpen" x-transition class="mt-3 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-4">
                </div>
            </div>

            {{-- Advanced --}}
            @if (preg_match('#^pages/(?!dashboard/).*⚡[^/]+\.blade\.php$#u', $file))
            <div x-data="{ advancedOpen: false }" x-on:open-settings-section.window="advancedOpen = ($event.detail === 'advanced')" class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <button
                    type="button"
                    @click="$dispatch('open-settings-section', advancedOpen ? null : 'advanced')"
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

                <div id="editor-seo-advanced-content" x-show="advancedOpen" x-transition class="mt-3 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-4">
                    {{-- Cache --}}
                    <flux:switch
                        label="Cache response"
                        :description="'Full-page cache this page for ' . \Carbon\CarbonInterval::seconds(config('responsecache.cache_lifetime_in_seconds'))->cascade()->forHumans() . ' for unauthenticated visitors. Disable for pages with dynamic or user-specific content.'"
                        wire:model="isCachedPage"
                    />

                    {{-- Login Required --}}
                    <div id="editor-seo-login-required" x-data>
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
                    @click="$dispatch('open-settings-section', redirectOpen ? null : 'redirect')"
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

                <div id="editor-seo-redirect-content" x-show="redirectOpen" x-transition class="mt-3 pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-3">
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

        <div id="editor-seo-modal-footer" class="mt-6 flex justify-end gap-2">
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

        <div id="editor-tw-help-grid" class="grid grid-cols-3 gap-6 text-sm">
            {{-- Column 1: Responsive & Variants --}}
            <div id="editor-tw-help-col-1" class="space-y-5">
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
            <div id="editor-tw-help-col-2" class="space-y-5">
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
            <div id="editor-tw-help-col-3" class="space-y-5">
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

        <div id="editor-tw-help-footer" class="mt-6 flex justify-end">
            <flux:modal.close>
                <flux:button>Close</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    {{-- Accessibility Audit modal --}}
    <flux:modal wire:model="showAccessibilityModal" class="w-full max-w-2xl">
        <div id="editor-a11y-modal-header" class="flex items-start justify-between gap-4">
            <div>
                <flux:heading size="lg">Accessibility Audit</flux:heading>
                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    @if ($accessibilityScannedSaveCount < 0)
                        Never scanned.
                    @else
                        @php $savesAgo = $accessibilitySaveCount - $accessibilityScannedSaveCount; @endphp
                        @if ($savesAgo === 0)
                            Scanned just now.
                        @elseif ($savesAgo === 1)
                            Scanned 1 save ago.
                        @else
                            Scanned {{ $savesAgo }} saves ago.
                        @endif
                    @endif
                </flux:text>
            </div>
        </div>

        <div id="editor-a11y-modal-body" class="mt-6">
            @if ($accessibilityScannedSaveCount < 0)
                <div id="editor-a11y-never-scanned" class="flex flex-col items-center justify-center py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-zinc-300 dark:text-zinc-600 mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.955 11.955 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">No scan run yet</p>
                    <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">Click "Run Scan" to check this page for accessibility issues.</p>
                </div>
            @elseif (empty($accessibilityIssues))
                <div id="editor-a11y-no-issues" class="flex flex-col items-center justify-center py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-green-400 dark:text-green-500 mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.955 11.955 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    <p class="text-sm font-medium text-green-700 dark:text-green-400">No issues found</p>
                    <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">Great work — this page passed all accessibility checks.</p>
                </div>
            @else
                @php
                    $errorCount = count(array_filter($accessibilityIssues, fn($i) => $i['severity'] === 'error'));
                    $warningCount = count(array_filter($accessibilityIssues, fn($i) => $i['severity'] === 'warning'));
                @endphp
                <div id="editor-a11y-issue-summary" class="flex items-center gap-3 mb-4">
                    @if ($errorCount > 0)
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                            {{ $errorCount }} {{ Str::plural('error', $errorCount) }}
                        </span>
                    @endif
                    @if ($warningCount > 0)
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                            {{ $warningCount }} {{ Str::plural('warning', $warningCount) }}
                        </span>
                    @endif
                </div>

                <div id="editor-a11y-issue-list" class="space-y-2">
                    @foreach ($accessibilityIssues as $issue)
                        @php $isNavigable = ! empty($issue['row_slug']); @endphp
                        <div
                            class="flex items-start gap-3 p-3 rounded-lg transition-opacity {{ $issue['severity'] === 'error' ? 'bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30' : 'bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30' }} {{ $isNavigable ? 'cursor-pointer hover:opacity-75' : '' }}"
                            @if ($isNavigable)
                                wire:click="navigateToIssue('{{ $issue['row_slug'] }}', '{{ $issue['field_key'] }}', '{{ $issue['group'] }}')"
                            @endif
                        >
                            @if ($issue['severity'] === 'error')
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-red-500 dark:text-red-400 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-amber-500 dark:text-amber-400 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                            @endif
                            <div id="editor-a11y-issue-text-{{ $loop->index }}" class="flex-1 min-w-0">
                                <p class="text-xs font-medium {{ $issue['severity'] === 'error' ? 'text-red-700 dark:text-red-400' : 'text-amber-700 dark:text-amber-400' }}">{{ $issue['message'] }}</p>
                                <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
                                    Row: {{ $issue['row'] }}
                                    @if ($isNavigable)
                                        <span class="ml-1 text-zinc-300 dark:text-zinc-600">— click to fix</span>
                                    @endif
                                </p>
                            </div>
                            @if ($isNavigable)
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 shrink-0 mt-0.5 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div id="editor-a11y-modal-footer" class="mt-6 flex items-start justify-between gap-4">
            <p class="text-xs text-zinc-400 dark:text-zinc-500 leading-relaxed max-w-sm">
                Checks: missing alt text, heading hierarchy (H1 count, skipped levels), and empty link labels.
            </p>
            <div id="editor-a11y-modal-actions" class="flex items-center gap-2 shrink-0">
                <flux:button wire:click="runAccessibilityAudit" icon="shield-check" variant="outline">
                    {{ $accessibilityScannedSaveCount < 0 ? 'Run Scan' : 'Re-scan' }}
                </flux:button>
                <flux:modal.close>
                    <flux:button>Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="confirm-remove-all-rows" class="w-full max-w-sm">
        <flux:heading size="lg">Remove all rows?</flux:heading>
        <flux:text class="mt-2">This will remove all rows from this page. Any saved content for these rows will also be deleted.</flux:text>
        <div id="editor-confirm-remove-all-footer" class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:modal.close>
                <flux:button variant="danger" wire:click="removeAllRows">Remove All</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <flux:modal name="generate-all-content" class="w-full max-w-lg">
        <div
            x-data="{
                prompt: '',
                mode: 'all',
                useHtml: true,
                generateImages: false,
                running: false,
                cancelled: false,
                total: 0,
                done: 0,
                rowCount: 0,
                errors: [],
                get pct() {
                    return this.total ? Math.round((this.done / this.total) * 100) : 0;
                },
                handleOpen(detail) {
                    this.rowCount = detail.rowCount || 0;
                    this.prompt = '';
                    this.mode = 'all';
                    this.useHtml = true;
                    this.generateImages = false;
                    this.running = false;
                    this.cancelled = false;
                    this.total = 0;
                    this.done = 0;
                    this.errors = [];
                },
                async start() {
                    if (!this.prompt.trim()) return;
                    this.running = true;
                    this.cancelled = false;
                    this.total = this.rowCount;
                    this.done = 0;
                    this.errors = [];
                    const overwriteAll = this.mode === 'all';
                    const useHtml = this.useHtml;
                    const generateImages = this.generateImages;
                    const allImageTasks = [];
                    for (let idx = 0; idx < this.rowCount; idx++) {
                        if (this.cancelled) break;
                        const result = await $wire.generateAiAllRowText(idx, this.prompt, overwriteAll, useHtml);
                        if (result.error) {
                            this.errors.push(result.error);
                        }
                        if (generateImages && result.imageFields && result.imageFields.length) {
                            this.total += result.imageFields.length;
                            for (const fieldKey of result.imageFields) {
                                allImageTasks.push({ rowSlug: result.rowSlug, fieldKey: fieldKey });
                            }
                        }
                        this.done++;
                    }
                    for (const task of allImageTasks) {
                        if (this.cancelled) break;
                        const imgResult = await $wire.generateAiAllRowImage(task.rowSlug, task.fieldKey, this.prompt);
                        if (imgResult.error) {
                            this.errors.push(imgResult.error);
                        }
                        this.done++;
                    }
                    if (!this.cancelled) { await $wire.triggerPreviewRefresh(); }
                    this.running = false;
                    $flux.modal('generate-all-content').close();
                    const msg = this.errors.length ? 'Content generated with ' + this.errors.length + ' error(s).' : 'Content generated for all sections.';
                    this.$dispatch('notify', { message: msg });
                },
                cancel() { this.cancelled = true; }
            }"
            x-on:open-generate-all-modal.window="handleOpen($event.detail)"
        >
            <flux:heading size="lg">Generate content for all sections</flux:heading>
            <flux:text class="mt-1">Describe what the page is about and AI will write content for every text field across all sections at once.</flux:text>

            <div id="editor-generate-all-form" class="mt-4 space-y-4" x-show="!running">
                <div>
                    <flux:label>Prompt</flux:label>
                    <textarea
                        x-model="prompt"
                        placeholder="e.g. A plumbing company in Austin TX specializing in emergency repairs and residential plumbing. Family owned, 24/7 service, licensed and insured."
                        rows="4"
                        class="mt-1 w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none placeholder-zinc-400"
                    ></textarea>
                    <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">Describe the business, page purpose, and tone. AI will use this context to write each section appropriately.</p>
                </div>

                <div id="editor-generate-mode-selector">
                    <flux:label class="mb-2">What to generate</flux:label>
                    <div id="editor-generate-mode-options" class="mt-2 space-y-2.5">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="radio" x-model="mode" value="all" class="mt-0.5 text-primary shrink-0">
                            <div>
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Overwrite all</span>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">Replace every text field, including fields that already have content</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="radio" x-model="mode" value="empty" class="mt-0.5 text-primary shrink-0">
                            <div>
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Fill empty only</span>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">Skip fields that already have content saved</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="editor-generate-options" class="space-y-3 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <div>
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Use HTML in rich text fields</span>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500">Generate &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;li&gt; tags where supported</p>
                        </div>
                        <button
                            type="button"
                            x-on:click="useHtml = !useHtml"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                            x-bind:class="useHtml ? 'bg-primary' : 'bg-zinc-200 dark:bg-zinc-600'"
                        >
                            <span class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform duration-200 ease-in-out" x-bind:class="useHtml ? 'translate-x-4' : 'translate-x-0'"></span>
                        </button>
                    </label>
                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <div>
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Generate images</span>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500">Use DALL-E 3 to fill empty image fields (requires OpenAI key, adds ~15s per image)</p>
                        </div>
                        <button
                            type="button"
                            x-on:click="generateImages = !generateImages"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                            x-bind:class="generateImages ? 'bg-primary' : 'bg-zinc-200 dark:bg-zinc-600'"
                        >
                            <span class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform duration-200 ease-in-out" x-bind:class="generateImages ? 'translate-x-4' : 'translate-x-0'"></span>
                        </button>
                    </label>
                </div>
            </div>

            <div id="editor-generate-all-progress" x-show="running" class="mt-4 space-y-3">
                <div id="editor-generate-all-progress-header" class="flex items-center justify-between text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400">Generating content…</span>
                    <span class="font-medium text-zinc-700 dark:text-zinc-300" x-text="total ? (done + ' / ' + total + ' steps') : 'Preparing…'"></span>
                </div>
                <div id="editor-generate-all-progress-bar-track" class="h-2 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-primary rounded-full transition-all duration-300 ease-out"
                        x-bind:style="'width: ' + (total ? pct : 0) + '%'"
                        x-bind:class="total === 0 ? 'animate-pulse w-full opacity-30' : ''"
                    ></div>
                </div>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">Processing each section — this may take a moment.</p>
                <div id="editor-generate-all-errors" x-show="errors.length" class="rounded-lg bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 px-3 py-2">
                    <p class="text-xs font-medium text-red-700 dark:text-red-400 mb-1">Some sections had errors:</p>
                    <template x-for="err in errors">
                        <p class="text-xs text-red-600 dark:text-red-400" x-text="err"></p>
                    </template>
                </div>
            </div>

            <div id="editor-generate-all-footer" class="mt-6 flex justify-end gap-3">
                <template x-if="!running">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                </template>
                <template x-if="running">
                    <flux:button variant="ghost" x-on:click="cancel()">Stop</flux:button>
                </template>
                <flux:button
                    variant="primary"
                    x-show="!running"
                    x-bind:disabled="!prompt.trim()"
                    x-on:click="start()"
                >
                    <flux:icon name="sparkles" class="size-4" />
                    Generate
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="confirm-auto-bem" class="w-full max-w-sm">
        <flux:heading size="lg">Auto BEM all rows?</flux:heading>
        <flux:text class="mt-2">Auto-generate BEM IDs for all rows on this page. Existing IDs will be updated.</flux:text>
        <div id="editor-confirm-auto-bem-footer" class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:modal.close>
                <flux:button variant="primary" wire:click="applyAutoBemAllRows">Apply</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    @php
        $tlModalLangs = array_values(array_filter(
            \App\Models\Setting::get('site.languages', [['code' => 'en']]),
            fn ($l) => $l['code'] !== 'en'
        ));
    @endphp
    <flux:modal name="translate-options" class="w-full max-w-md">
        <div
            data-langs='{!! json_encode($tlModalLangs, JSON_HEX_APOS) !!}'
            x-data="{
                rowIndex: null,
                rowCount: 0,
                targetLang: '',
                mode: 'all',
                running: false,
                cancelled: false,
                total: 0,
                done: 0,
                langs: [],
                init() {
                    this.langs = JSON.parse(this.$el.dataset.langs || '[]');
                },
                get pct() {
                    return this.total ? Math.round((this.done / this.total) * 100) : 0;
                },
                get langLabel() {
                    const tl = this.targetLang;
                    const l = this.langs.find(function(x) { return x.code === tl; });
                    return l ? (l.flag + ' ' + l.label) : tl;
                },
                handleOpen(detail) {
                    this.rowIndex = detail.rowIndex ?? null;
                    this.rowCount = detail.rowCount ?? 0;
                    this.targetLang = detail.lang || (this.langs.length === 1 ? this.langs[0].code : '');
                    this.mode = 'all';
                    this.running = false;
                    this.cancelled = false;
                    this.total = 0;
                    this.done = 0;
                },
                async start() {
                    if (!this.targetLang) return;
                    this.running = true;
                    this.cancelled = false;
                    this.total = 0;
                    this.done = 0;
                    const skipFilled = this.mode === 'empty';
                    const indices = this.rowIndex !== null
                        ? [this.rowIndex]
                        : [...Array(this.rowCount).keys()];
                    for (const idx of indices) {
                        if (this.cancelled) break;
                        const fields = await $wire.prepareTranslation(idx, this.targetLang, skipFilled);
                        this.total += fields.length;
                        for (const fieldKey of fields) {
                            if (this.cancelled) break;
                            await $wire.translateNextField(fieldKey, this.targetLang);
                            this.done++;
                        }
                    }
                    if (!this.cancelled) { await $wire.triggerPreviewRefresh(); }
                    const msg = this.done
                        ? ('Translated ' + this.done + ' field(s).')
                        : 'No translatable content found.';
                    this.running = false;
                    $flux.modal('translate-options').close();
                    this.$dispatch('notify', { message: msg });
                },
                cancel() { this.cancelled = true; }
            }"
            x-on:open-translate-modal.window="handleOpen($event.detail)"
        >
            <flux:heading size="lg" x-text="rowIndex !== null ? 'Translate section' : 'Translate all sections'"></flux:heading>
            <flux:text class="mt-1" x-text="rowIndex !== null ? 'Translate all text fields in this section.' : 'Translate all text fields across every section on this page.'"></flux:text>

            <div id="editor-translate-form" class="mt-4 space-y-4" x-show="!running">
                <div id="editor-translate-lang-selector" x-show="rowIndex === null">
                    <flux:label>Translate to</flux:label>
                    <select x-model="targetLang" class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Select language…</option>
                        @foreach ($tlModalLangs as $tl)
                            <option value="{{ $tl['code'] }}">{{ $tl['flag'] }} {{ $tl['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="editor-translate-single-row-indicator" x-show="rowIndex !== null">
                    <flux:text class="text-sm">Translating to: <span class="font-medium text-zinc-800 dark:text-zinc-200" x-text="langLabel"></span></flux:text>
                </div>
                <div id="editor-translate-mode-selector">
                    <flux:label class="mb-2">What to translate</flux:label>
                    <div id="editor-translate-mode-options" class="mt-2 space-y-2.5">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="radio" x-model="mode" value="all" class="mt-0.5 text-primary shrink-0">
                            <div>
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Overwrite all</span>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">Translate every field, replacing any existing translations</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="radio" x-model="mode" value="empty" class="mt-0.5 text-primary shrink-0">
                            <div>
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Fill empty only</span>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">Skip fields that already have a translation</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div id="editor-translate-progress" x-show="running" class="mt-4 space-y-3">
                <div id="editor-translate-progress-header" class="flex items-center justify-between text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400">Translating…</span>
                    <span class="font-medium text-zinc-700 dark:text-zinc-300" x-text="total > 0 ? `${done} / ${total} fields` : 'Preparing…'"></span>
                </div>
                <div id="editor-translate-progress-bar-track" class="h-2 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-primary rounded-full transition-all duration-300 ease-out"
                        x-bind:style="`width: ${total > 0 ? pct : 0}%`"
                        x-bind:class="total === 0 ? 'animate-pulse w-full opacity-30' : ''"
                    ></div>
                </div>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">This may take a moment — each field is translated individually.</p>
            </div>

            <div id="editor-translate-footer" class="mt-6 flex justify-end gap-3">
                <template x-if="!running">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                </template>
                <template x-if="running">
                    <flux:button variant="ghost" @click="cancel()">Stop</flux:button>
                </template>
                <flux:button
                    variant="primary"
                    x-show="!running"
                    x-bind:disabled="!targetLang"
                    @click="start()"
                >Translate</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="confirm-make-shared-row" class="w-full max-w-sm">
        <flux:heading size="lg">Make row shared?</flux:heading>
        <flux:text class="mt-2">This row will be available to insert on other pages. Changes to it will affect all pages using it.</flux:text>
        <div id="editor-confirm-make-shared-footer" class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:modal.close>
                <flux:button variant="primary" wire:click="makeRowShared($wire.pendingMakeSharedRowIndex)">Make Shared</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <flux:modal name="confirm-remove-row" class="w-full max-w-sm">
        <flux:heading size="lg">Remove row?</flux:heading>
        <flux:text class="mt-2">This will remove the row from the page. Any saved content for this row will also be deleted.</flux:text>
        <div id="editor-confirm-remove-row-footer" class="mt-6 flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:modal.close>
                <flux:button variant="danger" wire:click="removeRow($wire.pendingRemoveRowIndex)">Remove</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

</div>
