<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Read our latest articles, insights, and updates.'])] #[Title('Blog')] class extends Component {
    public string $pageName = 'Blog';
    // ROW:php:start:blog-posts-index:4ZGUOg
    use \Livewire\WithPagination;

    #[\Livewire\Attributes\Url(as: 'q')]
    public string $search = '';

    #[\Livewire\Attributes\Url(as: 'category')]
    public ?string $categorySlug = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategorySlug(): void
    {
        $this->resetPage();
    }

    public function filterByCategory(?string $slug): void
    {
        $this->categorySlug = $slug;
        $this->resetPage();
    }

    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Post> */
    public function getBlogPostsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return \App\Models\Post::query()
            ->published()
            ->with('category')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->categorySlug, fn ($q) => $q->whereHas('category', fn ($q) => $q->where('slug', $this->categorySlug)))
            ->latest('published_at')
            ->paginate(9);
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> */
    public function getBlogCategoriesProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Category::query()
            ->whereHas('posts', fn ($q) => $q->published())
            ->orderBy('name')
            ->get();
    }
    // ROW:php:end:blog-posts-index:4ZGUOg
}; ?>
<div>{{-- ROW:start:page-title-banner:EwZBCC:shared=1 --}}
<x-dl.section slug="page-title-banner:EwZBCC"
    default-section-classes="relative py-section-banner px-6 bg-zinc-800 bg-cover bg-center"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="page-title-banner:EwZBCC" prefix="overlay" tag="div"
        default-toggle="1"
        default-classes="absolute inset-0 bg-black/50" />
    <x-dl.heading slug="page-title-banner:EwZBCC" prefix="headline" default="{{ $pageName ?? 'Page Title' }}"
        default-tag="h1"
        default-classes="relative z-10 font-heading text-4xl sm:text-5xl font-bold text-white" />
</x-dl.section>
{{-- ROW:end:page-title-banner:EwZBCC --}}

{{-- ROW:start:blog-posts-index:4ZGUOg --}}
<x-dl.section slug="blog-posts-index:4ZGUOg"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="blog-posts-index:4ZGUOg" prefix="headline" default="Blog"
        default-tag="h1"
        default-classes="text-4xl font-semibold leading-tight mb-4" />
    <x-dl.subheadline slug="blog-posts-index:4ZGUOg" prefix="subheadline" default="Insights, updates, and news from our team."
        default-classes="text-zinc-500 dark:text-zinc-400 leading-normal mb-10" />

    {{-- Search + category filters --}}
    <x-dl.wrapper slug="blog-posts-index:4ZGUOg" prefix="filters_bar"
        default-classes="flex flex-col sm:flex-row sm:items-center gap-4 mb-8">
        <x-dl.group slug="blog-posts-index:4ZGUOg" prefix="search_wrapper"
            default-classes="relative flex-1 max-w-sm">
            <div class="pointer-events-none absolute inset-y-0 inset-s-0 flex items-center ps-3 text-zinc-400/75">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" /></svg>
            </div>
            <x-dl.wrapper slug="blog-posts-index:4ZGUOg" prefix="search_input" tag="input"
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Search posts…"
                default-classes="w-full border border-zinc-200 border-b-zinc-300/80 rounded-lg bg-white text-base sm:text-sm py-2 h-10 ps-10 pe-3 text-zinc-700 placeholder-zinc-400 dark:bg-white/10 dark:border-white/10 dark:text-zinc-300 dark:placeholder-zinc-400" />
        </x-dl.group>
        <x-dl.group slug="blog-posts-index:4ZGUOg" prefix="category_pills"
            default-classes="flex flex-wrap items-center gap-2">
            <button
                wire:click="filterByCategory(null)"
                :class="$categorySlug === null ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:border-zinc-400 dark:hover:border-zinc-500'"
                class="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all"
            >
                All
            </button>
            @foreach ($this->blogCategories as $category)
                <button
                    wire:click="filterByCategory('{{ $category->slug }}')"
                    :class="$categorySlug === '{{ $category->slug }}' ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 border-transparent' : 'border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 hover:border-zinc-400 dark:hover:border-zinc-500'"
                    class="inline-block px-4 py-1.5 text-sm rounded-sm border transition-all"
                >
                    {{ $category->name }}
                </button>
            @endforeach
        </x-dl.group>
    </x-dl.wrapper>

    {{-- Empty state --}}
    @if ($this->blogPosts->isEmpty())
        <x-dl.wrapper slug="blog-posts-index:4ZGUOg" prefix="empty_message" tag="p"
            default-classes="text-zinc-500 dark:text-zinc-400 text-sm">
            @if ($search)
                No posts found for "{{ $search }}".
            @else
                No posts found.
            @endif
        </x-dl.wrapper>
    @else
        {{-- Post grid --}}
        <x-dl.wrapper slug="blog-posts-index:4ZGUOg" prefix="posts_grid"
            default-classes="grid sm:grid-cols-2 lg:grid-cols-3 gap-6"
            note="Posts are managed from the <a href='/dashboard/blog' class='text-primary underline hover:text-primary/80'>Blog</a> page.">
            @foreach ($this->blogPosts as $post)
                <x-dl.card slug="blog-posts-index:4ZGUOg" prefix="post_card" tag="a"
                    wire:key="post-{{ $post->id }}"
                    wire:transition
                    href="{{ route('blog.show', $post->slug) }}"
                    default-classes="group block bg-white dark:bg-zinc-900 rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden hover:shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.3)] dark:hover:shadow-[inset_0px_0px_0px_1px_#fffaed50] transition-shadow">
                    @if ($post->featured_image)
                        <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="w-full h-44 object-cover" />
                    @endif
                    <x-dl.group slug="blog-posts-index:4ZGUOg" prefix="card_body"
                        default-classes="p-6 flex flex-col">
                        @if ($post->category)
                            <x-dl.wrapper slug="blog-posts-index:4ZGUOg" prefix="post_category" tag="span"
                                wire:click.stop="filterByCategory('{{ $post->category->slug }}')"
                                default-classes="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 cursor-pointer hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors w-fit">
                                {{ $post->category->name }}
                            </x-dl.wrapper>
                        @endif
                        <x-dl.wrapper slug="blog-posts-index:4ZGUOg" prefix="post_title" tag="h2"
                            default-classes="font-semibold text-base leading-snug mb-2 group-hover:text-zinc-500 dark:group-hover:text-zinc-400 transition-colors">
                            {{ $post->title }}
                        </x-dl.wrapper>
                        @if ($post->excerpt)
                            <x-dl.wrapper slug="blog-posts-index:4ZGUOg" prefix="post_excerpt" tag="p"
                                default-classes="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4 line-clamp-3">
                                {{ $post->excerpt }}
                            </x-dl.wrapper>
                        @endif
                        <x-dl.wrapper slug="blog-posts-index:4ZGUOg" prefix="post_footer"
                            default-classes="mt-auto pt-4 border-t border-zinc-200 dark:border-zinc-700">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $post->published_at?->format('M j, Y') }}</span>
                        </x-dl.wrapper>
                    </x-dl.group>
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>
        <x-dl.wrapper slug="blog-posts-index:4ZGUOg" prefix="pagination"
            default-classes="mt-10">
            {{ $this->blogPosts->links() }}
        </x-dl.wrapper>
    @endif
</x-dl.section>
{{-- ROW:end:blog-posts-index:4ZGUOg --}}
</div>
