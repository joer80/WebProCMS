<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Categories')] class extends Component {
    public ?int $confirmingDelete = null;

    public function deleteCategory(int $categoryId): void
    {
        Category::query()->findOrFail($categoryId)->delete();

        $this->confirmingDelete = null;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Category> */
    public function getCategoriesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::query()
            ->withCount('posts')
            ->orderBy('name')
            ->get();
    }
}; ?>

<div>
    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">Categories</flux:heading>
                <flux:text class="mt-1">Organise your blog posts into categories.</flux:text>
            </div>
            <flux:button href="{{ route('dashboard.categories.create') }}" variant="primary" wire:navigate>
                New Category
            </flux:button>
        </div>

        @if ($this->categories->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="tag" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">No categories yet. Create your first one!</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Name</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">Slug</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Posts</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->categories as $category)
                            <tr wire:key="category-{{ $category->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $category->name }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 font-mono text-xs hidden sm:table-cell">
                                    {{ $category->slug }}
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                    {{ $category->posts_count }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button
                                            href="{{ route('dashboard.categories.edit', $category) }}"
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil"
                                            wire:navigate
                                        />
                                        @if ($confirmingDelete === $category->id)
                                            <div class="flex items-center gap-1">
                                                <flux:button wire:click="deleteCategory({{ $category->id }})" variant="danger" size="sm">
                                                    Confirm
                                                </flux:button>
                                                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">
                                                    Cancel
                                                </flux:button>
                                            </div>
                                        @else
                                            <flux:button
                                                wire:click="$set('confirmingDelete', {{ $category->id }})"
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
