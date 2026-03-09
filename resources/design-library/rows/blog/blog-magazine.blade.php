{{--
@name Blog - Magazine
@description Magazine-style layout with a large hero post and sidebar of smaller posts.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="flex items-center justify-between mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="The Latest"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.link slug="__SLUG__" prefix="view_all"
            default-label="View all →"
            default-url="/blog"
            default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
    </x-dl.wrapper>
    @php $featuredPost = ($this->recentPosts ?? collect())->first(); $sidebarPosts = ($this->recentPosts ?? collect())->skip(1); @endphp
    <x-dl.wrapper slug="__SLUG__" prefix="magazine_grid"
        default-classes="grid md:grid-cols-3 gap-8">
        <x-dl.wrapper slug="__SLUG__" prefix="featured_column"
            default-classes="md:col-span-2">
            @if ($featuredPost)
                <x-dl.card slug="__SLUG__" prefix="featured_card" tag="article"
                    default-classes="group h-full">
                    <a href="{{ route('blog.show', $featuredPost->slug) }}">
                        <x-dl.wrapper slug="__SLUG__" prefix="featured_image"
                            default-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800 mb-5">
                            @if ($featuredPost->featured_image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($featuredPost->featured_image) }}" alt="{{ $featuredPost->featured_image_alt }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-zinc-400">No image</div>
                            @endif
                        </x-dl.wrapper>
                        @if ($featuredPost->category)
                            <x-dl.wrapper slug="__SLUG__" prefix="featured_category" tag="span"
                                default-classes="text-xs font-semibold text-primary uppercase tracking-wider">
                                {{ $featuredPost->category->name }}
                            </x-dl.wrapper>
                        @endif
                        <x-dl.wrapper slug="__SLUG__" prefix="featured_title" tag="h3"
                            default-classes="mt-2 text-2xl font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">
                            {{ $featuredPost->title }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="featured_excerpt" tag="p"
                            default-classes="mt-3 text-zinc-500 dark:text-zinc-400 line-clamp-3">
                            {{ $featuredPost->excerpt }}
                        </x-dl.wrapper>
                    </a>
                </x-dl.card>
            @endif
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="sidebar_column"
            default-classes="space-y-6 divide-y divide-zinc-200 dark:divide-zinc-800">
            @foreach ($sidebarPosts as $post)
                <x-dl.card slug="__SLUG__" prefix="sidebar_post" tag="article"
                    default-classes="pt-6 first:pt-0 group">
                    <a href="{{ route('blog.show', $post->slug) }}">
                        @if ($post->category)
                            <x-dl.wrapper slug="__SLUG__" prefix="sidebar_category" tag="span"
                                default-classes="text-xs font-semibold text-primary uppercase tracking-wider">
                                {{ $post->category->name }}
                            </x-dl.wrapper>
                        @endif
                        <x-dl.wrapper slug="__SLUG__" prefix="sidebar_title" tag="h3"
                            default-classes="mt-1 font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                            {{ $post->title }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="sidebar_date"
                            default-classes="mt-2 text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $post->published_at?->format('M j, Y') }}
                        </x-dl.wrapper>
                    </a>
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>
    </x-dl.wrapper>
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
        ->limit(5)
        ->get();
}
--}}
