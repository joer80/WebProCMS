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

    /** @var array<int, string> */
    public array $footerSlugs = [];

    public bool $currentMenuInFooter = false;

    public bool $showAuthLinks = false;

    public bool $showAccountInFooter = true;

    public bool $showEditItemModal = false;
    public int $editItemIndex = -1;
    public string $editItemType = 'page';
    public string $editItemLabel = '';
    public string $editItemRoute = '';
    public string $editItemUrl = '';
    public bool $editItemNewWindow = false;
    public string $editDynamicSource = '';
    public bool $editDynamicShowAll = false;
    public string $editDynamicShowAllLabel = '';

    public function mount(): void
    {
        $this->menus = Setting::get('navigation.menus', []);
        $this->footerSlugs = Setting::get('navigation.footer_slugs', []);
        $this->showAuthLinks = (bool) Setting::get('navigation.show_auth_links', '0');
        $this->showAccountInFooter = (bool) Setting::get('navigation.show_account_in_footer', '0');
        $this->syncCurrentMenuInFooter();
    }

    public function updatedActiveMenuIndex(): void
    {
        $this->syncCurrentMenuInFooter();
    }

    public function updatedCurrentMenuInFooter(bool $value): void
    {
        $slug = $this->menus[$this->activeMenuIndex]['slug'] ?? '';

        if ($value && ! in_array($slug, $this->footerSlugs, true)) {
            $this->footerSlugs[] = $slug;
        } elseif (! $value) {
            $this->footerSlugs = array_values(array_filter($this->footerSlugs, fn (string $s): bool => $s !== $slug));
        }
    }

    private function syncCurrentMenuInFooter(): void
    {
        $slug = $this->menus[$this->activeMenuIndex]['slug'] ?? '';
        $this->currentMenuInFooter = in_array($slug, $this->footerSlugs, true);
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
        $item = $this->menus[$this->activeMenuIndex]['items'][$index];
        $this->editItemIndex = $index;

        if (($item['type'] ?? null) === 'dynamic') {
            $this->editItemType = 'dynamic';
            $this->editDynamicSource = $item['source'] ?? '';
            $this->editDynamicShowAll = ! empty($item['show_all']);
            $this->editDynamicShowAllLabel = $item['show_all_label'] ?? '';
        } else {
            $this->editItemType = isset($item['route']) ? 'page' : 'custom';
            $this->editDynamicSource = '';
            $this->editDynamicShowAll = false;
            $this->editDynamicShowAllLabel = '';
        }

        $this->editItemLabel = $item['label'] ?? '';
        $this->editItemRoute = $item['route'] ?? '';
        $this->editItemUrl = $item['url'] ?? '';
        $this->editItemNewWindow = $item['new_window'] ?? false;
        $this->showEditItemModal = true;
    }

    public function saveEditItem(): void
    {
        $active = $this->menus[$this->activeMenuIndex]['items'][$this->editItemIndex]['active'] ?? true;

        if ($this->editItemType === 'page') {
            $this->validate([
                'editItemRoute' => ['required', 'string'],
                'editItemLabel' => ['required', 'string', 'max:255'],
            ]);

            $item = [
                'label'  => $this->editItemLabel,
                'route'  => $this->editItemRoute,
                'active' => $active,
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
            ];

            if ($this->editDynamicShowAll) {
                $item['show_all'] = true;
                $item['show_all_label'] = $this->editDynamicShowAllLabel ?: 'See All';
            }
        } else {
            $this->validate([
                'editItemLabel' => ['required', 'string', 'max:255'],
                'editItemUrl'   => ['required', 'url', 'max:2048'],
            ]);

            $item = [
                'label'  => $this->editItemLabel,
                'url'    => $this->editItemUrl,
                'active' => $active,
            ];

            if ($this->editItemNewWindow) {
                $item['new_window'] = true;
            }
        }

        $this->menus[$this->activeMenuIndex]['items'][$this->editItemIndex] = $item;
        $this->showEditItemModal = false;
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

        $item = array_splice($this->menus[$this->activeMenuIndex]['items'], $from, 1)[0];
        array_splice($this->menus[$this->activeMenuIndex]['items'], $to, 0, [$item]);
        $this->menus[$this->activeMenuIndex]['items'] = array_values($this->menus[$this->activeMenuIndex]['items']);
    }

    public function reorderFooterSlugs(int $from, int $to): void
    {
        if ($from === $to) {
            return;
        }

        $slug = array_splice($this->footerSlugs, $from, 1)[0];
        array_splice($this->footerSlugs, $to, 0, [$slug]);
        $this->footerSlugs = array_values($this->footerSlugs);
    }

    public function save(): void
    {
        Setting::set('navigation.menus', array_values($this->menus));
        Setting::set('navigation.footer_slugs', array_values($this->footerSlugs));
        Setting::set('navigation.show_auth_links', $this->showAuthLinks ? '1' : '0');
        Setting::set('navigation.show_account_in_footer', $this->showAccountInFooter ? '1' : '0');

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
    }
}; ?>

