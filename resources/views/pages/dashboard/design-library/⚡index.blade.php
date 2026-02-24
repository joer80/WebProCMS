<?php

use App\Enums\PageCategory;
use App\Enums\RowCategory;
use App\Jobs\IndexDesignLibraryJob;
use App\Models\DesignPage;
use App\Models\DesignRow;
use App\Support\DesignLibraryService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Design Library')] class extends Component {
    #[Url]
    public string $tab = 'rows';

    #[Url]
    public string $category = '';

    public bool $showModal = false;
    public ?int $editingId = null;
    public bool $indexing = false;

    // Row form fields
    public string $formName = '';
    public string $formCategory = '';
    public string $formDescription = '';
    public string $formBladeCode = '';
    public string $formPhpCode = '';

    // Confirm delete
    public ?int $confirmingDelete = null;

    /** @return \Illuminate\Database\Eloquent\Collection<int, DesignRow> */
    #[Computed]
    public function rows(): \Illuminate\Database\Eloquent\Collection
    {
        return DesignRow::query()
            ->when($this->category, fn ($q) => $q->where('category', $this->category))
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, DesignPage> */
    #[Computed]
    public function pages(): \Illuminate\Database\Eloquent\Collection
    {
        return DesignPage::query()
            ->when($this->category, fn ($q) => $q->where('website_category', $this->category))
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
        return collect(PageCategory::cases())
            ->mapWithKeys(fn (PageCategory $c) => [$c->value => $c->label()])
            ->all();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->category = '';
    }

    public function syncLibrary(): void
    {
        $this->indexing = true;
        IndexDesignLibraryJob::dispatchSync();
        $this->indexing = false;

        unset($this->rows, $this->pages);

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
            $this->formBladeCode = $row->blade_code;
            $this->formPhpCode = $row->php_code ?? '';
        } else {
            $page = DesignPage::query()->findOrFail($id);
            $this->formName = $page->name;
            $this->formCategory = $page->website_category->value;
            $this->formDescription = $page->description ?? '';
            $this->formBladeCode = $page->blade_code;
            $this->formPhpCode = $page->php_code ?? '';
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'formName' => ['required', 'string', 'max:255'],
            'formCategory' => ['required', 'string'],
            'formBladeCode' => ['required', 'string'],
        ]);

        $data = [
            'name' => $this->formName,
            'description' => $this->formDescription ?: null,
            'blade_code' => $this->formBladeCode,
            'php_code' => $this->formPhpCode ?: null,
        ];

        $service = new DesignLibraryService;

        if ($this->tab === 'rows') {
            $data['category'] = $this->formCategory;

            if ($this->editingId) {
                $row = DesignRow::query()->findOrFail($this->editingId);
                $row->update($data);

                // Write back to source file if it exists
                if ($row->source_file) {
                    $fullPath = $service->fullPath($row->source_file);
                    if (file_exists($fullPath)) {
                        $service->writeTemplateFile($fullPath, array_merge($data, ['sort_order' => $row->sort_order]));
                    }
                }

                $message = 'Row updated.';
            } else {
                $slug = $this->formCategory.'-'.now()->format('YmdHis');
                $data['source_file'] = 'rows/'.$this->formCategory.'/'.$slug.'.blade.php';
                $data['sort_order'] = 0;
                $row = DesignRow::query()->create($data);

                if (app()->isLocal()) {
                    $service->writeTemplateFile($service->fullPath($row->source_file), $data);
                }

                $message = 'Row created.';
            }

            unset($this->rows);
        } else {
            $data['website_category'] = $this->formCategory;

            if ($this->editingId) {
                $page = DesignPage::query()->findOrFail($this->editingId);
                $page->update($data);

                if ($page->source_file) {
                    $fullPath = $service->fullPath($page->source_file);
                    if (file_exists($fullPath)) {
                        $service->writeTemplateFile($fullPath, array_merge($data, ['sort_order' => $page->sort_order]));
                    }
                }

                $message = 'Page template updated.';
            } else {
                $slug = $this->formCategory.'-'.now()->format('YmdHis');
                $data['source_file'] = 'pages/'.$this->formCategory.'/'.$slug.'.blade.php';
                $data['sort_order'] = 0;
                $page = DesignPage::query()->create($data);

                if (app()->isLocal()) {
                    $service->writeTemplateFile($service->fullPath($page->source_file), $data);
                }

                $message = 'Page template created.';
            }

            unset($this->pages);
        }

        $this->showModal = false;
        $this->resetForm();

        $this->dispatch('notify', message: $message);
    }

    public function deleteItem(int $id): void
    {
        if ($this->tab === 'rows') {
            DesignRow::query()->findOrFail($id)->delete();
            unset($this->rows);
        } else {
            DesignPage::query()->findOrFail($id)->delete();
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
    }
}; ?>

