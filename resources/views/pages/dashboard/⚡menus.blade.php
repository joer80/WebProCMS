<?php

use Illuminate\Support\Facades\Route as RoutesFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

    public function mount(): void
    {
        $websiteType = config('features.website_type', 'saas');
        $this->menus = config("navigation.{$websiteType}.menus", []);
        $this->footerSlugs = config("navigation.{$websiteType}.footer_slugs", []);
        $this->showAuthLinks = (bool) config("navigation.{$websiteType}.show_auth_links", false);
        $this->showAccountInFooter = (bool) config("navigation.{$websiteType}.show_account_in_footer", true);
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
                $name => ucwords(str_replace(['.', '-', '_'], ' ', $name)),
            ])
            ->all();
    }

    public function openAddModal(): void
    {
        $this->resetAddForm();
        $this->showAddModal = true;
    }

    public function updatedNewPageRoute(string $value): void
    {
        if ($value !== '' && $this->newPageLabel === '') {
            $this->newPageLabel = ucwords(str_replace(['.', '-', '_'], ' ', $value));
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
        } else {
            $this->validate([
                'newCustomLabel' => ['required', 'string', 'max:255'],
                'newCustomUrl' => ['required', 'string', 'max:2048'],
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
        $this->editItemType = isset($item['route']) ? 'page' : 'custom';
        $this->editItemLabel = $item['label'] ?? '';
        $this->editItemRoute = $item['route'] ?? '';
        $this->editItemUrl = $item['url'] ?? '';
        $this->editItemNewWindow = $item['new_window'] ?? false;
        $this->showEditItemModal = true;
    }

    public function saveEditItem(): void
    {
        if ($this->editItemType === 'page') {
            $this->validate([
                'editItemRoute' => ['required', 'string'],
                'editItemLabel' => ['required', 'string', 'max:255'],
            ]);

            $item = [
                'label' => $this->editItemLabel,
                'route' => $this->editItemRoute,
                'active' => $this->menus[$this->activeMenuIndex]['items'][$this->editItemIndex]['active'] ?? true,
            ];
        } else {
            $this->validate([
                'editItemLabel' => ['required', 'string', 'max:255'],
                'editItemUrl' => ['required', 'string', 'max:2048'],
            ]);

            $item = [
                'label' => $this->editItemLabel,
                'url' => $this->editItemUrl,
                'active' => $this->menus[$this->activeMenuIndex]['items'][$this->editItemIndex]['active'] ?? true,
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
        $websiteType = config('features.website_type', 'saas');
        $config = config('navigation');
        $config[$websiteType]['show_auth_links'] = $this->showAuthLinks;
        $config[$websiteType]['show_account_in_footer'] = $this->showAccountInFooter;
        $config[$websiteType]['footer_slugs'] = array_values($this->footerSlugs);
        $config[$websiteType]['menus'] = array_values($this->menus);

        $configPath = config_path('navigation.php');
        file_put_contents($configPath, $this->buildConfigFileContents($config));

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($configPath, true);
        }

        config(['navigation' => $config]);

        $this->dispatch('notify', message: 'Menu saved.');
    }

    private function resetAddForm(): void
    {
        $this->addType = 'page';
        $this->newPageRoute = '';
        $this->newPageLabel = '';
        $this->newCustomUrl = '';
        $this->newCustomLabel = '';
        $this->newCustomNewWindow = false;
    }

    /** @param array<string, mixed> $config */
    private function buildConfigFileContents(array $config): string
    {
        $contents = "<?php\n\n";
        $contents .= "/*\n";
        $contents .= "|--------------------------------------------------------------------------\n";
        $contents .= "| Website Type Navigation\n";
        $contents .= "|--------------------------------------------------------------------------\n";
        $contents .= "|\n";
        $contents .= "| Defines the public navigation and footer links for each website type.\n";
        $contents .= "| Set WEBSITE_TYPE in your .env to activate the appropriate config.\n";
        $contents .= "|\n";
        $contents .= "| Each type has:\n";
        $contents .= "|   show_auth_links  - whether login/register/dashboard appear in the nav\n";
        $contents .= "|   footer_slugs     - slugs of menus rendered as footer columns (in order)\n";
        $contents .= "|   menus            - all menus; templates request them by slug\n";
        $contents .= "|\n";
        $contents .= "*/\n\n";
        $contents .= "return [\n\n";

        $typeBlocks = [];
        foreach ($config as $type => $data) {
            $typeBlocks[] = $this->formatTypeBlock((string) $type, $data);
        }

        $contents .= implode("\n\n", $typeBlocks);
        $contents .= "\n\n];\n";

        return $contents;
    }

    /**
     * @param array{
     *     show_auth_links: bool,
     *     show_account_in_footer: bool,
     *     footer_slugs: array<int, string>,
     *     menus: array<int, array{slug: string, label: string, items: array<int, array<string, mixed>>}>
     * } $data
     */
    private function formatTypeBlock(string $type, array $data): string
    {
        $showAuth = $data['show_auth_links'] ? 'true' : 'false';
        $showAccountInFooter = ($data['show_account_in_footer'] ?? true) ? 'true' : 'false';

        $footerSlugItems = array_map(
            fn (string $s): string => "'" . str_replace("'", "\\'", $s) . "'",
            $data['footer_slugs'] ?? [],
        );
        $footerSlugsLine = '        \'footer_slugs\' => [' . implode(', ', $footerSlugItems) . '],';

        $menuBlocks = [];
        foreach ($data['menus'] ?? [] as $menu) {
            $slug = str_replace("'", "\\'", $menu['slug']);
            $menuLabel = str_replace("'", "\\'", $menu['label']);

            $itemLines = array_map(
                fn (array $item): string => '                ' . $this->formatNavItem($item) . ',',
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
            "    '{$type}' => [",
            "        'show_auth_links' => {$showAuth},",
            "        'show_account_in_footer' => {$showAccountInFooter},",
            $footerSlugsLine,
            "        'menus' => [",
            ...$menuBlocks,
            "        ],",
            "    ],",
        ];

        return implode("\n", $lines);
    }

    /** @param array<string, mixed> $item */
    private function formatNavItem(array $item): string
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
            </flux:radio.group>

            @if ($editItemType === 'page')
                <flux:select wire:model.live="editItemRoute" :label="__('Page')">
                    <flux:select.option value="">{{ __('Select a page…') }}</flux:select.option>
                    @foreach ($this->availableRoutes as $routeName => $routeLabel)
                        <flux:select.option value="{{ $routeName }}">{{ $routeLabel }}</flux:select.option>
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
        <div class="max-w-2xl">
            <div class="mb-8">
                <flux:heading size="xl">{{ __('Menus') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Manage the navigation menus for your website.') }}</flux:text>
            </div>

            @if (empty($menus))
                <div class="rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600 py-20 text-center text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="bars-3" class="mx-auto mb-3 size-12 opacity-40" />
                    <p class="text-sm font-medium">{{ __('No menus yet.') }}</p>
                    <p class="mt-1 text-xs">{{ __('Create your first menu to get started.') }}</p>
                    <flux:button wire:click="$set('showCreateMenuModal', true)" variant="outline" size="sm" class="mt-4" icon="plus">
                        {{ __('Create Menu') }}
                    </flux:button>
                </div>
            @else
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <flux:select wire:model.live="activeMenuIndex" class="w-52">
                            @foreach ($menus as $i => $menu)
                                <flux:select.option value="{{ $i }}">{{ $menu['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:button
                            wire:click="$set('showCreateMenuModal', true)"
                            variant="ghost"
                            size="sm"
                            icon="plus"
                            title="{{ __('Create menu') }}"
                        />
                        <flux:button
                            wire:click="$set('showEditMenuModal', true)"
                            variant="ghost"
                            size="sm"
                            icon="pencil-square"
                            title="{{ __('Edit menu') }}"
                        />
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
                                                    @if (isset($item['route']))
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
                                                    <flux:button
                                                        wire:click="moveItemUp({{ $index }})"
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="chevron-up"
                                                        :disabled="$index === 0"
                                                    />
                                                    <flux:button
                                                        wire:click="moveItemDown({{ $index }})"
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="chevron-down"
                                                        :disabled="$index === count($currentItems) - 1"
                                                    />
                                                    <flux:switch wire:model.live="menus.{{ $activeMenuIndex }}.items.{{ $index }}.active" />
                                                    <flux:button
                                                        wire:click="openEditItemModal({{ $index }})"
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="pencil-square"
                                                    />
                                                    <flux:button
                                                        wire:click="removeItem({{ $index }})"
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="trash"
                                                        class="text-red-500 dark:text-red-400"
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endif

                {{-- Auth link settings --}}
                <div class="mt-8 space-y-3">
                    <flux:heading size="sm">{{ __('Auth links') }}</flux:heading>
                    <flux:switch wire:model.live="showAuthLinks" :label="__('Show sign in / register links in header nav')" />
                    <flux:switch wire:model.live="showAccountInFooter" :label="__('Show account column in footer')" />
                </div>

                {{-- Footer column order --}}
                @if (count($footerSlugs) > 1)
                    <div class="mt-8">
                        <div class="mb-3">
                            <flux:heading size="sm">{{ __('Footer column order') }}</flux:heading>
                            <flux:text size="sm" class="mt-0.5">{{ __('Drag to reorder how footer menus appear as columns.') }}</flux:text>
                        </div>
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
            @endif
        </div>
    </flux:main>
</div>
