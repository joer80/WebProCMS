<?php

use App\Enums\RowCategory;
use App\Jobs\IndexDesignLibraryJob;
use App\Models\ContentOverride;
use App\Models\DesignRow;
use App\Models\MediaItem;
use App\Models\SharedRow;
use App\Support\VoltFileService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\ResponseCache\Facades\ResponseCache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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

    public bool $requiresLogin = false;

    public string $requiredRole = '';

    #[Validate('required|in:draft,published,unlisted,unpublished')]
    public string $pageStatus = 'published';

    // Content editor state
    public bool $showContentEditor = false;

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

    /** @var array<string, array<string, string>> */
    public array $rowDesignValues = [];

    /** @var array<string, array<string, string>> */
    public array $rowDesignDefaults = [];

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
        $drafts[$slug.':'.$fieldKey] = ['type' => 'classes', 'value' => $storeValue];
        session(['editor_draft_overrides' => $drafts]);

        $this->isDirty = true;
        $this->refreshPreview();
    }

    public function resetRowDesignField(string $slug, string $fieldKey): void
    {
        $default = $this->rowDesignDefaults[$slug][$fieldKey] ?? '';
        $this->rowDesignValues[$slug][$fieldKey] = $default;

        $drafts = session('editor_draft_overrides', []);
        $drafts[$slug.':'.$fieldKey] = ['type' => 'classes', 'value' => ''];
        session(['editor_draft_overrides' => $drafts]);

        $this->isDirty = true;
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

    /** @return \Illuminate\Database\Eloquent\Collection<int, SharedRow> */
    #[Computed]
    public function sharedLibraryRows(): \Illuminate\Database\Eloquent\Collection
    {
        return SharedRow::query()->orderBy('name')->get();
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
        $this->loadRowDesignValues();
        $this->isDirty = false;
        $this->parseSeoFromPhpSection();
        $this->pageSlug = preg_match('#^pages/⚡([^/]+)\.blade\.php$#u', $relativePath, $m) ? $m[1] : '';
        $this->originalPageSlug = $this->pageSlug;
        $this->createSlugRedirect = false;
        $this->slugRedirectType = '301';

        if ($this->pageSlug) {
            $this->requiresLogin = $service->isAuthRoute($this->pageSlug);

            if ($this->requiresLogin) {
                $this->isCachedPage = $service->isAuthRouteCached($this->pageSlug);
                $this->requiredRole = $service->getRouteAuthRole($this->pageSlug);
            } else {
                $this->isCachedPage = $service->isRouteCached($this->pageSlug);
                $this->requiredRole = '';
            }
        } else {
            $this->requiresLogin = false;
            $this->isCachedPage = true;
            $this->requiredRole = '';
        }
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

    public function toggleRowVisibility(int $index): void
    {
        $this->rows[$index]['hidden'] = empty($this->rows[$index]['hidden']) ? true : false;
        $this->isDirty = true;

        $this->refreshPreview();
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
        $this->loadRowDesignValues();
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

        $slug = basename($designRow->source_file, '.blade.php').':'.Str::random(6);
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
        $this->loadRowDesignValues();
        $this->isDirty = true;
        $this->showLibraryDrawer = false;

        $this->refreshPreview();
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
            fn ($f) => ! in_array($f['key'], ['section_classes', 'section_container_classes'], true)
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

            if ($value === '') {
                ContentOverride::query()
                    ->where('row_slug', $slug)
                    ->where('key', $key)
                    ->delete();
            } else {
                ContentOverride::updateOrCreate(
                    ['row_slug' => $slug, 'key' => $key],
                    ['type' => $type, 'value' => $value, 'page_slug' => isset($sharedSlugs[$slug]) ? null : ($this->pageSlug ?: null)]
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
        $this->showMediaPicker = true;
    }

    #[On('media-image-picked')]
    public function handleMediaImagePicked(string $key, string $path, string $alt = ''): void
    {
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

        $service = new VoltFileService;
        $fullPath = resource_path('views/'.$this->file);
        $service->writeFile($fullPath, $service->buildFileContent($this->phpSection, $this->rows));

        if ($this->liveUrl) {
            ResponseCache::forget($this->liveUrl);
        }

        $this->isDirty = false;
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

    private function loadRowDesignValues(): void
    {
        $this->rowDesignDefaults = [];

        foreach ($this->rows as $row) {
            $fields = $this->parseContentFields($row['blade'], $row['slug']);

            foreach ($fields as $field) {
                if (in_array($field['key'], ['section_classes', 'section_container_classes'], true)) {
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
            ->whereIn('key', ['section_classes', 'section_container_classes'])
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
    }

    /**
     * Parse editable field definitions for a row from its @schema block.
     *
     * @return array<int, array{slug: string, key: string, type: string, default: string, label: string, group: string}>
     */
    private function parseContentFields(string $blade, string $slug): array
    {
        $templateName = explode(':', $slug, 2)[0];
        $schemaFields = app(\App\Support\SchemaCache::class)->getFieldsForRow($templateName);

        return array_map(fn ($field) => array_merge($field, ['slug' => $slug]), $schemaFields);
    }

    public function saveSeoSettings(): void
    {
        $isPublicPage = (bool) preg_match('#^pages/⚡[^/]+\.blade\.php$#u', $this->file);

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

        // Remove any legacy page-auth PHP block — auth is now handled at the route level.
        $this->phpSection = $service->removePhpCode($this->phpSection, 'page-auth');
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
                <flux:select.option value="shared">{{ __('Shared Rows') }}</flux:select.option>
            </flux:select>
        </div>

        @if ($libraryCategory === 'shared')
            @if ($this->sharedLibraryRows->isEmpty())
                <div class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="share" class="size-10 mx-auto mb-3 opacity-40" />
                    <p class="text-sm">No shared rows yet.</p>
                    <p class="text-xs mt-1">Use the "Make Shared" action on any row to share it.</p>
                </div>
            @else
                <div class="space-y-2 max-h-96 overflow-y-auto">
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
        @elseif ($this->libraryRows->isEmpty())
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
            activePreview: 'a',
            setWidth(w) { this.previewWidth = this.previewWidth === w ? null : w; },
            selectRowBySlug(slug) {
                const rows = $wire.rows;
                const index = rows.findIndex(r => r.slug === slug);
                if (index !== -1) {
                    $wire.openContentEditor(index);
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
        @message.window="
            if ($event.data && $event.data.editorRowSlug) { selectRowBySlug($event.data.editorRowSlug); }
            else if ($event.origin === window.location.origin && $event.data && $event.data.type === 'editor-save-page' && $wire.file) { $wire.saveFile(); }
        "
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
                <div
                    class="w-96 shrink-0 order-last border-l border-zinc-200 dark:border-zinc-700 flex flex-col"
                    x-data="{ editorOpen: false, designMode: false, allGroupsOpen: true }"
                    x-on:content-editor-opened.window="editorOpen = true; designMode = false"
                    x-on:content-editor-closed.window="editorOpen = false"
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
                            <button
                                type="button"
                                @click="allGroupsOpen = !allGroupsOpen; $dispatch('set-group-open', { value: allGroupsOpen })"
                                :title="allGroupsOpen ? 'Collapse all' : 'Expand all'"
                                class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors shrink-0"
                            >
                                <flux:icon x-show="allGroupsOpen" name="chevron-up" class="size-4" />
                                <flux:icon x-show="!allGroupsOpen" name="chevron-down" class="size-4" />
                            </button>
                            <div class="flex rounded-md border border-zinc-200 dark:border-zinc-700 text-[11px] font-medium overflow-hidden shrink-0">
                                <button type="button" @click="designMode = false; $wire.resetEmptyClassesFields(); $dispatch('set-group-design-mode', { value: false })" :class="!designMode ? 'bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'" class="px-2.5 py-1 transition-colors">Content</button>
                                <button type="button" @click="designMode = true; $wire.resetEmptyClassesFields(); $dispatch('set-group-design-mode', { value: true })" :class="designMode ? 'bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-white text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'" class="px-2.5 py-1 transition-colors border-l border-zinc-200 dark:border-zinc-700">Design</button>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto p-4">
                            @if (! empty($rows[$editingRowIndex]['shared'] ?? false))
                                <div class="mb-3 flex items-start gap-2 px-3 py-2 rounded-lg bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 text-xs text-blue-700 dark:text-blue-300">
                                    <flux:icon name="share" class="size-3.5 mt-0.5 shrink-0" />
                                    <span>Shared row — changes affect all pages using it.</span>
                                </div>
                            @endif
                            @if (empty($contentFields))
                                <div class="text-center py-8 text-zinc-400 dark:text-zinc-500">
                                    <flux:icon name="pencil" class="size-10 mx-auto mb-2 opacity-40" />
                                    <p class="text-sm">This row has no editable content fields.</p>
                                </div>
                            @else
                                @php
                                    $fieldGroups = collect($contentFields)->groupBy('group');
                                    $showGroupHeaders = $fieldGroups->count() > 1;
                                @endphp
                                <div class="{{ $showGroupHeaders ? 'space-y-4' : 'space-y-5' }}">
                                    @foreach ($fieldGroups as $groupKey => $groupFields)
                                        @if ($showGroupHeaders)
                                            @php
                                                // Detect a group-level toggle: a show_ toggle whose prefix matches all other field keys in the group.
                                                $groupShowField = $groupFields->first(fn ($f) => $f['type'] === 'toggle' && str_starts_with($f['key'], 'toggle_'));
                                                $headerToggleField = null;
                                                if ($groupShowField) {
                                                    $showPrefix = str_replace('toggle_', '', $groupShowField['key']);
                                                    $otherFields = $groupFields->reject(fn ($f) => $f['key'] === $groupShowField['key']);
                                                    $isGroupToggle = $otherFields->every(fn ($f) => str_ends_with($f['key'], '_new_tab') || str_contains($f['key'], $showPrefix));
                                                    if ($isGroupToggle) {
                                                        $headerToggleField = $groupShowField;
                                                    }
                                                }
                                                $bodyFields = $headerToggleField
                                                    ? $groupFields->reject(fn ($f) => $f['key'] === $headerToggleField['key'])
                                                    : $groupFields;
                                                $groupHasClassesFields = $bodyFields->contains(fn ($f) => $f['type'] === 'classes');
                                                $groupAllClasses = $bodyFields->every(fn ($f) => $f['type'] === 'classes');
                                            @endphp
                                            <div x-data="{ open: true, groupDesignMode: {{ $groupAllClasses ? 'true' : 'false' }}, groupContentMode: false, groupHasClasses: {{ $groupHasClassesFields ? 'true' : 'false' }} }" @set-group-design-mode.window="groupDesignMode = $event.detail.value; groupContentMode = false" @set-group-open.window="open = $event.detail.value" x-show="designMode ? {{ $groupHasClassesFields ? 'true' : 'false' }} : true" class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                                                <div class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700/50">
                                                    <button
                                                        type="button"
                                                        @click="open = !open"
                                                        class="flex-1 min-w-0 text-left text-xs uppercase tracking-wider font-semibold text-zinc-600 dark:text-zinc-300"
                                                    >{{ ucwords($groupKey) }}</button>
                                                    @if ($groupHasClassesFields)
                                                        <button
                                                            type="button"
                                                            x-show="!designMode"
                                                            @click="groupDesignMode = !groupDesignMode; $wire.resetEmptyClassesFields()"
                                                            :class="groupDesignMode ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                                            class="transition-colors"
                                                            title="Toggle design mode for this group"
                                                        >
                                                            <flux:icon x-show="!groupDesignMode" name="paint-brush" class="size-3.5" />
                                                            <flux:icon x-show="groupDesignMode" name="document-text" class="size-3.5" />
                                                        </button>
                                                        <button
                                                            type="button"
                                                            x-show="designMode"
                                                            @click="groupContentMode = !groupContentMode"
                                                            :class="groupContentMode ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                                            class="transition-colors"
                                                            title="Switch to content mode for this group"
                                                        >
                                                            <flux:icon name="document-text" class="size-3.5" />
                                                        </button>
                                                    @endif
                                                    @if ($headerToggleField)
                                                        <flux:switch wire:model.live="contentValues.{{ $headerToggleField['key'] }}" />
                                                    @endif
                                                </div>
                                                <div x-show="open" x-collapse class="border-t border-zinc-200 dark:border-zinc-700 p-3 space-y-4">
                                                    @foreach ($bodyFields as $field)
                                                        @include('pages.dashboard.pages.partials.content-field', ['field' => $field])
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            @foreach ($groupFields as $field)
                                                @include('pages.dashboard.pages.partials.content-field', ['field' => $field])
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            @endif
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
                                    data-row-sidebar-index="{{ $index }}"
                                    x-data="{ designOpen: false }"
                                    class="rounded-lg border bg-white dark:bg-zinc-900 overflow-hidden transition-colors {{ !empty($row['hidden']) ? 'opacity-60' : '' }}"
                                    :class="editorOpen && {{ $editingRowIndex ?? -1 }} === {{ $index }} ? 'border-primary' : (designOpen ? 'border-primary/50' : 'border-zinc-200 dark:border-zinc-700')"
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
                                    {{-- Row header: clickable name area + paintbrush + visibility toggle --}}
                                    <div class="flex items-center gap-2 px-3 py-2 bg-zinc-100 dark:bg-zinc-700/50">
                                        <button
                                            wire:click="openContentEditor({{ $index }})"
                                            class="flex-1 min-w-0 text-left hover:opacity-75 transition-opacity"
                                        >
                                            <div class="flex items-center gap-1.5">
                                                <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $row['name'] }}</div>
                                                @if (! empty($row['shared']))
                                                    <flux:badge size="sm" color="blue" class="shrink-0">Shared</flux:badge>
                                                @endif
                                            </div>
                                            <div class="text-[10px] font-mono text-zinc-400 dark:text-zinc-500 truncate mt-0.5">{{ $row['slug'] }}</div>
                                        </button>
                                        @if (isset($rowDesignDefaults[$row['slug']]))
                                            <button
                                                type="button"
                                                @click.stop="designOpen = !designOpen"
                                                :class="designOpen ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                                class="transition-colors shrink-0"
                                                title="Edit section styles"
                                            >
                                                <flux:icon name="paint-brush" class="size-3.5" />
                                            </button>
                                        @endif
                                        <flux:switch
                                            :checked="empty($row['hidden'])"
                                            @click.stop="$wire.toggleRowVisibility({{ $index }})"
                                            title="{{ !empty($row['hidden']) ? 'Row hidden — click to show' : 'Click to hide row' }}"
                                        />
                                    </div>

                                    {{-- Inline design panel --}}
                                    @if (isset($rowDesignDefaults[$row['slug']]))
                                        <div x-show="designOpen" x-collapse class="border-t border-zinc-200 dark:border-zinc-700 p-3 space-y-3">
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
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

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
                        :description="'Full-page cache this page for ' . \Carbon\CarbonInterval::seconds(config('responsecache.cache_lifetime_in_seconds'))->cascade()->forHumans() . ' for unauthenticated visitors. Disable for pages with dynamic or user-specific content.'"
                        wire:model="isCachedPage"
                    />
                </div>

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

    {{-- Media Library Picker --}}
    <flux:modal wire:model="showMediaPicker" name="media-picker" class="max-w-3xl! p-0!">
        @if ($showMediaPicker)
            <livewire:pages::dashboard.media-library.picker
                :field-key="$mediaPickerKey"
                :key="'media-picker-'.$mediaPickerKey"
            />
        @endif
    </flux:modal>
</div>