<div>
    {{-- Edit / Create Modal --}}
    <flux:modal wire:model="showModal" class="w-full max-w-3xl">
        <flux:heading size="lg" class="mb-6">
            {{ $editingId ? __('Edit') : __('New') }} {{ $tab === 'rows' ? __('Row') : __('Page Template') }}
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
                <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                    {{ $tab === 'rows' ? __('Add Row') : __('Add Page') }}
                </flux:button>
            </div>
        </div>

        <div class="flex gap-8">
            {{-- Category sidebar --}}
            <aside class="hidden lg:block w-48 shrink-0">
                <div class="sticky top-20">
                    <flux:heading size="sm" class="mb-3 text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-xs">
                        {{ $tab === 'rows' ? __('Categories') : __('Website Type') }}
                    </flux:heading>
                    <nav class="space-y-1">
                        <button
                            wire:click="$set('category', '')"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors {{ $category === '' ? 'bg-primary/10 text-primary font-medium' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                        >
                            {{ __('All') }}
                        </button>
                        @foreach ($tab === 'rows' ? $this->rowCategories : $this->pageCategories as $value => $label)
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
                            {{ $this->rows->count() }}
                        </span>
                    </button>
                    <button
                        wire:click="setTab('pages')"
                        class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px {{ $tab === 'pages' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                    >
                        {{ __('Page Library') }}
                        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ $tab === 'pages' ? 'bg-primary/10 text-primary' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400' }}">
                            {{ $this->pages->count() }}
                        </span>
                    </button>
                </div>

                @if ($tab === 'rows')
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
                                    {{-- Code preview --}}
                                    <div class="bg-zinc-950 text-zinc-300 p-3 font-mono text-[10px] leading-relaxed overflow-hidden h-28 relative">
                                        <span class="opacity-60">{{ Str::limit(strip_tags($row->blade_code), 180) }}</span>
                                        <div class="absolute inset-x-0 bottom-0 h-8 bg-gradient-to-t from-zinc-950"></div>
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
                    @endif
                @else
                    @if ($this->pages->isEmpty())
                        <div class="text-center py-20 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
                            <flux:icon name="document-text" class="size-12 mx-auto mb-3 text-zinc-300 dark:text-zinc-600" />
                            <flux:heading class="text-zinc-500">No page templates yet</flux:heading>
                            <flux:text class="mt-1 text-sm">Add page templates manually or sync from your library files.</flux:text>
                            <div class="mt-6 flex justify-center gap-3">
                                <flux:button wire:click="syncLibrary" variant="outline" size="sm" icon="arrow-path">Sync Library</flux:button>
                                <flux:button wire:click="openCreateModal" variant="primary" size="sm" icon="plus">Add Page Template</flux:button>
                            </div>
                        </div>
                    @else
                        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach ($this->pages as $page)
                                <div wire:key="page-{{ $page->id }}" class="group rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden hover:border-primary/40 transition-colors flex flex-col">
                                    <div class="bg-zinc-950 text-zinc-300 p-3 font-mono text-[10px] leading-relaxed overflow-hidden h-28 relative">
                                        <span class="opacity-60">{{ Str::limit(strip_tags($page->blade_code), 180) }}</span>
                                        <div class="absolute inset-x-0 bottom-0 h-8 bg-gradient-to-t from-zinc-950"></div>
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
                    @endif
                @endif
            </div>
        </div>
    </flux:main>
</div>
