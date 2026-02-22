<?php

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Blog Posts')] #[Lazy] class extends Component {
    use WithPagination;

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

    public function deletePost(int $postId): void
    {
        Post::query()->findOrFail($postId)->delete();

        $this->confirmingDelete = null;
    }

    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<Post> */
    public function getPostsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Post::query()
            ->with('category')
            ->latest()
            ->paginate(15);
    }
}; ?>

<div>
    <flux:main>
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">Blog Posts</flux:heading>
                <flux:text class="mt-1">Manage your blog content.</flux:text>
            </div>
            <flux:button href="{{ route('dashboard.blog.create') }}" variant="primary" wire:navigate>
                New Post
            </flux:button>
        </div>

        @if ($this->posts->isEmpty())
            <div class="text-center py-16 text-zinc-500 dark:text-zinc-400">
                <flux:icon name="document-text" class="size-12 mx-auto mb-4 opacity-40" />
                <p class="text-sm">No posts yet. Create your first one!</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Title</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">Category</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden md:table-cell">Status</th>
                            <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden lg:table-cell">Date</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->posts as $post)
                            <tr wire:key="post-{{ $post->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $post->title }}</div>
                                    @if ($post->excerpt)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate max-w-xs mt-0.5">{{ $post->excerpt }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">
                                    {{ $post->category?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <flux:badge variant="{{ $post->status === 'published' ? 'green' : 'zinc' }}" size="sm">
                                        {{ ucfirst($post->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs hidden lg:table-cell">
                                    {{ $post->published_at?->format('M j, Y') ?? $post->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($post->status === 'published')
                                            <flux:button
                                                href="{{ route('blog.show', $post->slug) }}"
                                                variant="ghost"
                                                size="sm"
                                                icon="arrow-top-right-on-square"
                                                target="_blank"
                                            />
                                        @endif
                                        <flux:button
                                            href="{{ route('dashboard.blog.edit', $post) }}"
                                            variant="ghost"
                                            size="sm"
                                            icon="pencil"
                                            wire:navigate
                                        />
                                        @if ($confirmingDelete === $post->id)
                                            <div class="flex items-center gap-1">
                                                <flux:button wire:click="deletePost({{ $post->id }})" variant="danger" size="sm">
                                                    Confirm
                                                </flux:button>
                                                <flux:button wire:click="$set('confirmingDelete', null)" variant="ghost" size="sm">
                                                    Cancel
                                                </flux:button>
                                            </div>
                                        @else
                                            <flux:button
                                                wire:click="$set('confirmingDelete', {{ $post->id }})"
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

            <div class="mt-6">
                {{ $this->posts->links() }}
            </div>
        @endif
    </flux:main>
</div>
