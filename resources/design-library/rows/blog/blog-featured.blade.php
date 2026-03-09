{{--
@name Blog - Featured
@description Large featured post on top with smaller posts grid below.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="flex items-center justify-between mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Latest from the Blog"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.link slug="__SLUG__" prefix="view_all"
            default-label="View all →"
            default-url="/blog"
            default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
    </x-dl.wrapper>
    @php $featuredPost = ($this->recentPosts ?? collect())->first(); $otherPosts = ($this->recentPosts ?? collect())->skip(1); @endphp
    @if ($featuredPost)
        <x-dl.wrapper slug="__SLUG__" prefix="featured_card"
            default-classes="mb-12 group">
            <a href="{{ route('blog.show', $featuredPost->slug) }}">
                <x-dl.wrapper slug="__SLUG__" prefix="featured_image_wrapper"
                    default-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800 mb-6">
                    @if ($featuredPost->featured_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($featuredPost->featured_image) }}" alt="{{ $featuredPost->featured_image_alt }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-400">No image</div>
                    @endif
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="featured_title" tag="h3"
                    default-classes="text-2xl font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">
                    {{ $featuredPost->title }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="featured_excerpt" tag="p"
                    default-classes="mt-3 text-zinc-500 dark:text-zinc-400 line-clamp-2">
                    {{ $featuredPost->excerpt }}
                </x-dl.wrapper>
            </a>
        </x-dl.wrapper>
    @endif
    <x-dl.wrapper slug="__SLUG__" prefix="other_posts_grid"
        default-classes="grid md:grid-cols-3 gap-8">
        @foreach ($otherPosts as $post)
            <x-dl.card slug="__SLUG__" prefix="post_card" tag="article"
                default-classes="group">
                <a href="{{ route('blog.show', $post->slug) }}">
                    <x-dl.wrapper slug="__SLUG__" prefix="post_image_wrapper"
                        default-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800 mb-4">
                        @if ($post->featured_image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" alt="{{ $post->featured_image_alt }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
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
