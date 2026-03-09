{{--
@name Blog - List
@description Horizontal list-style blog posts with thumbnail on left and text on right.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-4xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="flex items-center justify-between mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Latest Articles"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.link slug="__SLUG__" prefix="view_all"
            default-label="View all →"
            default-url="/blog"
            default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="posts_list"
        default-classes="space-y-8">
        @foreach ($this->recentPosts ?? [] as $post)
            <x-dl.card slug="__SLUG__" prefix="post_item" tag="article"
                default-classes="flex gap-6 group">
                <x-dl.wrapper slug="__SLUG__" prefix="post_thumbnail"
                    default-classes="shrink-0 w-32 h-24 rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    @if ($post->featured_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" alt="{{ $post->featured_image_alt }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-400 text-xs">No image</div>
                    @endif
                </x-dl.wrapper>
                <div>
                    @if ($post->category)
                        <x-dl.wrapper slug="__SLUG__" prefix="post_category" tag="span"
                            default-classes="text-xs font-semibold text-primary uppercase tracking-wider">
                            {{ $post->category->name }}
                        </x-dl.wrapper>
                    @endif
                    <x-dl.wrapper slug="__SLUG__" prefix="post_title" tag="h3"
                        default-classes="mt-1 font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="post_date"
                        default-classes="mt-2 text-xs text-zinc-400 dark:text-zinc-500">
                        {{ $post->published_at?->format('M j, Y') }}
                    </x-dl.wrapper>
                </div>
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
        ->limit(5)
        ->get();
}
--}}
