<?php

use App\Enums\RowCategory;
use App\Jobs\IndexDesignLibraryJob;
use App\Models\ContentOverride;
use App\Models\DesignRow;
use App\Support\VoltFileService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.editor')] #[Title('Page Editor')] class extends Component {
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

    // Content editor state
    public bool $showContentEditor = false;
    public ?int $editingRowIndex = null;

    /** @var array<int, array{slug: string, key: string, type: string, default: string, label: string}> */
    public array $contentFields = [];

    /** @var array<string, string|bool> */
    public array $contentValues = [];

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
    }

    /** @return array<string, array<string, string>> */
    #[Computed]
    public function voltFiles(): array
    {
        return (new VoltFileService)->listVoltFiles();
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

        $this->contentValues = [];

        foreach ($this->contentFields as $field) {
            $dbKey = $field['slug'].':'.$field['key'];
            $rawValue = $overrides->get($dbKey)?->value;
            $this->contentValues[$field['key']] = $field['type'] === 'toggle'
                ? ($rawValue !== null ? $rawValue === '1' : $field['default'] === '1')
                : ($rawValue ?? '');
        }

        $this->showContentEditor = true;
    }

    public function saveContentOverrides(): void
    {
        foreach ($this->contentFields as $field) {
            $raw = $this->contentValues[$field['key']] ?? '';

            if ($field['type'] === 'toggle') {
                // Always persist toggles so explicit off ('0') isn't confused with the default.
                ContentOverride::updateOrCreate(
                    ['row_slug' => $field['slug'], 'key' => $field['key']],
                    ['type' => $field['type'], 'value' => $raw ? '1' : '0']
                );
                continue;
            }

            $value = (string) $raw;

            if ($value === '') {
                ContentOverride::query()
                    ->where('row_slug', $field['slug'])
                    ->where('key', $field['key'])
                    ->delete();
            } else {
                ContentOverride::updateOrCreate(
                    ['row_slug' => $field['slug'], 'key' => $field['key']],
                    ['type' => $field['type'], 'value' => $value]
                );
            }
        }

        $this->showContentEditor = false;
        $this->dispatch('notify', message: 'Content saved.');
        $this->refreshPreview();
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

    private function refreshPreview(): void
    {
        try {
            (new VoltFileService)->writePreviewFile($this->phpSection, $this->rows, $this->file);
        } catch (\Throwable) {
            // Preview write failed; continue without updating the iframe.
        }

        $this->dispatch('refresh-preview');
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

    {{-- Content Editor Drawer --}}
    <flux:modal wire:model="showContentEditor" class="w-full max-w-lg">
        @if ($editingRowIndex !== null && isset($rows[$editingRowIndex]))
            <flux:heading size="lg" class="mb-1">{{ __('Edit Content') }}</flux:heading>
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">{{ $rows[$editingRowIndex]['name'] }}</flux:text>

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
                                    wire:model="contentValues.{{ $field['key'] }}"
                                    rows="5"
                                    placeholder="{{ $field['default'] }}"
                                />
                                <flux:text class="text-xs text-zinc-400 mt-1">HTML is supported.</flux:text>
                            @elseif ($field['type'] === 'toggle')
                                <flux:checkbox
                                    wire:model="contentValues.{{ $field['key'] }}"
                                    label="Yes"
                                />
                            @elseif (str_ends_with($field['key'], '_url'))
                                <flux:input
                                    wire:model="contentValues.{{ $field['key'] }}"
                                    type="url"
                                    placeholder="{{ $field['default'] ?: 'https://' }}"
                                />
                            @else
                                <flux:input
                                    wire:model="contentValues.{{ $field['key'] }}"
                                    placeholder="{{ $field['default'] }}"
                                />
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <flux:button wire:click="$set('showContentEditor', false)" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button wire:click="saveContentOverrides" variant="primary" icon="check">
                        {{ __('Save Content') }}
                    </flux:button>
                </div>
            @endif
        @endif
    </flux:modal>

    <div class="flex flex-col min-h-screen bg-white dark:bg-zinc-900">
        {{-- Editor toolbar --}}
        <div class="sticky top-0 z-30 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 px-6 py-3 flex flex-wrap items-center gap-3">
            <a href="{{ route('dashboard.pages') }}" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 mr-1" wire:navigate>
                <flux:icon name="arrow-left" class="size-5" />
            </a>

            <flux:heading>{{ __('Page Editor') }}</flux:heading>

            <div class="flex-1 min-w-0 max-w-xs">
                <flux:select wire:model.live="file" placeholder="Select a page to edit…">
                    <flux:select.option value="">{{ __('Select a page…') }}</flux:select.option>
                    @foreach ($this->voltFiles as $group => $files)
                        <optgroup label="{{ $group }}">
                            @foreach ($files as $label => $path)
                                <flux:select.option value="{{ $path }}">{{ $label }}</flux:select.option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </flux:select>
            </div>

            <div class="flex items-center gap-2 ml-auto">
                @if ($isDirty)
                    <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">Unsaved changes</span>
                @endif

                @if ($file)
                    @if ($liveUrl)
                        <a href="{{ $liveUrl }}" target="_blank">
                            <flux:button variant="outline" size="sm" icon="arrow-top-right-on-square">{{ __('View Live') }}</flux:button>
                        </a>
                    @endif
                    <flux:button wire:click="saveFile" variant="primary" size="sm" icon="check">
                        {{ __('Save') }}
                    </flux:button>
                    <flux:button wire:click="discardChanges" variant="ghost" size="sm">
                        {{ __('Discard') }}
                    </flux:button>
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
                {{-- Left panel: row list --}}
                <div class="w-80 shrink-0 border-r border-zinc-200 dark:border-zinc-700 overflow-y-auto flex flex-col">
                    <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                        <flux:heading size="sm" class="text-zinc-600 dark:text-zinc-400">{{ __('Page Rows') }}</flux:heading>
                        <flux:text class="text-xs mt-1 text-zinc-400 dark:text-zinc-500">{{ count($rows) }} {{ Str::plural('row', count($rows)) }}</flux:text>
                    </div>

                    <div class="flex-1 p-3 space-y-2">
                        @forelse ($rows as $index => $row)
                            <div
                                wire:key="row-item-{{ $row['slug'] }}"
                                class="group rounded-lg border bg-white dark:bg-zinc-900 p-3 transition-colors {{ $editingRowIndex === $index ? 'border-primary' : 'border-zinc-200 dark:border-zinc-700' }}"
                            >
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $row['name'] }}</div>
                                        <div class="text-[10px] font-mono text-zinc-400 dark:text-zinc-500 truncate mt-0.5">{{ $row['slug'] }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <flux:button
                                        wire:click="moveRowUp({{ $index }})"
                                        variant="ghost"
                                        size="sm"
                                        icon="arrow-up"
                                        :disabled="$index === 0"
                                        title="Move up"
                                    />
                                    <flux:button
                                        wire:click="moveRowDown({{ $index }})"
                                        variant="ghost"
                                        size="sm"
                                        icon="arrow-down"
                                        :disabled="$index === count($rows) - 1"
                                        title="Move down"
                                    />
                                    <flux:button
                                        wire:click="openContentEditor({{ $index }})"
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil-square"
                                        title="Edit content"
                                        class="ml-auto"
                                    />
                                    <flux:button
                                        wire:click="openLibraryDrawer({{ $index }})"
                                        variant="ghost"
                                        size="sm"
                                        icon="plus"
                                        title="Insert row before this"
                                    />
                                    <flux:button
                                        wire:click="removeRow({{ $index }})"
                                        wire:confirm="Remove this row from the page?"
                                        variant="ghost"
                                        size="sm"
                                        icon="trash"
                                        class="text-red-500 dark:text-red-400"
                                        title="Remove row"
                                    />
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

                {{-- Right panel: iframe preview --}}
                <div class="flex-1 flex flex-col bg-zinc-100 dark:bg-zinc-950">
                    <div class="flex items-center justify-between px-4 py-2 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                        <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 font-mono truncate">
                            {{ $liveUrl ?: __('Preview will appear after loading a page') }}
                            @if ($isDirty)
                                <span class="text-amber-500">(unsaved)</span>
                            @endif
                        </flux:text>
                    </div>

                    @if ($previewUrl)
                        <iframe
                            wire:ignore
                            id="page-preview"
                            class="flex-1 w-full border-0"
                            x-init="$el.src = {{ Js::from($previewUrl) }}"
                            x-on:refresh-preview.window="$el.src = $el.src.split('?')[0] + '?_=' + Date.now()"
                        ></iframe>
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
</div>
