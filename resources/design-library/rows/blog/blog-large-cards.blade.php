{{--
@name Blog - Large Cards
@description Two-column grid of large blog cards with full-width images.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Featured Stories"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.link slug="__SLUG__" prefix="view_all"
            default-label="All stories →"
            default-url="/blog"
            default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
    </div>
    <x-dl.wrapper slug="__SLUG__" prefix="posts_grid"
        default-classes="grid md:grid-cols-2 gap-8">
        @foreach ($this->recentPosts ?? [] as $post)
            <x-dl.card slug="__SLUG__" prefix="post_card" tag="article"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="group bg-white dark:bg-zinc-900 rounded-card overflow-hidden shadow-card border border-zinc-200 dark:border-zinc-700">
                <a href="{{ route('blog.show', $post->slug) }}">
                    <x-dl.wrapper slug="__SLUG__" prefix="post_image_wrapper"
                        default-classes="aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        @if ($post->featured_image)
                            <x-dl.wrapper slug="__SLUG__" prefix="post_img" tag="img"
                                src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}"
                                alt="{{ $post->featured_image_alt }}"
                                default-classes="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                        @else
                            <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">No image</div>
                        @endif
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="post_body"
                        default-classes="p-6">
                        @if ($post->category)
                            <x-dl.wrapper slug="__SLUG__" prefix="post_category" tag="span"
                                default-classes="text-xs font-semibold text-primary uppercase tracking-wider">
                                {{ $post->category->name }}
                            </x-dl.wrapper>
                        @endif
                        <x-dl.wrapper slug="__SLUG__" prefix="post_title" tag="h3"
                            default-classes="mt-2 text-xl font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors leading-snug">
                            {{ $post->title }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="post_excerpt" tag="p"
                            default-classes="mt-3 text-zinc-500 dark:text-zinc-400 line-clamp-3">
                            {{ $post->excerpt }}
                        </x-dl.wrapper>
                        <x-dl.group slug="__SLUG__" prefix="post_footer"
                            default-classes="mt-5 flex items-center justify-between text-xs text-zinc-400 dark:text-zinc-500">
                            <x-dl.wrapper slug="__SLUG__" prefix="post_date" tag="span"
                                default-classes="text-xs text-zinc-400 dark:text-zinc-500">
                                {{ $post->published_at?->format('M j, Y') }}
                            </x-dl.wrapper>
                            @if ($post->author)
                                <x-dl.wrapper slug="__SLUG__" prefix="post_author" tag="span"
                                    default-classes="text-xs text-zinc-400 dark:text-zinc-500">
                                    {{ $post->author->name }}
                                </x-dl.wrapper>
                            @endif
                        </x-dl.group>
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
        ->with(['category', 'author'])
        ->where('status', 'published')
        ->latest('published_at')
        ->limit(4)
        ->get();
}
--}}
