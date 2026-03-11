<?php

use App\Models\ContentItem;
use App\Models\ContentTypeDefinition;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Content')] class extends Component {
    use WithPagination;

    public string $typeSlug = '';

    public ?ContentTypeDefinition $typeDef = null;

    public function mount(string $typeSlug): void
    {
        $this->typeSlug = $typeSlug;
        $this->typeDef = ContentTypeDefinition::where('slug', $typeSlug)->firstOrFail();
    }

    public function deleteItem(int $id): void
    {
        ContentItem::query()->findOrFail($id)->delete();

        $this->dispatch('notify', message: 'Item deleted.');
    }

    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<ContentItem> */
    public function getItemsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return ContentItem::query()
            ->where('type_slug', $this->typeSlug)
            ->latest()
            ->paginate(20);
    }
}; ?>

<div>
    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">{{ $typeDef->name }}</flux:heading>
                <flux:text class="mt-1">Manage your {{ Str::lower($typeDef->name) }} content.</flux:text>
            </div>
            <flux:button href="{{ route('dashboard.content.create', $typeSlug) }}" variant="primary" wire:navigate>
                New {{ $typeDef->singular }}
            </flux:button>
        </div>

        @if ($this->items->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon :name="$typeDef->icon" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">No {{ Str::lower($typeDef->name) }} yet. Create your first one!</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Title</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden md:table-cell">Status</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden lg:table-cell">Created</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->items as $item)
                            <tr wire:key="item-{{ $item->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <a href="{{ route('dashboard.content.edit', [$typeSlug, $item->id]) }}" wire:navigate class="font-medium text-zinc-900 dark:text-zinc-100 hover:text-primary transition-colors">
                                        {{ Str::limit($item->displayTitle(), 60) }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <flux:badge variant="{{ $item->status === 'published' ? 'green' : 'zinc' }}" size="sm">
                                        {{ ucfirst($item->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs hidden lg:table-cell">
                                    {{ $item->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @if (Route::has($typeSlug . '.show'))
                                            <flux:button
                                                href="{{ route($typeSlug . '.show', $item->id) }}"
                                                variant="ghost"
                                                size="sm"
                                                icon="arrow-top-right-on-square"
                                                target="_blank"
                                            />
                                        @endif
                                        <flux:button
                                            href="{{ route('dashboard.content.edit', [$typeSlug, $item->id]) }}"
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil"
                                            wire:navigate
                                        />
                                        <flux:button
                                            wire:click="deleteItem({{ $item->id }})"
                                            wire:confirm="Delete this item? This cannot be undone."
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

            <div class="mt-6">
                {{ $this->items->links() }}
            </div>
        @endif
    </flux:main>
</div>
