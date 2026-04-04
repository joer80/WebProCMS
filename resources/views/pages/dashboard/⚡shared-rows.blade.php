<?php

use App\Models\SharedRow;
use App\Support\VoltFileService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Shared Rows')] class extends Component {
    public string $pendingDeleteSlug = '';

    #[Computed]
    public function sharedRows(): \Illuminate\Support\Collection
    {
        return SharedRow::query()->orderBy('name')->get();
    }

    /**
     * Scan all page files once and return a map of shared row slug → pages using it.
     *
     * @return array<string, list<array{name: string, url: string}>>
     */
    #[Computed]
    public function sharedRowPageMap(): array
    {
        $pagesDir = resource_path('views/pages');
        $files = array_merge(
            glob($pagesDir.'/⚡*.blade.php') ?: [],
            glob($pagesDir.'/***/⚡*.blade.php') ?: [],
        );

        $map = [];

        foreach ($files as $file) {
            $contents = (string) file_get_contents($file);
            $relativePath = str_replace(resource_path('views').'/', '', $file);
            $editUrl = route('dashboard.design-library.editor').'?file='.urlencode($relativePath);

            // Derive a human-readable page name from the file path.
            $name = preg_replace('#^pages/#', '', $relativePath);
            $name = str_replace(['.blade.php', '⚡', '-', '_'], ['', '', ' ', ' '], $name);
            $name = implode(' / ', array_map('ucwords', array_filter(explode('/', $name))));

            foreach ($this->sharedRows as $sharedRow) {
                if (str_contains($contents, "ROW:start:{$sharedRow->slug}")) {
                    $map[$sharedRow->slug][] = ['name' => $name, 'url' => $editUrl];
                }
            }
        }

        return $map;
    }

    /**
     * Create a wrapper file for an orphaned shared row and open it in the editor.
     */
    public function openSharedRowEditor(string $slug): void
    {
        $sharedRow = SharedRow::query()->where('slug', $slug)->firstOrFail();
        $filename = str_replace(':', '-', $slug);
        $wrapperDir = resource_path('views/shared-row-wrappers');

        if (! is_dir($wrapperDir)) {
            mkdir($wrapperDir, 0755, true);
        }

        $content = (new VoltFileService)->buildFileContent('<?php ' . '?' . '>', [[
            'slug' => $slug,
            'name' => $sharedRow->name,
            'blade' => "@include('shared-rows.{$filename}')",
            'shared' => true,
            'hidden' => false,
        ]]);

        file_put_contents("{$wrapperDir}/{$filename}.blade.php", $content);

        $this->redirect(
            route('dashboard.design-library.editor').'?file='.urlencode("shared-row-wrappers/{$filename}.blade.php"),
            navigate: true
        );
    }

    public function confirmDeleteSharedRow(string $slug): void
    {
        $this->pendingDeleteSlug = $slug;
        $this->dispatch('open-modal', name: 'confirm-delete-shared-row');
    }

    public function deleteSharedRow(): void
    {
        $slug = $this->pendingDeleteSlug;

        if (! $slug) {
            return;
        }

        $service = new VoltFileService;
        $pagesDir = resource_path('views/pages');
        $files = array_merge(
            glob($pagesDir.'/⚡*.blade.php') ?: [],
            glob($pagesDir.'/***/⚡*.blade.php') ?: [],
        );

        foreach ($files as $file) {
            $contents = (string) file_get_contents($file);

            if (! str_contains($contents, "ROW:start:{$slug}")) {
                continue;
            }

            $parsed = $service->parseFile($file);
            $parsed['rows'] = array_values(array_filter($parsed['rows'], fn (array $r) => $r['slug'] !== $slug));
            $service->writeFile($file, $service->buildFileContent($parsed['phpSection'], $parsed['rows']));
        }

        // Also remove the wrapper file if one was created.
        $filename = str_replace(':', '-', $slug);
        $wrapperFile = resource_path('views/shared-row-wrappers/'.$filename.'.blade.php');

        if (file_exists($wrapperFile)) {
            unlink($wrapperFile);
        }

        $service->deleteSharedRow($slug);

        $this->pendingDeleteSlug = '';
        unset($this->sharedRows, $this->sharedRowPageMap);
        $this->dispatch('notify', message: 'Shared row deleted.');
    }
}; ?>

