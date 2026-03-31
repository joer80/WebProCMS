<?php

use App\Models\Snippet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Snippets')] #[Lazy] class extends Component {
    public ?int $confirmingDelete = null;

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

    public function toggleActive(int $snippetId): void
    {
        $snippet = Snippet::query()->findOrFail($snippetId);
        $snippet->update(['is_active' => ! $snippet->is_active]);
    }

    public function deleteSnippet(int $snippetId): void
    {
        Snippet::query()->findOrFail($snippetId)->delete();

        $this->confirmingDelete = null;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Snippet> */
    public function getSnippetsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Snippet::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}; ?>

<div>
    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">Snippets</flux:heading>
                <flux:text class="mt-1">Inject HTML, JavaScript, or PHP code into public pages.</flux:text>
            </div>
            <flux:button href="{{ route('dashboard.snippets.create') }}" variant="primary" wire:navigate>
                New Snippet
            </flux:button>
        </div>

        @if ($this->snippets->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="code-bracket-square" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">No snippets yet. Create your first one!</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Name</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">Type</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden md:table-cell">Placement</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden lg:table-cell">Scope</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->snippets as $snippet)
                            <tr wire:key="snippet-{{ $snippet->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $snippet->name }}
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    <flux:badge variant="zinc" size="sm">{{ $snippet->type->label() }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 hidden md:table-cell">
                                    {{ $snippet->placement->label() }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 font-mono text-xs hidden lg:table-cell">
                                    {{ $snippet->page_path ? $snippet->page_path : 'All pages' }}
                                </td>
                                <td class="px-4 py-3">
                                    <button wire:click="toggleActive({{ $snippet->id }})" class="cursor-pointer" title="Toggle status">
                                        <flux:badge variant="{{ $snippet->is_active ? 'green' : 'zinc' }}" size="sm">
                                            {{ $snippet->is_active ? 'Active' : 'Inactive' }}
                                        </flux:badge>
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:tooltip content="Edit snippet" position="bottom">
                                            <flux:button
                                                href="{{ route('dashboard.snippets.edit', $snippet) }}"
                                                variant="ghost"
                                                size="sm"
                                                icon="pencil"
                                                wire:navigate
                                            />
                                        </flux:tooltip>
                                        @if ($confirmingDelete === $snippet->id)
                                            <div class="flex items-center gap-1">
                                                <flux:button wire:click="deleteSnippet({{ $snippet->id }})" variant="danger" size="sm">
                                                    Confirm
                                                </flux:button>
                                                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">
                                                    Cancel
                                                </flux:button>
                                            </div>
                                        @else
                                            <flux:tooltip content="Delete snippet" position="bottom">
                                                <flux:button
                                                    wire:click="$set('confirmingDelete', {{ $snippet->id }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="trash"
                                                    class="text-red-500 dark:text-red-400"
                                                />
                                            </flux:tooltip>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </flux:main>
</div>
