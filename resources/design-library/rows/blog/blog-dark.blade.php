{{--
@name Blog - Dark
@description Dark background three-column blog post grid.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="From the Blog"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-white" />
        <x-dl.link slug="__SLUG__" prefix="view_all"
            default-label="View all →"
            default-url="/blog"
            default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
    </div>
    <x-dl.wrapper slug="__SLUG__" prefix="posts_grid"
        default-classes="grid md:grid-cols-3 gap-8">
        @foreach ($this->recentPosts ?? [] as $post)
            <x-dl.card slug="__SLUG__" prefix="post_card" tag="article"
                default-classes="group bg-zinc-800 rounded-card overflow-hidden border border-zinc-700 hover:border-primary/40 transition-colors">
                <a href="{{ route('blog.show', $post->slug) }}">
                    <x-dl.wrapper slug="__SLUG__" prefix="post_image_wrapper"
                        default-classes="aspect-video overflow-hidden bg-zinc-700">
                        @if ($post->featured_image)
                            <x-dl.wrapper slug="__SLUG__" prefix="post_img" tag="img"
                                src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}"
                                alt="{{ $post->featured_image_alt }}"
                                default-classes="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                        @else
                            <div class="w-full h-full flex items-center justify-center text-zinc-500 text-sm">No image</div>
                        @endif
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="post_body"
                        default-classes="p-5">
                        @if ($post->category)
                            <x-dl.wrapper slug="__SLUG__" prefix="post_category" tag="span"
                                default-classes="text-xs font-semibold text-primary uppercase tracking-wider">
                                {{ $post->category->name }}
                            </x-dl.wrapper>
                        @endif
                        <x-dl.wrapper slug="__SLUG__" prefix="post_title" tag="h3"
                            default-classes="mt-2 font-semibold text-white group-hover:text-primary transition-colors line-clamp-2">
                            {{ $post->title }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="post_date"
                            default-classes="mt-3 text-xs text-zinc-500">
                            {{ $post->published_at?->format('M j, Y') }}
                        </x-dl.wrapper>
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
        ->limit(3)
        ->get();
}
--}}
