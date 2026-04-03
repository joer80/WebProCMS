<?php

use App\Models\Setting;
use App\Services\MenuService;
use Illuminate\Support\Facades\Route as RoutesFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\ResponseCache\Facades\ResponseCache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Menus')] class extends Component {
    /**
     * @var array<int, array{slug: string, label: string, items: array<int, array<string, mixed>>}>
     */
    public array $menus = [];

    /** @var list<string> */
    public array $footerSlugs = [];

    public int $activeMenuIndex = 0;

    public bool $showAddModal = false;
    public string $addType = 'page';
    public string $newPageRoute = '';
    public string $newPageLabel = '';
    public string $newCustomUrl = '';
    public string $newCustomLabel = '';
    public bool $newCustomNewWindow = false;
    public string $newDynamicSource = '';
    public bool $newDynamicShowAll = false;
    public string $newDynamicShowAllLabel = '';

    public bool $showCreateMenuModal = false;
    public string $newMenuLabel = '';
    public string $newMenuSlug = '';

    public bool $showEditMenuModal = false;

    public int $editItemIndex = -1;
    public string $editItemType = 'page';
    public string $editItemLabel = '';
    public string $editItemRoute = '';
    public string $editItemUrl = '';
    public bool $editItemNewWindow = false;
    public string $editDynamicSource = '';
    public bool $editDynamicShowAll = false;
    public string $editDynamicShowAllLabel = '';

    /** @var list<array{heading: string, links: list<array{icon: string, title: string, desc: string, url: string, new_tab: bool}>}> */
    public array $newMegaColumns = [];

    /** @var list<array{heading: string, links: list<array{icon: string, title: string, desc: string, url: string, new_tab: bool}>}> */
    public array $editMegaColumns = [];

    public function mount(): void
    {
        $this->menus = Setting::get('navigation.menus', []);
    }

    /** @return array<string, string> */
    #[Computed]
    public function availableRoutes(): array
    {
        $skip = ['logout', 'login', 'register', 'design-library.preview', 'blog.show', 'services.content-editor'];
        $skipPrefixes = ['dashboard.', 'profile.', 'password.', 'two-factor.', 'verification.', 'sanctum.'];

        return collect(RoutesFacade::getRoutes()->getRoutesByName())
            ->keys()
            ->filter(function (string $name) use ($skip, $skipPrefixes): bool {
                if (in_array($name, $skip, true)) {
                    return false;
                }

                foreach ($skipPrefixes as $prefix) {
                    if (str_starts_with($name, $prefix)) {
                        return false;
                    }
                }

                return true;
            })
            ->sort()
            ->mapWithKeys(fn (string $name): array => [
                $name => ucwords(str_replace(['.', '-', '_'], ' ', preg_replace('/\.index$/', '', $name))),
            ])
            ->all();
    }

    /** @return array<string, string> */
    #[Computed]
    public function availableSources(): array
    {
        return MenuService::availableSources();
    }

    public function openAddModal(): void
    {
        $this->resetAddForm();
        $this->showAddModal = true;
    }

    public function updatedNewPageRoute(string $value): void
    {
        if ($value !== '' && $this->newPageLabel === '') {
            $this->newPageLabel = ucwords(str_replace(['.', '-', '_'], ' ', preg_replace('/\.index$/', '', $value)));
        }
    }

    public function updatedNewMenuLabel(string $value): void
    {
        $this->newMenuSlug = Str::slug($value);
    }

    public function addItem(): void
    {
        if ($this->addType === 'page') {
            $this->validate([
                'newPageRoute' => ['required', 'string'],
                'newPageLabel' => ['required', 'string', 'max:255'],
            ]);

            $item = [
                'label' => $this->newPageLabel,
                'route' => $this->newPageRoute,
                'active' => true,
            ];
        } elseif ($this->addType === 'dynamic') {
            $this->validate([
                'newDynamicSource' => ['required', 'string'],
                'newPageLabel' => ['required', 'string', 'max:255'],
            ]);

            $item = [
                'label'  => $this->newPageLabel,
                'type'   => 'dynamic',
                'source' => $this->newDynamicSource,
                'active' => true,
            ];

            if ($this->newDynamicShowAll) {
                $item['show_all'] = true;
                $item['show_all_label'] = $this->newDynamicShowAllLabel ?: 'See All';
            }
        } elseif ($this->addType === 'mega') {
            $this->validate([
                'newPageLabel' => ['required', 'string', 'max:255'],
            ]);

            $item = [
                'label'   => $this->newPageLabel,
                'type'    => 'mega',
                'active'  => true,
                'columns' => $this->newMegaColumns,
            ];
        } else {
            $this->validate([
                'newCustomLabel' => ['required', 'string', 'max:255'],
                'newCustomUrl' => ['required', 'url', 'max:2048'],
            ]);

            $item = [
                'label' => $this->newCustomLabel,
                'url' => $this->newCustomUrl,
                'active' => true,
            ];

            if ($this->newCustomNewWindow) {
                $item['new_window'] = true;
            }
        }

        $this->menus[$this->activeMenuIndex]['items'][] = $item;
        $this->showAddModal = false;
        $this->resetAddForm();
    }

    public function createMenu(): void
    {
        $existingSlugs = array_column($this->menus, 'slug');

        $this->validate([
            'newMenuLabel' => ['required', 'string', 'max:255'],
            'newMenuSlug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::notIn($existingSlugs)],
        ]);

        $this->menus[] = [
            'slug' => $this->newMenuSlug,
            'label' => $this->newMenuLabel,
            'items' => [],
        ];

        $this->activeMenuIndex = count($this->menus) - 1;
        $this->newMenuLabel = '';
        $this->newMenuSlug = '';
        $this->showCreateMenuModal = false;
    }

    public function deleteMenu(): void
    {
        array_splice($this->menus, $this->activeMenuIndex, 1);
        $this->menus = array_values($this->menus);
        $this->activeMenuIndex = max(0, $this->activeMenuIndex - 1);
    }

    public function removeItem(int $index): void
    {
        array_splice($this->menus[$this->activeMenuIndex]['items'], $index, 1);
    }

    public function openEditItemModal(int $index): void
    {
        if ($this->editItemIndex === $index) {
            $this->editItemIndex = -1;

            return;
        }

        $item = $this->menus[$this->activeMenuIndex]['items'][$index];
        $this->editItemIndex = $index;

        if (($item['type'] ?? null) === 'dynamic') {
            $this->editItemType = 'dynamic';
            $this->editDynamicSource = $item['source'] ?? '';
            $this->editDynamicShowAll = ! empty($item['show_all']);
            $this->editDynamicShowAllLabel = $item['show_all_label'] ?? '';
            $this->editMegaColumns = [];
        } elseif (($item['type'] ?? null) === 'mega') {
            $this->editItemType = 'mega';
            $this->editMegaColumns = $item['columns'] ?? [];
            $this->editDynamicSource = '';
            $this->editDynamicShowAll = false;
            $this->editDynamicShowAllLabel = '';
        } else {
            $this->editItemType = isset($item['route']) ? 'page' : 'custom';
            $this->editDynamicSource = '';
            $this->editDynamicShowAll = false;
            $this->editDynamicShowAllLabel = '';
            $this->editMegaColumns = [];
        }

        $this->editItemLabel = $item['label'] ?? '';
        $this->editItemRoute = $item['route'] ?? '';
        $this->editItemUrl = $item['url'] ?? '';
        $this->editItemNewWindow = $item['new_window'] ?? false;
    }

    public function saveEditItem(): void
    {
        $existing = $this->menus[$this->activeMenuIndex]['items'][$this->editItemIndex];
        $active = $existing['active'] ?? true;
        $depth = $existing['depth'] ?? 0;

        if ($this->editItemType === 'page') {
            $this->validate([
                'editItemRoute' => ['required', 'string'],
                'editItemLabel' => ['required', 'string', 'max:255'],
            ]);

            $item = [
                'label'  => $this->editItemLabel,
                'route'  => $this->editItemRoute,
                'active' => $active,
                'depth'  => $depth,
            ];
        } elseif ($this->editItemType === 'dynamic') {
            $this->validate([
                'editDynamicSource' => ['required', 'string'],
                'editItemLabel' => ['required', 'string', 'max:255'],
            ]);

            $item = [
                'label'  => $this->editItemLabel,
                'type'   => 'dynamic',
                'source' => $this->editDynamicSource,
                'active' => $active,
                'depth'  => 0,
            ];

            if ($this->editDynamicShowAll) {
                $item['show_all'] = true;
                $item['show_all_label'] = $this->editDynamicShowAllLabel ?: 'See All';
            }
        } elseif ($this->editItemType === 'mega') {
            $this->validate([
                'editItemLabel' => ['required', 'string', 'max:255'],
            ]);

            $item = [
                'label'   => $this->editItemLabel,
                'type'    => 'mega',
                'active'  => $active,
                'columns' => $this->editMegaColumns,
                'depth'   => 0,
            ];
        } else {
            $this->validate([
                'editItemLabel' => ['required', 'string', 'max:255'],
                'editItemUrl'   => ['required', 'url', 'max:2048'],
            ]);

            $item = [
                'label'  => $this->editItemLabel,
                'url'    => $this->editItemUrl,
                'active' => $active,
                'depth'  => $depth,
            ];

            if ($this->editItemNewWindow) {
                $item['new_window'] = true;
            }
        }

        $this->menus[$this->activeMenuIndex]['items'][$this->editItemIndex] = $item;
        $this->editItemIndex = -1;
    }

    public function indentItem(int $index): void
    {
        $items = $this->menus[$this->activeMenuIndex]['items'];

        if ($index <= 0) {
            return;
        }

        $item = $items[$index];

        // Already indented, or mega/dynamic items cannot be sub-items
        if (($item['depth'] ?? 0) >= 1 || in_array($item['type'] ?? null, ['mega', 'dynamic'], true)) {
            return;
        }

        // Previous item must be a plain top-level item (not mega/dynamic — those manage their own children)
        $prev = $items[$index - 1];

        if (($prev['depth'] ?? 0) > 0 || in_array($prev['type'] ?? null, ['mega', 'dynamic'], true)) {
            return;
        }

        $this->menus[$this->activeMenuIndex]['items'][$index]['depth'] = 1;
    }

    public function outdentItem(int $index): void
    {
        if (($this->menus[$this->activeMenuIndex]['items'][$index]['depth'] ?? 0) <= 0) {
            return;
        }

        $this->menus[$this->activeMenuIndex]['items'][$index]['depth'] = 0;
    }

    public function moveItemUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        $items = $this->menus[$this->activeMenuIndex]['items'];
        [$items[$index - 1], $items[$index]] = [$items[$index], $items[$index - 1]];
        $this->menus[$this->activeMenuIndex]['items'] = array_values($items);
    }

    public function moveItemDown(int $index): void
    {
        $count = count($this->menus[$this->activeMenuIndex]['items']);

        if ($index >= $count - 1) {
            return;
        }

        $items = $this->menus[$this->activeMenuIndex]['items'];
        [$items[$index], $items[$index + 1]] = [$items[$index + 1], $items[$index]];
        $this->menus[$this->activeMenuIndex]['items'] = array_values($items);
    }

    public function reorderItems(int $from, int $to): void
    {
        if ($from === $to) {
            return;
        }

        $items = $this->menus[$this->activeMenuIndex]['items'];

        // When moving a top-level item, carry its sub-children with it
        $movedGroup = [$items[$from]];

        if (($items[$from]['depth'] ?? 0) === 0) {
            $i = $from + 1;

            while ($i < count($items) && ($items[$i]['depth'] ?? 0) > 0) {
                $movedGroup[] = $items[$i];
                $i++;
            }
        }

        $groupSize = count($movedGroup);
        array_splice($items, $from, $groupSize);

        // Adjust destination index after removal
        $adjustedTo = $to > $from ? max(0, $to - ($groupSize - 1)) : $to;
        array_splice($items, $adjustedTo, 0, $movedGroup);

        $this->menus[$this->activeMenuIndex]['items'] = array_values($items);
    }

    public function reorderFooterSlugs(int $from, int $to): void
    {
        if ($from === $to) {
            return;
        }

        $slugs = $this->footerSlugs;
        $moved = array_splice($slugs, $from, 1);
        array_splice($slugs, $to, 0, $moved);
        $this->footerSlugs = array_values($slugs);
    }

    public function save(): void
    {
        Setting::set('navigation.menus', array_values($this->menus));

        ResponseCache::clear();

        $this->dispatch('notify', message: 'Menu saved.');
    }

    public function updatedNewDynamicSource(string $value): void
    {
        if ($value !== '' && $this->newPageLabel === '') {
            $sources = MenuService::availableSources();
            $this->newPageLabel = $sources[$value] ?? '';
        }
    }

    public function addMegaColumnToNew(): void
    {
        $this->newMegaColumns[] = ['heading' => '', 'links' => [['icon' => '', 'title' => '', 'desc' => '', 'url' => '', 'new_tab' => false]]];
    }

    public function addMegaColumnToEdit(): void
    {
        $this->editMegaColumns[] = ['heading' => '', 'links' => [['icon' => '', 'title' => '', 'desc' => '', 'url' => '', 'new_tab' => false]]];
    }

    public function removeMegaColumn(string $mode, int $colIndex): void
    {
        if ($mode === 'new') {
            array_splice($this->newMegaColumns, $colIndex, 1);
            $this->newMegaColumns = array_values($this->newMegaColumns);
        } else {
            array_splice($this->editMegaColumns, $colIndex, 1);
            $this->editMegaColumns = array_values($this->editMegaColumns);
        }
    }

    public function addMegaLink(string $mode, int $colIndex): void
    {
        $blank = ['icon' => '', 'title' => '', 'desc' => '', 'url' => '', 'new_tab' => false];

        if ($mode === 'new') {
            $this->newMegaColumns[$colIndex]['links'][] = $blank;
        } else {
            $this->editMegaColumns[$colIndex]['links'][] = $blank;
        }
    }

    public function removeMegaLink(string $mode, int $colIndex, int $linkIndex): void
    {
        if ($mode === 'new') {
            array_splice($this->newMegaColumns[$colIndex]['links'], $linkIndex, 1);
            $this->newMegaColumns[$colIndex]['links'] = array_values($this->newMegaColumns[$colIndex]['links']);
        } else {
            array_splice($this->editMegaColumns[$colIndex]['links'], $linkIndex, 1);
            $this->editMegaColumns[$colIndex]['links'] = array_values($this->editMegaColumns[$colIndex]['links']);
        }
    }

    private function resetAddForm(): void
    {
        $this->addType = 'page';
        $this->newPageRoute = '';
        $this->newPageLabel = '';
        $this->newCustomUrl = '';
        $this->newCustomLabel = '';
        $this->newCustomNewWindow = false;
        $this->newDynamicSource = '';
        $this->newDynamicShowAll = false;
        $this->newDynamicShowAllLabel = '';
        $this->newMegaColumns = [];
    }
}; ?>

