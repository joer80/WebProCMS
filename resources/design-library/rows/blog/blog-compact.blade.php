{{--
@name Blog - Compact
@description Compact three-column blog grid with small thumbnails.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="flex items-center justify-between mb-10">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Recent Posts"
            default-tag="h2"
            default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.link slug="__SLUG__" prefix="view_all"
            default-label="View all →"
            default-url="/blog"
            default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="posts_grid"
        default-classes="grid md:grid-cols-3 gap-6">
        @foreach ($this->recentPosts ?? [] as $post)
            <x-dl.card slug="__SLUG__" prefix="post_card" tag="article"
                default-classes="group flex gap-4">
                <x-dl.wrapper slug="__SLUG__" prefix="post_thumbnail"
                    default-classes="shrink-0 size-16 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    @if ($post->featured_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" alt="{{ $post->featured_image_alt }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-400 text-xs">—</div>
                    @endif
                </x-dl.wrapper>
                <div>
                    <x-dl.wrapper slug="__SLUG__" prefix="post_title" tag="h3"
                        default-classes="text-sm font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="post_date"
                        default-classes="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                        {{ $post->published_at?->format('M j, Y') }}
                    </x-dl.wrapper>
                </div>
            </x-dl.card>
        @endforeach
    </x-dl.wrapper>
</x-dl.section>
{{--
@php
use App\Models\Post;
use Livewire\Attributes\Computed;

#[Computed]
public function recentPosts(): \Illuminate\Database\Eloquent\Collection
{
    return Post::query()
        ->with('category')
        ->where('status', 'published')
        ->latest('published_at')
        ->limit(6)
        ->get();
}
--}}
