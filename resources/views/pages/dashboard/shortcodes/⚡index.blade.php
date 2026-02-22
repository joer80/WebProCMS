<?php

use App\Models\Shortcode;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Shortcodes')] #[Lazy] class extends Component {
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

    public ?int $confirmingDelete = null;

    public function toggleActive(int $shortcodeId): void
    {
        $shortcode = Shortcode::query()->findOrFail($shortcodeId);
        $shortcode->update(['is_active' => ! $shortcode->is_active]);
    }

    public function deleteShortcode(int $shortcodeId): void
    {
        Shortcode::query()->findOrFail($shortcodeId)->delete();

        $this->confirmingDelete = null;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Shortcode> */
    public function getShortcodesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Shortcode::query()
            ->orderBy('name')
            ->get();
    }
}; ?>

<div>
    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">Shortcodes</flux:heading>
                <flux:text class="mt-1">Reusable content snippets you can embed anywhere with <code class="font-mono text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">[[tag]]</code> syntax.</flux:text>
            </div>
            <flux:button href="{{ route('dashboard.shortcodes.create') }}" variant="primary" wire:navigate>
                New Shortcode
            </flux:button>
        </div>

        @if ($this->shortcodes->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="code-bracket" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">No shortcodes yet. Create your first one!</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Name</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">Tag</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden md:table-cell">Type</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden lg:table-cell">Modified</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->shortcodes as $shortcode)
                            <tr wire:key="shortcode-{{ $shortcode->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $shortcode->name }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 font-mono text-xs hidden sm:table-cell">
                                    [[{{ $shortcode->tag }}]]
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 hidden md:table-cell">
                                    {{ $shortcode->typeLabel() }}
                                </td>
                                <td class="px-4 py-3">
                                    <button
                                        wire:click="toggleActive({{ $shortcode->id }})"
                                        class="cursor-pointer"
                                        title="Toggle status"
                                    >
                                        <flux:badge
                                            variant="{{ $shortcode->is_active ? 'green' : 'zinc' }}"
                                            size="sm"
                                        >
                                            {{ $shortcode->is_active ? 'Active' : 'Inactive' }}
                                        </flux:badge>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs hidden lg:table-cell">
                                    {{ $shortcode->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            href="{{ route('dashboard.shortcodes.edit', $shortcode) }}"
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil"
                                            wire:navigate
                                        />
                                        @if ($confirmingDelete === $shortcode->id)
                                            <div class="flex items-center gap-1">
                                                <flux:button wire:click="deleteShortcode({{ $shortcode->id }})" variant="danger" size="sm">
                                                    Confirm
                                                </flux:button>
                                                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">
                                                    Cancel
                                                </flux:button>
                                            </div>
                                        @else
                                            <flux:button
                                                wire:click="$set('confirmingDelete', {{ $shortcode->id }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="trash"
                                                class="text-red-500 dark:text-red-400"
                                            />
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
