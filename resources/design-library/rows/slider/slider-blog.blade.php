{{--
@name Slider - Blog Carousel
@description Horizontally scrollable blog post cards.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900 overflow-hidden"
    default-container-classes="max-w-container mx-auto"
    x-data="{
        scrollEl: null,
        init() { this.scrollEl = this.$el.querySelector('[data-scroll]'); },
        prev() { this.scrollEl.scrollBy({ left: -320, behavior: 'smooth' }); },
        next() { this.scrollEl.scrollBy({ left: 320, behavior: 'smooth' }); }
    }">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="flex items-center justify-between mb-8">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Latest Articles"
            default-tag="h2"
            default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.wrapper slug="__SLUG__" prefix="nav_wrapper"
            default-classes="flex items-center gap-3">
            <x-dl.link slug="__SLUG__" prefix="view_all"
                default-label="View all →"
                default-url="/blog"
                default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm mr-4" />
            <button @click="prev()" class="size-9 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-zinc-600 dark:text-zinc-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </button>
            <button @click="next()" class="size-9 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-zinc-600 dark:text-zinc-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>
        </x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="scroll_container"
        default-classes="flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory"
        data-scroll="">
        @foreach ($this->recentPosts ?? [] as $post)
            <x-dl.card slug="__SLUG__" prefix="blog_card" tag="article"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="flex-none w-72 group">
                <a href="{{ route('blog.show', $post->slug) }}">
                    <x-dl.wrapper slug="__SLUG__" prefix="card_image_wrapper"
                        default-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800 mb-4 snap-start">
                        @if ($post->featured_image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" alt="{{ $post->featured_image_alt }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">No image</div>
                        @endif
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="card_title" tag="h3"
                        default-classes="font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                        {{ $post->title }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="card_date"
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