<div>
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
                <flux:switch wire:model.live="currentMenuInFooter" :label="__('Show in footer')" />
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

    {{-- Edit item modal --}}
    <flux:modal wire:model="showEditItemModal" class="max-w-md w-full">
        <flux:heading size="lg" class="mb-6">{{ __('Edit Menu Item') }}</flux:heading>

        <form wire:submit="saveEditItem" class="space-y-4">
            <flux:radio.group wire:model.live="editItemType" :label="__('Item type')">
                <flux:radio value="page" :label="__('Existing page')" />
                <flux:radio value="custom" :label="__('Custom URL')" />
                <flux:radio value="dynamic" :label="__('Dynamic content')" />
            </flux:radio.group>

            @if ($editItemType === 'page')
                <flux:select wire:model.live="editItemRoute" :label="__('Page')">
                    <flux:select.option value="">{{ __('Select a page…') }}</flux:select.option>
                    @foreach ($this->availableRoutes as $routeName => $routeLabel)
                        <flux:select.option value="{{ $routeName }}">{{ $routeLabel }}</flux:select.option>
                    @endforeach
                </flux:select>
            @elseif ($editItemType === 'dynamic')
                <flux:select wire:model.live="editDynamicSource" :label="__('Source')">
                    <flux:select.option value="">{{ __('Select a source…') }}</flux:select.option>
                    @foreach ($this->availableSources as $sourceKey => $sourceLabel)
                        <flux:select.option value="{{ $sourceKey }}">{{ $sourceLabel }}</flux:select.option>
                    @endforeach
                </flux:select>
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

            <flux:input
                wire:model="editItemLabel"
                :label="__('Navigation label')"
                placeholder="e.g. Features"
                required
            />

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
            @endif

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Save') }}</span>
                    <span wire:loading>{{ __('Saving…') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Add item modal --}}
    <flux:modal wire:model="showAddModal" class="max-w-md w-full">
        <flux:heading size="lg" class="mb-6">{{ __('Add Menu Item') }}</flux:heading>

        <form wire:submit="addItem" class="space-y-4">
            <flux:radio.group wire:model.live="addType" :label="__('Item type')">
                <flux:radio value="page" :label="__('Existing page')" />
                <flux:radio value="custom" :label="__('Custom URL')" />
                <flux:radio value="dynamic" :label="__('Dynamic content')" />
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
            <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 2rem;">
                {{-- Left: menu editor (3/4 width) --}}
                <div>
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
                                            <tr
                                                wire:key="menu-{{ $activeMenuIndex }}-item-{{ $index }}"
                                                class="bg-white dark:bg-zinc-900"
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
                                                    <div class="font-medium {{ ($item['active'] ?? true) ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500' }}">
                                                        {{ $item['label'] }}
                                                    </div>
                                                    <div class="mt-0.5 font-mono text-xs text-zinc-400 dark:text-zinc-500">
                                                        @if (($item['type'] ?? null) === 'dynamic')
                                                            dynamic · {{ $this->availableSources[$item['source']] ?? $item['source'] }}
                                                        @elseif (isset($item['route']))
                                                            route: {{ $item['route'] }}
                                                        @else
                                                            {{ $item['url'] }}
                                                            @if (!empty($item['new_window']))
                                                                · {{ __('new window') }}
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <flux:switch wire:model.live="menus.{{ $activeMenuIndex }}.items.{{ $index }}.active" />
                                                        <flux:tooltip content="Edit item" position="bottom">
                                                            <flux:button
                                                                wire:click="openEditItemModal({{ $index }})"
                                                                variant="ghost"
                                                                size="sm"
                                                                icon="pencil-square"
                                                            />
                                                        </flux:tooltip>
                                                        <flux:tooltip content="Remove item" position="bottom">
                                                            <flux:button
                                                                wire:click="removeItem({{ $index }})"
                                                                variant="ghost"
                                                                size="sm"
                                                                icon="trash"
                                                                class="text-red-500 dark:text-red-400"
                                                            />
                                                        </flux:tooltip>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif

                </div>

                {{-- Right: sidebar (1/4 width) --}}
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    {{-- Header settings --}}
                    <div x-data="{ open: false }" class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <button
                            @click="open = !open"
                            class="flex w-full items-center justify-between px-4 py-3 text-left"
                            style="background-color: var(--color-zinc-100);"
                        >
                            <flux:heading size="sm">{{ __('Header settings') }}</flux:heading>
                            <span :style="'transform: rotate(' + (open ? \'180deg\' : \'0deg\') + ')'">
                                <flux:icon name="chevron-down" class="size-4 text-zinc-400" />
                            </span>
                        </button>
                        <div x-show="open" x-cloak class="space-y-3" style="padding: 1rem; border-top: 1px solid var(--color-zinc-200);">
                            <flux:switch wire:model.live="showAuthLinks" :label="__('Show sign in / register links in header nav')" />
                        </div>
                    </div>

                    {{-- Footer settings --}}
                    <div x-data="{ open: false }" class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <button
                            @click="open = !open"
                            class="flex w-full items-center justify-between px-4 py-3 text-left"
                            style="background-color: var(--color-zinc-100);"
                        >
                            <flux:heading size="sm">{{ __('Footer settings') }}</flux:heading>
                            <span :style="'transform: rotate(' + (open ? \'180deg\' : \'0deg\') + ')'">
                                <flux:icon name="chevron-down" class="size-4 text-zinc-400" />
                            </span>
                        </button>
                        <div x-show="open" x-cloak class="space-y-4" style="padding: 1rem; border-top: 1px solid var(--color-zinc-200);">
                            <flux:switch wire:model.live="showAccountInFooter" :label="__('Show account column in footer')" />

                            @if (count($footerSlugs) > 1)
                                <div>
                                    <flux:text size="sm" class="mb-2">{{ __('Drag to reorder how footer menus appear as columns.') }}</flux:text>
                                    <div
                                        x-data="{ draggingIdx: null, overIdx: null }"
                                        class="flex flex-wrap gap-2"
                                    >
                                        @foreach ($footerSlugs as $fIdx => $fSlug)
                                            @php
                                                $fMenu = collect($menus)->firstWhere('slug', $fSlug);
                                                $fLabel = $fMenu['label'] ?? $fSlug;
                                            @endphp
                                            <div
                                                wire:key="footer-order-{{ $fIdx }}"
                                                draggable="true"
                                                @dragstart="draggingIdx = {{ $fIdx }}"
                                                @dragover.prevent="overIdx = {{ $fIdx }}"
                                                @drop="if (draggingIdx !== null) { $wire.reorderFooterSlugs(draggingIdx, overIdx); } draggingIdx = null; overIdx = null"
                                                @dragend="draggingIdx = null; overIdx = null"
                                                :class="{
                                                    'opacity-40': draggingIdx === {{ $fIdx }},
                                                    'ring-2 ring-zinc-900 dark:ring-zinc-200': overIdx === {{ $fIdx }} && draggingIdx !== null && draggingIdx !== {{ $fIdx }}
                                                }"
                                                class="flex cursor-grab items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 active:cursor-grabbing dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300"
                                            >
                                                <flux:icon name="bars-2" class="size-3.5 text-zinc-400" />
                                                {{ $fLabel }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </flux:main>
</div>
