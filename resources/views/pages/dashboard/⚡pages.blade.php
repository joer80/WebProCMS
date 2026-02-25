<?php

use App\Enums\Role;
use App\Support\VoltFileService;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Pages')] #[Lazy] class extends Component {
    public bool $showAll = false;

    public string $search = '';

    public bool $showCloneModal = false;

    public string $cloneSourcePath = '';

    public string $cloneName = '';

    public string $cloneSlug = '';

    public string $confirmingDeletePath = '';

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="flex items-center justify-center py-32">
            <svg class="animate-spin size-8 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        HTML;
    }

    /** @return array<string, array<string, string>> */
    #[Computed]
    public function voltFiles(): array
    {
        $service = new VoltFileService;
        $files = $service->listVoltFiles();

        unset($files['Other'], $files['Dashboard']);

        if (! $this->showAll) {
            $currentType = config('features.website_type', 'saas');
            $typeMap = $service->buildPageTypeMap();

            $files['Public Pages'] = array_filter(
                $files['Public Pages'] ?? [],
                function (string $path) use ($typeMap, $currentType): bool {
                    $types = $typeMap[$path] ?? [];

                    return empty($types) || in_array($currentType, $types, true);
                },
            );

            $files = array_filter($files);
        }

        if ($this->search !== '') {
            $term = strtolower($this->search);

            $files = array_map(
                fn (array $group) => array_filter(
                    $group,
                    fn (string $path, string $label) => str_contains(strtolower($label), $term)
                        || str_contains(strtolower($path), $term),
                    ARRAY_FILTER_USE_BOTH,
                ),
                $files,
            );

            $files = array_filter($files);
        }

        return $files;
    }

    public function openCloneModal(string $path, string $label): void
    {
        $this->cloneSourcePath = $path;
        $this->cloneName = $label.' Copy';
        $this->cloneSlug = Str::slug($label).'-copy';
        $this->showCloneModal = true;
    }

    public function updatedCloneName(string $value): void
    {
        $this->cloneSlug = Str::slug($value);
    }

    public function clonePage(): void
    {
        abort_if(! auth()->user()->isAtLeast(Role::Manager), 403);

        $this->validate([
            'cloneName' => ['required', 'string', 'max:100'],
            'cloneSlug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ]);

        $relativePath = 'pages/⚡'.$this->cloneSlug.'.blade.php';

        if (file_exists(resource_path('views/'.$relativePath))) {
            $this->addError('cloneSlug', 'A page with this slug already exists.');

            return;
        }

        (new VoltFileService)->clonePage($this->cloneSlug, $this->cloneName, $this->cloneSourcePath);

        $this->showCloneModal = false;
        $this->cloneSourcePath = '';
        $this->cloneName = '';
        $this->cloneSlug = '';
    }

    public function confirmDelete(string $path): void
    {
        $this->confirmingDeletePath = $path;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeletePath = '';
    }

    public function deletePage(string $path): void
    {
        abort_if(! auth()->user()->isAtLeast(Role::Manager), 403);

        (new VoltFileService)->deletePage($path);

        $this->confirmingDeletePath = '';
    }

    public function getPageUrl(string $path): string
    {
        return (new VoltFileService)->getRouteForFile($path);
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <flux:heading size="xl">Pages</flux:heading>
                <flux:text class="mt-1">Browse and edit your website pages.</flux:text>
            </div>
            <flux:switch wire:model.live="showAll" label="Show all" />
        </div>

        <div class="mb-6">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search pages…" icon="magnifying-glass" clearable />
        </div>

        @if (auth()->user()->isAtLeast(Role::Manager))
            <flux:modal wire:model="showCloneModal" class="w-full max-w-md">
                <flux:heading size="lg" class="mb-1">Clone Page</flux:heading>
                <flux:text class="mb-5 text-zinc-500">Create a copy of this page with a new name and URL.</flux:text>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Page Name</flux:label>
                        <flux:input wire:model.live="cloneName" placeholder="e.g. Our Team" />
                        <flux:error name="cloneName" />
                    </flux:field>

                    <flux:field>
                        <flux:label>URL Slug</flux:label>
                        <flux:input wire:model="cloneSlug" placeholder="e.g. our-team" />
                        <flux:description>The URL path for this page: /{{ $cloneSlug ?: 'slug' }}</flux:description>
                        <flux:error name="cloneSlug" />
                    </flux:field>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <flux:button wire:click="$set('showCloneModal', false)" variant="ghost">Cancel</flux:button>
                    <flux:button wire:click="clonePage" variant="primary" icon="document-duplicate">Clone Page</flux:button>
                </div>
            </flux:modal>
        @endif

        @forelse ($this->voltFiles as $section => $files)
            <div class="mb-8">
                @if (count($this->voltFiles) > 1)
                    <flux:heading size="sm" class="mb-3 text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-xs">
                        {{ $section }}
                    </flux:heading>
                @endif

                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($files as $label => $path)
                                <tr wire:key="page-{{ md5($path) }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $label }}</div>
                                        <div class="text-xs text-zinc-400 dark:text-zinc-500 font-mono mt-0.5">{{ $path }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($confirmingDeletePath === $path)
                                                <flux:text size="sm" class="text-red-600 dark:text-red-400">Delete this page?</flux:text>
                                                <flux:button wire:click="deletePage('{{ $path }}')" variant="danger" size="sm">Confirm</flux:button>
                                                <flux:button wire:click="cancelDelete" variant="ghost" size="sm">Cancel</flux:button>
                                            @else
                                                <flux:button
                                                    href="{{ route('dashboard.design-library.editor') . '?file=' . urlencode($path) }}"
                                                    variant="outline"
                                                    size="sm"
                                                    icon="pencil-square"
                                                >
                                                    Edit
                                                </flux:button>
                                                <flux:button
                                                    href="{{ $this->getPageUrl($path) }}"
                                                    variant="outline"
                                                    size="sm"
                                                    icon="eye"
                                                    target="_blank"
                                                />
                                                @if (auth()->user()->isAtLeast(Role::Manager))
                                                    <flux:button wire:click="openCloneModal('{{ $path }}', '{{ $label }}')" variant="outline" size="sm" icon="document-duplicate" />
                                                    <flux:button wire:click="confirmDelete('{{ $path }}')" variant="outline" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-zinc-400 dark:text-zinc-500">
                <flux:icon name="document" class="size-12 mx-auto mb-3 opacity-40" />
                <p class="text-sm">
                    @if ($search !== '')
                        No pages match "{{ $search }}".
                    @else
                        No pages found.
                    @endif
                </p>
            </div>
        @endforelse
    </flux:main>
</div>
