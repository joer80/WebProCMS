<?php

use App\Models\ContentItem;
use App\Models\ContentTypeDefinition;
use App\Support\ContentTypePageGenerator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Content Types')] class extends Component {
    public function deleteType(int $id): void
    {
        $type = ContentTypeDefinition::query()->findOrFail($id);

        ContentItem::query()->where('type_slug', $type->slug)->delete();
        $type->delete();

        app(ContentTypePageGenerator::class)->remove($type->slug);

        $this->dispatch('notify', message: 'Content type deleted.');
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, ContentTypeDefinition> */
    public function getTypesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return ContentTypeDefinition::allOrdered();
    }
}; ?>

<div>
    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">Content Types</flux:heading>
                <flux:text class="mt-1">Define custom content structures for your site.</flux:text>
            </div>
            <flux:button href="{{ route('dashboard.content-types.create') }}" variant="primary" wire:navigate>
                New Content Type
            </flux:button>
        </div>

        @if ($this->types->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="rectangle-group" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">No content types yet. Create your first one!</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Name</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">Slug</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden md:table-cell">Fields</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->types as $type)
                            <tr wire:key="type-{{ $type->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:icon :name="$type->icon" class="size-4 text-zinc-400 shrink-0" />
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $type->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 font-mono text-xs hidden sm:table-cell">
                                    {{ $type->slug }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 hidden md:table-cell">
                                    {{ count($type->fields) }} {{ Str::plural('field', count($type->fields)) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            href="{{ route('dashboard.content-types.edit', $type->id) }}"
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil"
                                            wire:navigate
                                        />
                                        <flux:button
                                            wire:click="deleteType({{ $type->id }})"
                                            wire:confirm="Delete '{{ $type->name }}' and all its content? This cannot be undone."
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
    </flux:main>
</div>
