<?php

use Illuminate\Support\Facades\Route as RoutesFacade;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Menus')] class extends Component {
    /** @var array<int, array<string, mixed>> */
    public array $navItems = [];

    /** @var array<int, array<string, mixed>> */
    public array $footerItems = [];

    public bool $showAddModal = false;
    public string $addTarget = 'nav';
    public string $addType = 'page';
    public string $newPageRoute = '';
    public string $newPageLabel = '';
    public string $newCustomUrl = '';
    public string $newCustomLabel = '';
    public bool $newCustomNewWindow = false;

    public function mount(): void
    {
        $websiteType = config('features.website_type', 'saas');
        $this->navItems = config("navigation.{$websiteType}.nav", []);
        $this->footerItems = config("navigation.{$websiteType}.footer_company", []);
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

    public function openAddModal(string $target = 'nav'): void
    {
        $this->addTarget = $target;
        $this->resetAddForm();
        $this->showAddModal = true;
    }

    public function updatedNewPageRoute(string $value): void
    {
        if ($value !== '' && $this->newPageLabel === '') {
            $this->newPageLabel = ucwords(str_replace(['.', '-', '_'], ' ', $value));
        }
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

            if ($this->addTarget === 'footer') {
                $this->footerItems[] = $item;
            } else {
                $this->navItems[] = $item;
            }
        } else {
            $this->validate([
                'newCustomLabel' => ['required', 'string', 'max:255'],
                'newCustomUrl' => ['required', 'url'],
            ]);

            $item = [
                'label' => $this->newCustomLabel,
                'url' => $this->newCustomUrl,
                'active' => true,
            ];

            if ($this->newCustomNewWindow) {
                $item['new_window'] = true;
            }

            if ($this->addTarget === 'footer') {
                $this->footerItems[] = $item;
            } else {
                $this->navItems[] = $item;
            }
        }

        $this->showAddModal = false;
        $this->resetAddForm();
    }

    public function removeItem(int $index): void
    {
        array_splice($this->navItems, $index, 1);
    }

    public function moveUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        $items = $this->navItems;
        [$items[$index - 1], $items[$index]] = [$items[$index], $items[$index - 1]];
        $this->navItems = array_values($items);
    }

    public function moveDown(int $index): void
    {
        if ($index >= count($this->navItems) - 1) {
            return;
        }

        $items = $this->navItems;
        [$items[$index], $items[$index + 1]] = [$items[$index + 1], $items[$index]];
        $this->navItems = array_values($items);
    }

    public function removeFooterItem(int $index): void
    {
        array_splice($this->footerItems, $index, 1);
    }

    public function moveFooterUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        $items = $this->footerItems;
        [$items[$index - 1], $items[$index]] = [$items[$index], $items[$index - 1]];
        $this->footerItems = array_values($items);
    }

    public function moveFooterDown(int $index): void
    {
        if ($index >= count($this->footerItems) - 1) {
            return;
        }

        $items = $this->footerItems;
        [$items[$index], $items[$index + 1]] = [$items[$index + 1], $items[$index]];
        $this->footerItems = array_values($items);
    }

    public function save(): void
    {
        $websiteType = config('features.website_type', 'saas');
        $config = config('navigation');
        $config[$websiteType]['nav'] = array_values($this->navItems);
        $config[$websiteType]['footer_company'] = array_values($this->footerItems);

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
        $contents .= "|   nav              - primary navigation items (always shown)\n";
        $contents .= "|   show_auth_links  - whether login/register/dashboard appear in the nav\n";
        $contents .= "|   footer_company   - links shown in the footer \"Company\" column\n";
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
     *     nav: array<int, array<string, mixed>>,
     *     show_auth_links: bool,
     *     footer_company: array<int, array<string, mixed>>
     * } $data
     */
    private function formatTypeBlock(string $type, array $data): string
    {
        $showAuth = $data['show_auth_links'] ? 'true' : 'false';

        $navLines = array_map(
            fn (array $item): string => '            ' . $this->formatNavItem($item) . ',',
            $data['nav'],
        );

        $footerLines = array_map(
            fn (array $item): string => '            ' . $this->formatNavItem($item) . ',',
            $data['footer_company'],
        );

        $lines = [
            "    '{$type}' => [",
            "        'nav' => [",
            ...$navLines,
            "        ],",
            "        'show_auth_links' => {$showAuth},",
            "        'footer_company' => [",
            ...$footerLines,
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
                    type="url"
                    placeholder="https://…"
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
            <flux:text class="mt-1">{{ __('Manage the main navigation for your website.') }}</flux:text>
        </div>

        <div>
            <div class="mb-4 flex items-center justify-between">
                <flux:heading>{{ __('Main Navigation') }}</flux:heading>
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

            @if (empty($navItems))
                <div class="rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600 py-16 text-center text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="bars-3" class="mx-auto mb-3 size-12 opacity-40" />
                    <p class="text-sm">{{ __('No navigation items yet.') }}</p>
                    <flux:button wire:click="openAddModal" variant="outline" size="sm" class="mt-4">
                        {{ __('Add your first item') }}
                    </flux:button>
                </div>
            @else
                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($navItems as $index => $item)
                                <tr wire:key="nav-item-{{ $index }}" class="bg-white dark:bg-zinc-900">
                                    <td class="w-8 px-4 py-3 text-zinc-400 dark:text-zinc-500">
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
                                                wire:click="moveUp({{ $index }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="chevron-up"
                                                :disabled="$index === 0"
                                            />
                                            <flux:button
                                                wire:click="moveDown({{ $index }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="chevron-down"
                                                :disabled="$index === count($navItems) - 1"
                                            />
                                            <flux:switch wire:model.live="navItems.{{ $index }}.active" />
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
        </div>

        <div class="mt-10">
            <div class="mb-4 flex items-center justify-between">
                <flux:heading>{{ __('Footer Links') }}</flux:heading>
                <div class="flex items-center gap-2">
                    <flux:button wire:click="openAddModal('footer')" variant="outline" size="sm" icon="plus">
                        {{ __('Add Item') }}
                    </flux:button>
                    <flux:button wire:click="save" variant="primary" size="sm" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                        <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                    </flux:button>
                </div>
            </div>

            @if (empty($footerItems))
                <div class="rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600 py-16 text-center text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="bars-3" class="mx-auto mb-3 size-12 opacity-40" />
                    <p class="text-sm">{{ __('No footer links yet.') }}</p>
                    <flux:button wire:click="openAddModal('footer')" variant="outline" size="sm" class="mt-4">
                        {{ __('Add your first item') }}
                    </flux:button>
                </div>
            @else
                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($footerItems as $index => $item)
                                <tr wire:key="footer-item-{{ $index }}" class="bg-white dark:bg-zinc-900">
                                    <td class="w-8 px-4 py-3 text-zinc-400 dark:text-zinc-500">
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
                                                wire:click="moveFooterUp({{ $index }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="chevron-up"
                                                :disabled="$index === 0"
                                            />
                                            <flux:button
                                                wire:click="moveFooterDown({{ $index }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="chevron-down"
                                                :disabled="$index === count($footerItems) - 1"
                                            />
                                            <flux:switch wire:model.live="footerItems.{{ $index }}.active" />
                                            <flux:button
                                                wire:click="removeFooterItem({{ $index }})"
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
        </div>
        </div>
    </flux:main>
</div>