<div>
<flux:main>
    <div class="mb-8">
        <flux:heading size="xl">Shared Rows</flux:heading>
        <flux:text class="mt-1">Rows shared across multiple pages. Edit or delete them here.</flux:text>
    </div>

    @if($this->sharedRows->isEmpty())
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-8 text-center">
            <flux:heading class="mb-2">No shared rows yet</flux:heading>
            <flux:text>Shared rows are created in the page editor by right-clicking a row and selecting "Make Shared". They appear here once created.</flux:text>
        </div>
    @else
        <div class="max-w-3xl space-y-3">
            @foreach($this->sharedRows as $sharedRow)
                @php $pages = $this->sharedRowPageMap[$sharedRow->slug] ?? []; @endphp
                <div x-data="{ open: false }" class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div class="p-4 flex items-center justify-between gap-4">
                        <div class="min-w-0 flex items-center gap-3">
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $sharedRow->name }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-mono">{{ $sharedRow->slug }}</p>
                            </div>
                            @if(count($pages) > 0)
                                <button type="button" @click="open = !open"
                                    class="shrink-0 text-xs font-medium px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600 transition-colors">
                                    {{ count($pages) }} {{ Str::plural('page', count($pages)) }}
                                </button>
                            @else
                                <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-400 dark:text-zinc-500">
                                    0 pages
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if(count($pages) > 0)
                                <flux:button :href="$pages[0]['url']" variant="outline" size="sm" icon="pencil-square" wire:navigate>
                                    Edit
                                </flux:button>
                            @else
                                <flux:button variant="outline" size="sm" icon="pencil-square" wire:click="openSharedRowEditor('{{ $sharedRow->slug }}')">
                                    Edit
                                </flux:button>
                            @endif
                            <flux:button variant="outline" size="sm" icon="trash" icon-variant="outline"
                                wire:click="confirmDeleteSharedRow('{{ $sharedRow->slug }}')"
                                class="text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300" />
                        </div>
                    </div>
                    @if(count($pages) > 0)
                        <div x-show="open" x-collapse class="border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 px-4 py-2 space-y-1">
                            @foreach($pages as $page)
                                <a href="{{ $page['url'] }}" wire:navigate
                                    class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 hover:text-primary dark:hover:text-primary py-1 transition-colors">
                                    <flux:icon name="document-text" class="size-3.5 shrink-0 text-zinc-400" />
                                    {{ $page['name'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</flux:main>

<flux:modal name="confirm-delete-shared-row" class="w-full max-w-sm">
    <flux:heading size="lg">Delete shared row?</flux:heading>
    @if(count($this->sharedRowPageMap[$pendingDeleteSlug] ?? []) > 0)
        <flux:text class="mt-2">
            This row is used on {{ count($this->sharedRowPageMap[$pendingDeleteSlug]) }} {{ Str::plural('page', count($this->sharedRowPageMap[$pendingDeleteSlug])) }}. It will be removed from all of them and cannot be undone.
        </flux:text>
        <ul class="mt-3 space-y-1">
            @foreach($this->sharedRowPageMap[$pendingDeleteSlug] ?? [] as $page)
                <li class="text-sm text-zinc-600 dark:text-zinc-400 flex items-center gap-2">
                    <flux:icon name="document-text" class="size-3.5 shrink-0 text-zinc-400" />
                    {{ $page['name'] }}
                </li>
            @endforeach
        </ul>
    @else
        <flux:text class="mt-2">This shared row is not used on any pages. This cannot be undone.</flux:text>
    @endif
    <div class="mt-6 flex justify-end gap-3">
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>
        <flux:modal.close>
            <flux:button variant="danger" wire:click="deleteSharedRow">Delete</flux:button>
        </flux:modal.close>
    </div>
</flux:modal>
</div>
