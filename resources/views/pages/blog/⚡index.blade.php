<?php

use App\Models\Category;
use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.public', ['description' => 'Insights, updates, and news from the GetRows team.'])] #[Title('Blog — GetRows')] class extends Component {
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public ?int $categoryId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function filterByCategory(?int $id): void
    {
        $this->categoryId = $id;
        $this->resetPage();
    }

    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<Post> */
    public function getPostsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Post::query()
            ->published()
            ->with('category')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
            ->latest('published_at')
            ->paginate(9);
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Category> */
    public function getCategoriesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::query()
            ->whereHas('posts', fn ($q) => $q->published())
            ->orderBy('name')
            ->get();
    }
}; ?>

<div>
    <div class="mb-10">
        <h1 class="text-4xl font-semibold leading-tight mb-4">Blog</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
            Insights, updates, and news from the GetRows team.
        </p>
    </div>

    {{-- Search + category filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-8">
        <div class="relative flex-1 max-w-sm">
            <flux:input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Search posts…"
                icon="magnifying-glass"
            />
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button
                wire:click="filterByCategory(null)"
                class="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all {{ $categoryId === null ? 'bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] border-transparent' : 'border-[#19140035] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#1915014a] dark:hover:border-[#62605b]' }}"
            >
                All
            </button>

            @foreach ($this->categories as $category)
                <button
                    wire:click="filterByCategory({{ $category->id }})"
                    class="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all {{ $categoryId === $category->id ? 'bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] border-transparent' : 'border-[#19140035] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#1915014a] dark:hover:border-[#62605b]' }}"
                >
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Post grid --}}
    @if ($this->posts->isEmpty())
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">
            @if ($search)
                No posts found for "{{ $search }}".
            @else
                No posts found.
            @endif
        </p>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($this->posts as $post)
                <a
                    wire:key="post-{{ $post->id }}"
                    wire:transition
                    href="{{ route('blog.show', $post->slug) }}"
                    class="group block bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden hover:shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.3)] dark:hover:shadow-[inset_0px_0px_0px_1px_#fffaed50] transition-shadow"
                >
                    @if ($post->featured_image)
                        <img
                            src="{{ $post->featuredImageUrl() }}"
                            alt="{{ $post->title }}"
                            class="w-full h-44 object-cover"
                        />
                    @endif

                    <div class="p-6 flex flex-col {{ $post->featured_image ? '' : 'h-full' }}">
                        @if ($post->category)
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A] mb-3">
                                {{ $post->category->name }}
                            </span>
                        @endif

                        <h2 class="font-semibold text-base leading-snug mb-2 group-hover:text-[#706f6c] dark:group-hover:text-[#A1A09A] transition-colors">
                            {{ $post->title }}
                        </h2>

                        @if ($post->excerpt)
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-relaxed mb-4 line-clamp-3">
                                {{ $post->excerpt }}
                            </p>
                        @endif

                        <div class="mt-auto pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $post->published_at?->format('M j, Y') }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $this->posts->links() }}
        </div>
    @endif
</div>
