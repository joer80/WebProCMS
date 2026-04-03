{{--
@name Blog Detail - Related Posts
@description Grid of related posts shown at the bottom of the article.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-container mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="You Might Also Like"
        default-tag="h2"
        default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-10" />
    <x-dl.wrapper slug="__SLUG__" prefix="posts_grid"
        default-classes="grid md:grid-cols-3 gap-8">
        @foreach ($this->relatedPosts ?? [] as $post)
            <x-dl.card slug="__SLUG__" prefix="related_card" tag="article"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="group">
                <a href="{{ route('blog.show', $post->slug) }}">
                    <x-dl.wrapper slug="__SLUG__" prefix="post_image_wrapper"
                        default-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800 mb-4">
                        @if ($post->featured_image)
                            <x-dl.wrapper slug="__SLUG__" prefix="post_img" tag="img"
                                src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}"
                                alt="{{ $post->featured_image_alt }}"
                                default-classes="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
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
public function relatedPosts(): \Illuminate\Database\Eloquent\Collection
{
    return \App\Models\Post::query()
        ->with('category')
        ->where('status', 'published')
        ->latest('published_at')
        ->limit(3)
        ->get();
}
--}}
