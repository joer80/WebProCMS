{{--
@name Blog - With Sidebar
@description Main blog grid with a categories and recent posts sidebar.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid lg:grid-cols-3 gap-12">
    <div class="lg:col-span-2">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="All Posts"
            default-tag="h2"
            default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-8" />
        <x-dl.wrapper slug="__SLUG__" prefix="posts_grid"
            default-classes="grid sm:grid-cols-2 gap-8">
            @foreach ($this->recentPosts ?? [] as $post)
                <x-dl.card slug="__SLUG__" prefix="post_card" tag="article"
                    data-editor-item-index="{{ $loop->index }}"
                    default-classes="group">
                    <a href="{{ route('blog.show', $post->slug) }}">
                        <x-dl.wrapper slug="__SLUG__" prefix="post_image_wrapper"
                            default-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800 mb-4">
                            @if ($post->featured_image)
                                <x-dl.wrapper slug="__SLUG__" prefix="post_img" tag="img"
                                    src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}"
                                    alt="{{ $post->featured_image_alt }}"
                                    default-classes="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">No image</div>
                            @endif
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="post_title" tag="h3"
                            default-classes="font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                            {{ $post->title }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="post_date"
                            default-classes="mt-2 text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $post->published_at?->format('M j, Y') }}
                        </x-dl.wrapper>
                    </a>
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>
    </div>
    <div class="space-y-10">
        <div>
            <x-dl.wrapper slug="__SLUG__" prefix="sidebar_heading" tag="h3"
                default-classes="font-heading font-bold text-zinc-900 dark:text-white mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-700">
                Categories
            </x-dl.wrapper>
            <x-dl.grid slug="__SLUG__" prefix="categories"
                default-grid-classes="space-y-2"
                default-items='[{"name":"Technology","count":"12"},{"name":"Design","count":"8"},{"name":"Business","count":"15"},{"name":"Updates","count":"5"}]'>
                @dlItems('__SLUG__', 'categories', $categories, '[{"name":"Technology","count":"12"},{"name":"Design","count":"8"},{"name":"Business","count":"15"},{"name":"Updates","count":"5"}]')
                @foreach ($categories as $cat)
                    <x-dl.card slug="__SLUG__" prefix="category_item"
                        data-editor-item-index="{{ $loop->index }}"
                        default-classes="flex items-center justify-between">
                        <x-dl.wrapper slug="__SLUG__" prefix="category_name" tag="span"
                            default-classes="text-sm text-zinc-700 dark:text-zinc-300 hover:text-primary transition-colors">
                            {{ $cat['name'] }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="category_count" tag="span"
                            default-classes="text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $cat['count'] }}
                        </x-dl.wrapper>
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </div>
        <div>
            <x-dl.wrapper slug="__SLUG__" prefix="recent_heading" tag="h3"
                default-classes="font-heading font-bold text-zinc-900 dark:text-white mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-700">
                Recent Posts
            </x-dl.wrapper>
            <x-dl.grid slug="__SLUG__" prefix="recent_items"
                default-grid-classes="space-y-3"
                default-items='[{"title":"Post Title One","date":"Jan 1, 2025"},{"title":"Post Title Two","date":"Jan 5, 2025"},{"title":"Post Title Three","date":"Jan 10, 2025"}]'>
                @dlItems('__SLUG__', 'recent_items', $recentItems, '[{"title":"Post Title One","date":"Jan 1, 2025"},{"title":"Post Title Two","date":"Jan 5, 2025"},{"title":"Post Title Three","date":"Jan 10, 2025"}]')
                @foreach ($recentItems as $item)
                    <x-dl.card slug="__SLUG__" prefix="recent_item"
                        data-editor-item-index="{{ $loop->index }}"
                        default-classes="">
                        <x-dl.wrapper slug="__SLUG__" prefix="recent_title" tag="span"
                            default-classes="block text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:text-primary transition-colors">
                            {{ $item['title'] }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="recent_date" tag="span"
                            default-classes="text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $item['date'] }}
                        </x-dl.wrapper>
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </div>
    </div>
</x-dl.section>
{{--
@php
#[\Livewire\Attributes\Computed]
public function recentPosts(): \Illuminate\Database\Eloquent\Collection
{
    return \App\Models\Post::query()
        ->with('category')
        ->where('status', 'published')
        ->latest('published_at')
        ->limit(4)
        ->get();
}
--}}
