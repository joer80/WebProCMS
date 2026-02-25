<?php

use App\Models\Category;
use App\Models\User;
use App\Models\Post;
use App\Support\VoltFileService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Dashboard')] #[Lazy] class extends Component {
    public bool $showNewPageModal = false;

    public string $newPageName = '';

    public string $newPageSlug = '';

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

    /** @return array<string, int> */
    public function getPostStatsProperty(): array
    {
        $counts = Post::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total' => array_sum($counts),
            'published' => $counts['published'] ?? 0,
            'unpublished' => ($counts['unpublished'] ?? 0) + ($counts['draft'] ?? 0),
            'unlisted' => $counts['unlisted'] ?? 0,
        ];
    }

    public function getUserCountProperty(): int
    {
        return User::query()->count();
    }

    public function getPageCountProperty(): int
    {
        $files = (new VoltFileService)->listVoltFiles();

        return count($files['Public Pages'] ?? []);
    }

    public function updatedNewPageName(string $value): void
    {
        $this->newPageSlug = Str::slug($value);
    }

    public function createPage(): void
    {
        $this->validate([
            'newPageName' => ['required', 'string', 'max:100'],
            'newPageSlug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ]);

        $relativePath = 'pages/⚡'.$this->newPageSlug.'.blade.php';

        if (file_exists(resource_path('views/'.$relativePath))) {
            $this->addError('newPageSlug', 'A page with this slug already exists.');

            return;
        }

        (new VoltFileService)->createPage($this->newPageSlug, $this->newPageName);

        $this->showNewPageModal = false;
        $this->newPageName = '';
        $this->newPageSlug = '';

        $this->redirect(
            route('dashboard.design-library.editor').'?file='.urlencode($relativePath),
            navigate: true,
        );
    }

    public function getCategoryCountProperty(): int
    {
        return Category::query()->count();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Post> */
    public function getRecentPostsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Post::query()
            ->with('category')
            ->latest()
            ->limit(5)
            ->get();
    }

    /**
     * @return list<array{label: string, path: string, modified_at: \Illuminate\Support\Carbon}>
     */
    public function getRecentlyUpdatedPagesProperty(): array
    {
        $publicPages = (new VoltFileService)->listVoltFiles()['Public Pages'] ?? [];
        $results = [];

        foreach ($publicPages as $label => $relativePath) {
            if (str_ends_with($relativePath, 'pages/⚡dashboard.blade.php')) {
                continue;
            }

            $fullPath = resource_path('views/'.$relativePath);

            if (! file_exists($fullPath)) {
                continue;
            }

            $results[] = [
                'label' => $label,
                'path' => $relativePath,
                'modified_at' => \Illuminate\Support\Carbon::createFromTimestamp(filemtime($fullPath)),
            ];
        }

        usort($results, fn (array $a, array $b): int => $b['modified_at']->timestamp <=> $a['modified_at']->timestamp);

        return array_slice($results, 0, 5);
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Post> */
    public function getDraftPostsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Post::query()
            ->whereIn('status', ['draft', 'unpublished'])
            ->latest()
            ->limit(5)
            ->get();
    }
}; ?>

<div>
    <flux:main>
        {{-- Header --}}
        <div class="mb-8">
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:text class="mt-1">Welcome back, {{ auth()->user()->name }}.</flux:text>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon name="document" class="size-4 text-zinc-400 dark:text-zinc-500" />
                    <flux:text size="sm" class="font-medium text-zinc-600 dark:text-zinc-400">Pages</flux:text>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100 mb-3">{{ $this->pageCount }}</div>
                <a href="{{ route('dashboard.pages') }}" class="text-xs text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200" wire:navigate>See all →</a>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon name="document-text" class="size-4 text-zinc-400 dark:text-zinc-500" />
                    <flux:text size="sm" class="font-medium text-zinc-600 dark:text-zinc-400">Posts</flux:text>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100 mb-3">{{ $this->postStats['total'] }}</div>
                <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="flex items-center gap-1">
                        <span class="size-1.5 rounded-full bg-green-500 inline-block"></span>
                        {{ $this->postStats['published'] }} published
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="size-1.5 rounded-full bg-zinc-400 inline-block"></span>
                        {{ $this->postStats['unpublished'] }} draft
                    </span>
                    @if ($this->postStats['unlisted'] > 0)
                        <span class="flex items-center gap-1">
                            <span class="size-1.5 rounded-full bg-blue-400 inline-block"></span>
                            {{ $this->postStats['unlisted'] }} unlisted
                        </span>
                    @endif
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon name="tag" class="size-4 text-zinc-400 dark:text-zinc-500" />
                    <flux:text size="sm" class="font-medium text-zinc-600 dark:text-zinc-400">Blog Categories</flux:text>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->categoryCount }}</div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <flux:icon name="users" class="size-4 text-zinc-400 dark:text-zinc-500" />
                    <flux:text size="sm" class="font-medium text-zinc-600 dark:text-zinc-400">Users</flux:text>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->userCount }}</div>
            </div>
        </div>

        {{-- New Page Modal --}}
        @if (auth()->user()->isAtLeast(\App\Enums\Role::Manager))
            <flux:modal wire:model="showNewPageModal" class="w-full max-w-md">
                <flux:heading size="lg" class="mb-1">New Page</flux:heading>
                <flux:text class="mb-5 text-zinc-500">Create a new blank page and open it in the editor.</flux:text>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Page Name</flux:label>
                        <flux:input wire:model.live="newPageName" placeholder="e.g. Our Team" />
                        <flux:error name="newPageName" />
                    </flux:field>

                    <flux:field>
                        <flux:label>URL Slug</flux:label>
                        <flux:input wire:model="newPageSlug" placeholder="e.g. our-team" />
                        <flux:description>The URL path for this page: /{{ $newPageSlug ?: 'slug' }}</flux:description>
                        <flux:error name="newPageSlug" />
                    </flux:field>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <flux:button wire:click="$set('showNewPageModal', false)" variant="ghost">Cancel</flux:button>
                    <flux:button wire:click="createPage" variant="primary" icon="plus">Create Page</flux:button>
                </div>
            </flux:modal>
        @endif

        {{-- Quick Actions --}}
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-5 mb-8">
            <flux:heading class="mb-4">Quick Actions</flux:heading>
            <div class="flex flex-wrap gap-3">
                <flux:button href="{{ route('dashboard.blog.create') }}" variant="primary" icon="plus" wire:navigate>
                    New Post
                </flux:button>
                @if (auth()->user()->isAtLeast(\App\Enums\Role::Manager))
                    <flux:button wire:click="$set('showNewPageModal', true)" variant="outline" icon="plus">
                        New Page
                    </flux:button>
                @endif
                <flux:button href="{{ route('dashboard.locations.create') }}" variant="outline" icon="plus" wire:navigate>
                    New Location
                </flux:button>
                <flux:button href="{{ route('dashboard.shortcodes.create') }}" variant="outline" icon="plus" wire:navigate>
                    New Shortcode
                </flux:button>
            </div>
        </div>

        {{-- Recent Posts & Recently Updated Pages --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            {{-- Recent Posts --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading>Recent Posts</flux:heading>
                    <flux:button href="{{ route('dashboard.blog.index') }}" variant="ghost" size="sm" wire:navigate>
                        View all
                    </flux:button>
                </div>

                @if ($this->recentPosts->isEmpty())
                    <div class="text-center py-12 rounded-lg border border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400">
                        <flux:icon name="document-text" class="size-10 mx-auto mb-3 opacity-40" />
                        <p class="text-sm">No posts yet. <a href="{{ route('dashboard.blog.create') }}" class="underline" wire:navigate>Create your first one.</a></p>
                    </div>
                @else
                    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Title</th>
                                    <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">Status</th>
                                    <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden md:table-cell">Date</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach ($this->recentPosts as $post)
                                    <tr wire:key="recent-post-{{ $post->id }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $post->title }}</td>
                                        <td class="px-4 py-3 hidden sm:table-cell">
                                            @php
                                                $badgeVariant = match($post->status) {
                                                    'published' => 'green',
                                                    'unlisted' => 'blue',
                                                    'unpublished' => 'zinc',
                                                    default => 'zinc',
                                                };
                                            @endphp
                                            <flux:badge variant="{{ $badgeVariant }}" size="sm">
                                                {{ ucfirst($post->status) }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs hidden md:table-cell">
                                            {{ $post->published_at?->format('M j, Y') ?? $post->created_at->format('M j, Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <flux:button
                                                href="{{ route('dashboard.blog.edit', $post) }}"
                                                variant="ghost"
                                                size="sm"
                                                icon="pencil"
                                                wire:navigate
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Recently Updated Pages --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading>Recently Updated Pages</flux:heading>
                    <flux:button href="{{ route('dashboard.pages') }}" variant="ghost" size="sm" wire:navigate>
                        View all
                    </flux:button>
                </div>

                @if (empty($this->recentlyUpdatedPages))
                    <div class="text-center py-12 rounded-lg border border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400">
                        <flux:icon name="document" class="size-10 mx-auto mb-3 opacity-40" />
                        <p class="text-sm">No pages found.</p>
                    </div>
                @else
                    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Page</th>
                                    <th class="text-left px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">Modified</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach ($this->recentlyUpdatedPages as $page)
                                    <tr wire:key="recent-page-{{ $loop->index }}" class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $page['label'] }}</td>
                                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs hidden sm:table-cell">
                                            {{ $page['modified_at']->diffForHumans() }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <flux:button
                                                href="{{ route('dashboard.design-library.editor') }}?file={{ urlencode($page['path']) }}"
                                                variant="ghost"
                                                size="sm"
                                                icon="pencil"
                                                wire:navigate
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        {{-- Drafts needing attention --}}
        @if ($this->draftPosts->isNotEmpty())
            <div>
                <div class="mb-4">
                    <flux:heading>Drafts Needing Attention</flux:heading>
                    <flux:text class="mt-1">These posts are unpublished and haven't gone live yet.</flux:text>
                </div>

                <div class="overflow-hidden rounded-lg border border-amber-200 dark:border-amber-900/50">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-amber-100 dark:divide-amber-900/30">
                            @foreach ($this->draftPosts as $post)
                                <tr wire:key="draft-post-{{ $post->id }}" class="bg-amber-50/50 dark:bg-amber-900/10 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $post->title }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                            Created {{ $post->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <flux:button
                                            href="{{ route('dashboard.blog.edit', $post) }}"
                                            variant="outline"
                                            size="sm"
                                            wire:navigate
                                        >
                                            Edit & Publish
                                        </flux:button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </flux:main>
</div>