<div x-data="{ iconPickerOpen: false, iconPickerWireKey: '', iconPickerSearch: '', iconPickerLib: 'heroicons', iconPickerVariant: 'outline' }"
     x-init="$watch('iconPickerOpen', v => { if (v) { $refs.iconPickerDialog.showModal(); } else { $refs.iconPickerDialog.close(); iconPickerSearch = ''; } })">
    {{-- Create menu modal --}}
    <flux:modal wire:model="showCreateMenuModal" class="max-w-sm w-full">
        <flux:heading size="lg" class="mb-4">{{ __('Create Menu') }}</flux:heading>
        <form wire:submit="createMenu" class="space-y-4">
            <flux:input
                wire:model.live="newMenuLabel"
                :label="__('Name')"
                placeholder="e.g. Footer Resources"
                required
            />
            <flux:input
                wire:model="newMenuSlug"
                :label="__('Slug')"
                placeholder="e.g. footer-resources"
                description="{{ __('Used by templates to request this menu.') }}"
                required
            />
            <div class="flex justify-end gap-3 pt-1">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit menu modal --}}
    <flux:modal wire:model="showEditMenuModal" class="max-w-sm w-full">
        <flux:heading size="lg" class="mb-6">{{ __('Edit Menu') }}</flux:heading>

        @if (isset($menus[$activeMenuIndex]))
            @php $currentMenu = $menus[$activeMenuIndex]; @endphp
            <div class="space-y-4">
                <flux:input
                    wire:model.live="menus.{{ $activeMenuIndex }}.label"
                    :label="__('Name')"
                />
                <div class="flex flex-col gap-1">
                    <flux:label>{{ __('Slug') }}</flux:label>
                    <code class="flex h-10 items-center rounded-md border border-zinc-200 bg-zinc-50 px-3 text-xs text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                        {{ $currentMenu['slug'] }}
                    </code>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <flux:button
                    wire:click="deleteMenu"
                    wire:confirm="{{ __('Delete this menu and all its items?') }}"
                    variant="ghost"
                    size="sm"
                    icon="trash"
                    class="text-red-500 dark:text-red-400"
                >
                    {{ __('Delete') }}
                </flux:button>
                <flux:modal.close>
                    <flux:button variant="primary" size="sm">{{ __('Done') }}</flux:button>
                </flux:modal.close>
            </div>
        @endif
    </flux:modal>

    {{-- Add item modal --}}
    <flux:modal wire:model="showAddModal" class="max-w-2xl w-full">
        <flux:heading size="lg" class="mb-6">{{ __('Add Menu Item') }}</flux:heading>

        <form wire:submit="addItem" class="space-y-4">
            <flux:radio.group wire:model.live="addType" :label="__('Item type')" variant="segmented">
                <flux:radio value="page" :label="__('Existing page')" />
                <flux:radio value="custom" :label="__('Custom URL')" />
                <flux:radio value="dynamic" :label="__('Dynamic content')" />
                <flux:radio value="mega" :label="__('Mega menu')" />
            </flux:radio.group>

            @if ($addType === 'page')
                <flux:select wire:model.live="newPageRoute" :label="__('Page')">
                    <flux:select.option value="">{{ __('Select a page…') }}</flux:select.option>
                    @foreach ($this->availableRoutes as $routeName => $routeLabel)
                        <flux:select.option value="{{ $routeName }}">{{ $routeLabel }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input
                    wire:model="newPageLabel"
                    :label="__('Navigation label')"
                    placeholder="e.g. Features"
                    required
                />
            @elseif ($addType === 'dynamic')
                <flux:select wire:model.live="newDynamicSource" :label="__('Source')">
                    <flux:select.option value="">{{ __('Select a source…') }}</flux:select.option>
                    @foreach ($this->availableSources as $sourceKey => $sourceLabel)
                        <flux:select.option value="{{ $sourceKey }}">{{ $sourceLabel }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input
                    wire:model="newPageLabel"
                    :label="__('Navigation label')"
                    placeholder="e.g. Locations"
                    description="{{ __('Shown as the dropdown trigger in the nav.') }}"
                    required
                />

                <flux:switch
                    wire:model.live="newDynamicShowAll"
                    :label="__('Show a See All link at the bottom of the dropdown')"
                />

                @if ($newDynamicShowAll)
                    <flux:input
                        wire:model="newDynamicShowAllLabel"
                        :label="__('See All label')"
                        placeholder="e.g. See All Locations"
                    />
                @endif
            @elseif ($addType === 'mega')
                <flux:input
                    wire:model="newPageLabel"
                    :label="__('Navigation label')"
                    placeholder="e.g. Products"
                    required
                />

                <div class="space-y-3">
                    @foreach ($newMegaColumns as $colIndex => $col)
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <flux:heading size="sm">{{ __('Column') }} {{ $colIndex + 1 }}</flux:heading>
                                <flux:button wire:click="removeMegaColumn('new', {{ $colIndex }})" variant="ghost" size="sm" icon="trash" class="text-red-500 dark:text-red-400" />
                            </div>
                            <flux:input wire:model="newMegaColumns.{{ $colIndex }}.heading" :label="__('Column heading (optional)')" placeholder="e.g. Platform" />
                            @foreach ($col['links'] as $linkIndex => $link)
                                <div class="rounded border border-zinc-100 dark:border-zinc-800 p-3 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <flux:text size="sm" class="font-medium">{{ __('Link') }} {{ $linkIndex + 1 }}</flux:text>
                                        <flux:button wire:click="removeMegaLink('new', {{ $colIndex }}, {{ $linkIndex }})" variant="ghost" size="sm" icon="x-mark" />
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">{{ __('Icon') }}</p>
                                            <div class="flex items-center gap-2 px-2.5 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 dark:text-white">
                                                @php $newIconVal = $link['icon'] ?? ''; @endphp
                                                <div class="size-5 shrink-0 text-zinc-500 dark:text-zinc-400">
                                                    @if(str_starts_with($newIconVal, 'ion:'))
                                                        <x-ionicon name="{{ substr($newIconVal, 4) }}" class="size-5" />
                                                    @elseif($newIconVal && str_contains($newIconVal, ':'))
                                                        @php [$_n, $_v] = explode(':', $newIconVal, 2); @endphp
                                                        <x-heroicon name="{{ $_n }}" variant="{{ $_v }}" class="size-5" />
                                                    @elseif($newIconVal)
                                                        <x-heroicon name="{{ $newIconVal }}" class="size-5" />
                                                    @endif
                                                </div>
                                                <span class="text-sm text-zinc-500 dark:text-zinc-400 flex-1 font-mono truncate">{{ $newIconVal ?: '—' }}</span>
                                                <button type="button"
                                                    @click="iconPickerOpen = true; iconPickerWireKey = 'newMegaColumns.{{ $colIndex }}.links.{{ $linkIndex }}.icon'"
                                                    class="text-xs text-primary hover:text-primary/80 shrink-0 transition-colors">
                                                    {{ __('Change') }}
                                                </button>
                                            </div>
                                        </div>
                                        <flux:input wire:model="newMegaColumns.{{ $colIndex }}.links.{{ $linkIndex }}.title" :label="__('Title')" placeholder="Performance" required />
                                    </div>
                                    <flux:input wire:model="newMegaColumns.{{ $colIndex }}.links.{{ $linkIndex }}.desc" :label="__('Description')" placeholder="Short description (optional)" />
                                    <flux:input wire:model="newMegaColumns.{{ $colIndex }}.links.{{ $linkIndex }}.url" :label="__('URL')" placeholder="/pricing or https://…" />
                                    <flux:checkbox wire:model="newMegaColumns.{{ $colIndex }}.links.{{ $linkIndex }}.new_tab" :label="__('Open in new tab')" />
                                </div>
                            @endforeach
                            <flux:button wire:click="addMegaLink('new', {{ $colIndex }})" variant="ghost" size="sm" icon="plus">{{ __('Add link') }}</flux:button>
                        </div>
                    @endforeach
                </div>

                <flux:button wire:click="addMegaColumnToNew" variant="outline" size="sm" icon="plus">{{ __('Add column') }}</flux:button>
            @else
                <flux:input
                    wire:model="newCustomLabel"
                    :label="__('Navigation label')"
                    placeholder="e.g. Our Store"
                    required
                />

                <flux:input
                    wire:model="newCustomUrl"
                    :label="__('URL')"
                    placeholder="https://… or #"
                    required
                />

                <flux:checkbox
                    wire:model="newCustomNewWindow"
                    :label="__('Open in new window')"
                />
            @endif

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Add Item') }}</span>
                    <span wire:loading>{{ __('Adding…') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">{{ __('Menus') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Manage the navigation menus for your website.') }}</flux:text>
        </div>

        @if (empty($menus))
            <div class="max-w-2xl">
                <div class="rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600 py-20 text-center text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="bars-3" class="mx-auto mb-3 size-12 opacity-40" />
                    <p class="text-sm font-medium">{{ __('No menus yet.') }}</p>
                    <p class="mt-1 text-xs">{{ __('Create your first menu to get started.') }}</p>
                    <flux:button wire:click="$set('showCreateMenuModal', true)" variant="outline" size="sm" class="mt-4" icon="plus">
                        {{ __('Create Menu') }}
                    </flux:button>
                </div>
            </div>
        @else
            <div class="max-w-3xl">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <flux:select wire:model.live="activeMenuIndex" class="w-52">
                                @foreach ($menus as $i => $menu)
                                    <flux:select.option value="{{ $i }}">{{ $menu['label'] }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:tooltip content="{{ __('Create menu') }}" position="bottom">
                                <flux:button
                                    wire:click="$set('showCreateMenuModal', true)"
                                    variant="ghost"
                                    size="sm"
                                    icon="plus"
                                />
                            </flux:tooltip>
                            <flux:tooltip content="{{ __('Edit menu') }}" position="bottom">
                                <flux:button
                                    wire:click="$set('showEditMenuModal', true)"
                                    variant="ghost"
                                    size="sm"
                                    icon="pencil-square"
                                />
                            </flux:tooltip>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="openAddModal" variant="outline" size="sm" icon="plus">
                                {{ __('Add Item') }}
                            </flux:button>
                            <flux:button wire:click="save" variant="primary" size="sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                                <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                            </flux:button>
                        </div>
                    </div>

                    @if (isset($menus[$activeMenuIndex]))
                        @php
                            $currentMenu = $menus[$activeMenuIndex];
                            $currentItems = $currentMenu['items'] ?? [];
                        @endphp

                        @if (empty($currentItems))
                            <div class="rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600 py-16 text-center text-zinc-500 dark:text-zinc-400">
                                <flux:icon name="bars-3" class="mx-auto mb-3 size-12 opacity-40" />
                                <p class="text-sm">{{ __('No items yet.') }}</p>
                                <flux:button wire:click="openAddModal" variant="outline" size="sm" class="mt-4">
                                    {{ __('Add your first item') }}
                                </flux:button>
                            </div>
                        @else
                            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                                <table class="w-full text-sm">
                                    <tbody
                                        class="divide-y divide-zinc-200 dark:divide-zinc-700"
                                        x-data="{ dragging: null, over: null }"
                                    >
                                        @foreach ($currentItems as $index => $item)
                                            @php
                                                $itemDepth = $item['depth'] ?? 0;
                                                $itemType  = $item['type'] ?? null;
                                                $isSubItem = $itemDepth >= 1;
                                                $prevItem  = $index > 0 ? $currentItems[$index - 1] : null;
                                                $canIndent = ! $isSubItem
                                                    && $prevItem !== null
                                                    && ! in_array($itemType, ['mega', 'dynamic'], true)
                                                    && ($prevItem['depth'] ?? 0) === 0
                                                    && ! in_array($prevItem['type'] ?? null, ['mega', 'dynamic'], true);
                                                $canOutdent = $isSubItem;
                                            @endphp
                                            <tr
                                                wire:key="menu-{{ $activeMenuIndex }}-item-{{ $index }}"
                                                class="{{ $isSubItem ? 'bg-zinc-50 dark:bg-zinc-800/50' : 'bg-white dark:bg-zinc-900' }}"
                                                draggable="true"
                                                @dragstart="dragging = {{ $index }}"
                                                @dragover.prevent="over = {{ $index }}"
                                                @drop="if (dragging !== null) { $wire.reorderItems(dragging, over); } dragging = null; over = null"
                                                @dragend="dragging = null; over = null"
                                                :style="{
                                                    opacity: dragging === {{ $index }} ? '0.4' : '',
                                                    'border-top': over === {{ $index }} && dragging !== null && dragging > {{ $index }} ? '2px solid var(--color-primary)' : '',
                                                    'border-bottom': over === {{ $index }} && dragging !== null && dragging < {{ $index }} ? '2px solid var(--color-primary)' : ''
                                                }"
                                            >
                                                <td class="w-8 cursor-grab px-4 py-3 text-zinc-400 active:cursor-grabbing dark:text-zinc-500">
                                                    <flux:icon name="bars-2" class="size-4" />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="{{ $isSubItem ? 'pl-6' : '' }} flex items-center gap-2">
                                                        @if ($isSubItem)
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                                        @endif
                                                        <div>
                                                            <div class="font-medium {{ ($item['active'] ?? true) ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500' }}">
                                                                {{ $item['label'] }}
                                                            </div>
                                                            <div class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
                                                                @if ($itemType === 'mega')
                                                                    @php
                                                                        $megaCols = $item['columns'] ?? [];
                                                                        $megaLinkCount = collect($megaCols)->sum(fn ($c) => count($c['links'] ?? []));
                                                                    @endphp
                                                                    <span class="font-mono">mega · {{ count($megaCols) }} {{ Str::plural('column', count($megaCols)) }} · {{ $megaLinkCount }} {{ Str::plural('link', $megaLinkCount) }}</span>
                                                                @elseif ($itemType === 'dynamic')
                                                                    <span class="font-mono">dynamic · {{ $this->availableSources[$item['source']] ?? $item['source'] }}</span>
                                                                @elseif (isset($item['route']))
                                                                    <span class="font-mono">route: {{ $item['route'] }}</span>
                                                                @else
                                                                    <span class="font-mono">{{ $item['url'] }}</span>
                                                                    @if (!empty($item['new_window']))
                                                                        <span class="font-mono"> · {{ __('new window') }}</span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-end gap-1">
                                                        @if ($canOutdent)
                                                            <flux:tooltip content="{{ __('Move to top level') }}" position="bottom">
                                                                <flux:button
                                                                    wire:click="outdentItem({{ $index }})"
                                                                    wire:target="outdentItem({{ $index }})"
                                                                    variant="ghost"
                                                                    size="sm"
                                                                    icon="chevron-left"
                                                                />
                                                            </flux:tooltip>
                                                        @elseif ($canIndent)
                                                            <flux:tooltip content="{{ __('Make sub-item') }}" position="bottom">
                                                                <flux:button
                                                                    wire:click="indentItem({{ $index }})"
                                                                    wire:target="indentItem({{ $index }})"
                                                                    variant="ghost"
                                                                    size="sm"
                                                                    icon="chevron-right"
                                                                />
                                                            </flux:tooltip>
                                                        @else
                                                            <div class="size-8"></div>
                                                        @endif
                                                        <flux:tooltip content="{{ ($item['active'] ?? true) ? __('Disable item') : __('Enable item') }}" position="bottom">
                                                            <flux:switch wire:model.live="menus.{{ $activeMenuIndex }}.items.{{ $index }}.active" />
                                                        </flux:tooltip>
                                                        <flux:tooltip content="Edit item" position="bottom">
                                                            <flux:button
                                                                wire:click="openEditItemModal({{ $index }})"
                                                                wire:target="openEditItemModal({{ $index }})"
                                                                variant="ghost"
                                                                size="sm"
                                                                icon="pencil-square"
                                                            />
                                                        </flux:tooltip>
                                                        <flux:tooltip content="Remove item" position="bottom">
                                                            <flux:button
                                                                wire:click="removeItem({{ $index }})"
                                                                wire:target="removeItem({{ $index }})"
                                                                variant="ghost"
                                                                size="sm"
                                                                icon="trash"
                                                                class="text-red-500 dark:text-red-400"
                                                            />
                                                        </flux:tooltip>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if ($editItemIndex === $index)
                                                <tr wire:key="menu-{{ $activeMenuIndex }}-item-{{ $index }}-edit" class="bg-zinc-50 dark:bg-zinc-800/40">
                                                    <td colspan="3" class="px-6 py-5">
                                                        <form wire:submit="saveEditItem" class="space-y-4">
                                                            <div class="flex gap-4">
                                                                <div class="md:w-1/3">
                                                                    <flux:select wire:model.live="editItemType" :label="__('Item type')">
                                                                        <flux:select.option value="page">{{ __('Existing page') }}</flux:select.option>
                                                                        <flux:select.option value="custom">{{ __('Custom URL') }}</flux:select.option>
                                                                        <flux:select.option value="dynamic">{{ __('Dynamic content') }}</flux:select.option>
                                                                        <flux:select.option value="mega">{{ __('Mega menu') }}</flux:select.option>
                                                                    </flux:select>
                                                                </div>
                                                                @if ($editItemType === 'page')
                                                                    <div class="md:w-1/3">
                                                                        <flux:select wire:model.live="editItemRoute" :label="__('Page')">
                                                                            <flux:select.option value="">{{ __('Select a page…') }}</flux:select.option>
                                                                            @foreach ($this->availableRoutes as $routeName => $routeLabel)
                                                                                <flux:select.option value="{{ $routeName }}">{{ $routeLabel }}</flux:select.option>
                                                                            @endforeach
                                                                        </flux:select>
                                                                    </div>
                                                                @elseif ($editItemType === 'dynamic')
                                                                    <div class="md:w-1/3">
                                                                        <flux:select wire:model.live="editDynamicSource" :label="__('Source')">
                                                                            <flux:select.option value="">{{ __('Select a source…') }}</flux:select.option>
                                                                            @foreach ($this->availableSources as $sourceKey => $sourceLabel)
                                                                                <flux:select.option value="{{ $sourceKey }}">{{ $sourceLabel }}</flux:select.option>
                                                                            @endforeach
                                                                        </flux:select>
                                                                    </div>
                                                                @endif
                                                                <div class="md:w-1/3">
                                                                    <flux:input
                                                                        wire:model="editItemLabel"
                                                                        :label="__('Navigation label')"
                                                                        placeholder="e.g. Features"
                                                                        required
                                                                    />
                                                                </div>
                                                            </div>

                                                            @if ($editItemType === 'mega')
                                                                {{-- Column builder rendered after the label field below --}}
                                                            @else
                                                                <flux:input
                                                                    wire:model="editItemUrl"
                                                                    :label="__('URL')"
                                                                    placeholder="https://… or #"
                                                                    required
                                                                />
                                                                <flux:checkbox
                                                                    wire:model="editItemNewWindow"
                                                                    :label="__('Open in new window')"
                                                                />
                                                            @endif

                                                            @if ($editItemType === 'dynamic')
                                                                <flux:switch
                                                                    wire:model.live="editDynamicShowAll"
                                                                    :label="__('Show a See All link at the bottom of the dropdown')"
                                                                />
                                                                @if ($editDynamicShowAll)
                                                                    <flux:input
                                                                        wire:model="editDynamicShowAllLabel"
                                                                        :label="__('See All label')"
                                                                        placeholder="e.g. See All Locations"
                                                                    />
                                                                @endif
                                                            @elseif ($editItemType === 'mega')
                                                                <div x-data="{ activeCol: 0 }">
                                                                    {{-- Column tabs --}}
                                                                    <div class="mb-3 flex items-center gap-2">
                                                                        <div class="flex items-center gap-1 rounded-lg bg-zinc-100 p-1 dark:bg-zinc-800">
                                                                            @foreach ($editMegaColumns as $colIndex => $col)
                                                                                <button
                                                                                    type="button"
                                                                                    @click="activeCol = {{ $colIndex }}"
                                                                                    :class="activeCol === {{ $colIndex }} ? 'bg-white text-zinc-900 shadow-sm dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                                                                                    class="rounded-md px-3 py-1 text-sm font-medium transition-colors"
                                                                                >{{ $col['heading'] ?: __('Column :n', ['n' => $colIndex + 1]) }}</button>
                                                                            @endforeach
                                                                        </div>
                                                                        <button
                                                                            type="button"
                                                                            @click="$wire.addMegaColumnToEdit(); activeCol = {{ count($editMegaColumns) }}"
                                                                            class="inline-flex items-center gap-1 rounded-md px-2.5 py-1.5 text-sm text-zinc-400 transition-colors hover:text-zinc-600 dark:hover:text-zinc-200"
                                                                        >
                                                                            <flux:icon name="plus" class="size-3.5" />
                                                                            {{ __('Add column') }}
                                                                        </button>
                                                                    </div>

                                                                    {{-- Column panels --}}
                                                                    @foreach ($editMegaColumns as $colIndex => $col)
                                                                        <div x-show="activeCol === {{ $colIndex }}" class="space-y-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                                                                            <div class="flex items-center justify-between">
                                                                                <flux:heading size="sm">{{ __('Column') }} {{ $colIndex + 1 }}</flux:heading>
                                                                                <button
                                                                                    type="button"
                                                                                    @click="activeCol = {{ max(0, $colIndex - 1) }}; $wire.removeMegaColumn('edit', {{ $colIndex }})"
                                                                                    class="inline-flex items-center justify-center rounded-md p-1.5 text-red-400 transition-colors hover:text-red-600 dark:text-red-500 dark:hover:text-red-400"
                                                                                >
                                                                                    <flux:icon name="trash" class="size-4" />
                                                                                </button>
                                                                            </div>
                                                                            <flux:input wire:model="editMegaColumns.{{ $colIndex }}.heading" :label="__('Column heading (optional)')" placeholder="e.g. Platform" />
                                                                            @foreach ($col['links'] as $linkIndex => $link)
                                                                                <div class="space-y-2 rounded border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                                                                                    <div class="flex items-center justify-between">
                                                                                        <flux:text size="sm" class="font-medium">{{ __('Link') }} {{ $linkIndex + 1 }}</flux:text>
                                                                                        <flux:button wire:click="removeMegaLink('edit', {{ $colIndex }}, {{ $linkIndex }})" variant="ghost" size="sm" icon="x-mark" />
                                                                                    </div>
                                                                                    <div class="grid grid-cols-2 gap-2">
                                                                                        <div>
                                                                                            <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">{{ __('Icon') }}</p>
                                                                                            <div class="flex items-center gap-2 px-2.5 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 dark:text-white">
                                                                                                @php $editIconVal = $link['icon'] ?? ''; @endphp
                                                                                                <div class="size-5 shrink-0 text-zinc-500 dark:text-zinc-400">
                                                                                                    @if(str_starts_with($editIconVal, 'ion:'))
                                                                                                        <x-ionicon name="{{ substr($editIconVal, 4) }}" class="size-5" />
                                                                                                    @elseif($editIconVal && str_contains($editIconVal, ':'))
                                                                                                        @php [$_n, $_v] = explode(':', $editIconVal, 2); @endphp
                                                                                                        <x-heroicon name="{{ $_n }}" variant="{{ $_v }}" class="size-5" />
                                                                                                    @elseif($editIconVal)
                                                                                                        <x-heroicon name="{{ $editIconVal }}" class="size-5" />
                                                                                                    @endif
                                                                                                </div>
                                                                                                <span class="text-sm text-zinc-500 dark:text-zinc-400 flex-1 font-mono truncate">{{ $editIconVal ?: '—' }}</span>
                                                                                                <button type="button"
                                                                                                    @click="iconPickerOpen = true; iconPickerWireKey = 'editMegaColumns.{{ $colIndex }}.links.{{ $linkIndex }}.icon'"
                                                                                                    class="text-xs text-primary hover:text-primary/80 shrink-0 transition-colors">
                                                                                                    {{ __('Change') }}
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                        <flux:input wire:model="editMegaColumns.{{ $colIndex }}.links.{{ $linkIndex }}.title" :label="__('Title')" placeholder="Performance" required />
                                                                                    </div>
                                                                                    <flux:input wire:model="editMegaColumns.{{ $colIndex }}.links.{{ $linkIndex }}.desc" :label="__('Description')" placeholder="Short description (optional)" />
                                                                                    <flux:input wire:model="editMegaColumns.{{ $colIndex }}.links.{{ $linkIndex }}.url" :label="__('URL')" placeholder="/pricing or https://…" />
                                                                                    <flux:checkbox wire:model="editMegaColumns.{{ $colIndex }}.links.{{ $linkIndex }}.new_tab" :label="__('Open in new tab')" />
                                                                                </div>
                                                                            @endforeach
                                                                            <flux:button wire:click="addMegaLink('edit', {{ $colIndex }})" variant="ghost" size="sm" icon="plus">{{ __('Add link') }}</flux:button>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif

                                                            <div class="flex items-center justify-end gap-3 pt-2">
                                                                <flux:button
                                                                    type="button"
                                                                    wire:click="$set('editItemIndex', -1)"
                                                                    variant="ghost"
                                                                >{{ __('Cancel') }}</flux:button>
                                                                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                                                                    <span wire:loading.remove>{{ __('Save') }}</span>
                                                                    <span wire:loading>{{ __('Saving…') }}</span>
                                                                </flux:button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif

            </div>
        @endif
    </flux:main>

    {{-- Shared icon picker modal (used by mega menu icon Change buttons) --}}
    @php
        if (! isset($outlineIconsMenus)) {
            $allIconsDataMenus = require resource_path('heroicons/data.php');
            $outlineIconsMenus = array_keys($allIconsDataMenus['outline']);
            $solidIconsMenus   = array_keys($allIconsDataMenus['solid']);
        }
        if (! isset($ionIconsFilledMenus)) {
            $allIonIconsDataMenus = require resource_path('ionicons/data.php');
            $ionIconNamesMenus    = array_keys($allIonIconsDataMenus);
            $ionIconsFilledMenus  = array_values(array_filter($ionIconNamesMenus, fn ($n) => ! str_ends_with($n, '-outline') && ! str_ends_with($n, '-sharp')));
            $ionIconsOutlineMenus = array_values(array_filter($ionIconNamesMenus, fn ($n) => str_ends_with($n, '-outline')));
            $ionIconsSharpMenus   = array_values(array_filter($ionIconNamesMenus, fn ($n) => str_ends_with($n, '-sharp')));
        }
    @endphp
    <style>
        dialog#icon-picker-dialog::backdrop { background-color: rgb(0 0 0 / 0.5); }
        dialog#icon-picker-dialog { padding: 0; border: none; border-radius: 0.75rem; width: 100%; max-width: 42rem; max-height: 80vh; overflow: hidden; box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25); }
    </style>
    <dialog
        wire:ignore
        x-ref="iconPickerDialog"
        id="icon-picker-dialog"
        @close="iconPickerOpen = false"
        @click.self="iconPickerOpen = false"
        class="bg-white dark:bg-zinc-800"
    >
        <div class="flex flex-col h-full overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Select Icon') }}</p>
                <div class="flex items-center gap-3">
                    {{-- Library toggle --}}
                    <div class="flex rounded-lg border border-zinc-200 dark:border-zinc-700 p-0.5">
                        <button type="button" @click="iconPickerLib = 'heroicons'; iconPickerVariant = 'outline'" x-bind:class="iconPickerLib === 'heroicons' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Heroicons</button>
                        <button type="button" @click="iconPickerLib = 'ionicons'; iconPickerVariant = 'outline'" x-bind:class="iconPickerLib === 'ionicons' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Ionicons</button>
                    </div>
                    {{-- Variant toggle --}}
                    <div class="flex rounded-lg border border-zinc-200 dark:border-zinc-700 p-0.5">
                        <template x-if="iconPickerLib === 'heroicons'">
                            <div class="flex">
                                <button type="button" @click="iconPickerVariant = 'outline'" x-bind:class="iconPickerVariant === 'outline' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Outline</button>
                                <button type="button" @click="iconPickerVariant = 'solid'" x-bind:class="iconPickerVariant === 'solid' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Solid</button>
                            </div>
                        </template>
                        <template x-if="iconPickerLib === 'ionicons'">
                            <div class="flex">
                                <button type="button" @click="iconPickerVariant = 'filled'" x-bind:class="iconPickerVariant === 'filled' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Filled</button>
                                <button type="button" @click="iconPickerVariant = 'outline'" x-bind:class="iconPickerVariant === 'outline' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Outline</button>
                                <button type="button" @click="iconPickerVariant = 'sharp'" x-bind:class="iconPickerVariant === 'sharp' ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'" class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors">Sharp</button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="iconPickerOpen = false; iconPickerSearch = ''" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                        <flux:icon name="x-mark" class="size-4" />
                    </button>
                </div>
            </div>
            {{-- Search --}}
            <div class="px-5 py-3 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                <input
                    x-model="iconPickerSearch"
                    type="text"
                    placeholder="{{ __('Search icons…') }}"
                    class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                />
            </div>
            {{-- Icon grids --}}
            <div class="overflow-y-auto p-5">
                {{-- Heroicons --}}
                <div x-show="iconPickerLib === 'heroicons' && iconPickerVariant === 'outline'" class="grid grid-cols-10 gap-1">
                    @foreach ($outlineIconsMenus as $iconName)
                        <button type="button" x-show="!iconPickerSearch || '{{ $iconName }}'.includes(iconPickerSearch)" @click="$wire.set(iconPickerWireKey, '{{ $iconName }}'); iconPickerOpen = false; iconPickerSearch = ''" class="flex items-center justify-center p-2 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 text-zinc-500 dark:text-zinc-400 transition-colors" title="{{ $iconName }}"><x-heroicon name="{{ $iconName }}" class="size-5" /></button>
                    @endforeach
                </div>
                <div x-show="iconPickerLib === 'heroicons' && iconPickerVariant === 'solid'" class="grid grid-cols-10 gap-1">
                    @foreach ($solidIconsMenus as $iconName)
                        <button type="button" x-show="!iconPickerSearch || '{{ $iconName }}'.includes(iconPickerSearch)" @click="$wire.set(iconPickerWireKey, '{{ $iconName }}:solid'); iconPickerOpen = false; iconPickerSearch = ''" class="flex items-center justify-center p-2 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 text-zinc-500 dark:text-zinc-400 transition-colors" title="{{ $iconName }}"><x-heroicon name="{{ $iconName }}" variant="solid" class="size-5" /></button>
                    @endforeach
                </div>
                {{-- Ionicons --}}
                <div x-show="iconPickerLib === 'ionicons' && iconPickerVariant === 'filled'" class="grid grid-cols-10 gap-1">
                    @foreach ($ionIconsFilledMenus as $ionName)
                        <button type="button" x-show="!iconPickerSearch || '{{ $ionName }}'.includes(iconPickerSearch)" @click="$wire.set(iconPickerWireKey, 'ion:{{ $ionName }}'); iconPickerOpen = false; iconPickerSearch = ''" class="flex items-center justify-center p-2 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 text-zinc-500 dark:text-zinc-400 transition-colors" title="{{ $ionName }}"><x-ionicon name="{{ $ionName }}" class="size-5" /></button>
                    @endforeach
                </div>
                <div x-show="iconPickerLib === 'ionicons' && iconPickerVariant === 'outline'" class="grid grid-cols-10 gap-1">
                    @foreach ($ionIconsOutlineMenus as $ionName)
                        <button type="button" x-show="!iconPickerSearch || '{{ $ionName }}'.includes(iconPickerSearch)" @click="$wire.set(iconPickerWireKey, 'ion:{{ $ionName }}'); iconPickerOpen = false; iconPickerSearch = ''" class="flex items-center justify-center p-2 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 text-zinc-500 dark:text-zinc-400 transition-colors" title="{{ $ionName }}"><x-ionicon name="{{ $ionName }}" class="size-5" /></button>
                    @endforeach
                </div>
                <div x-show="iconPickerLib === 'ionicons' && iconPickerVariant === 'sharp'" class="grid grid-cols-10 gap-1">
                    @foreach ($ionIconsSharpMenus as $ionName)
                        <button type="button" x-show="!iconPickerSearch || '{{ $ionName }}'.includes(iconPickerSearch)" @click="$wire.set(iconPickerWireKey, 'ion:{{ $ionName }}'); iconPickerOpen = false; iconPickerSearch = ''" class="flex items-center justify-center p-2 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 text-zinc-500 dark:text-zinc-400 transition-colors" title="{{ $ionName }}"><x-ionicon name="{{ $ionName }}" class="size-5" /></button>
                    @endforeach
                </div>
            </div>
        </div>
    </dialog>
</div>
