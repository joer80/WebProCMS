{{--
@name Blog - Minimal
@description Minimal blog list with title, date, and category — no images.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="flex items-center justify-between mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Writing"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.link slug="__SLUG__" prefix="view_all"
            default-label="All posts →"
            default-url="/blog"
            default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="posts_list"
        default-classes="divide-y divide-zinc-200 dark:divide-zinc-800">
        @foreach ($this->recentPosts ?? [] as $post)
            <x-dl.card slug="__SLUG__" prefix="post_row" tag="article"
                default-classes="py-5 group">
                <a href="{{ route('blog.show', $post->slug) }}">
                    <x-dl.wrapper slug="__SLUG__" prefix="post_meta"
                        default-classes="flex items-center gap-3 mb-1">
                        <x-dl.wrapper slug="__SLUG__" prefix="post_date" tag="time"
                            default-classes="text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $post->published_at?->format('M j, Y') }}
                        </x-dl.wrapper>
                        @if ($post->category)
                            <x-dl.wrapper slug="__SLUG__" prefix="post_category" tag="span"
                                default-classes="text-xs font-semibold text-primary">
                                {{ $post->category->name }}
                            </x-dl.wrapper>
                        @endif
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="post_title" tag="h3"
                        default-classes="font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">
                        {{ $post->title }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="post_excerpt" tag="p"
                        default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                        {{ $post->excerpt }}
                    </x-dl.wrapper>
                </a>
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
