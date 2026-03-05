{{--
@name Blog - Grid
@description Three-column blog post grid with featured image, category, title, and date.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
        <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
            default-classes="flex items-center justify-between mb-12">
            <div>
                <x-dl.heading slug="__SLUG__" prefix="headline" default="Latest Articles"
                    default-tag="h2"
                    default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
                <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Insights, tutorials, and company news."
                    default-classes="mt-2 text-zinc-500 dark:text-zinc-400" />
            </div>
            <x-dl.link slug="__SLUG__" prefix="view_all"
                default-label="View all →"
                default-url="/blog"
                default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="posts_grid"
            default-classes="grid md:grid-cols-3 gap-8">
            @foreach ($this->recentPosts ?? [] as $post)
                <x-dl.card slug="__SLUG__" prefix="article" tag="article"
                    default-classes="group">
                    <x-dl.group slug="__SLUG__" prefix="post_link" tag="a"
                        href="{{ route('blog.show', $post->slug) }}"
                        default-classes="block">
                        <x-dl.wrapper slug="__SLUG__" prefix="image_wrapper"
                            default-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video mb-4">
                            @if ($post->featured_image)
                                <x-dl.wrapper slug="__SLUG__" prefix="image" tag="img"
                                    src="{{ Storage::url($post->featured_image) }}"
                                    alt="{{ $post->featured_image_alt }}"
                                    default-classes="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500">No image</div>
                            @endif
                        </x-dl.wrapper>
                        @if ($post->category)
                            <x-dl.wrapper slug="__SLUG__" prefix="category" tag="span"
                                default-classes="text-xs font-semibold text-primary uppercase tracking-wider">
                                {{ $post->category->name }}
                            </x-dl.wrapper>
                        @endif
                        <x-dl.wrapper slug="__SLUG__" prefix="post_title" tag="h3"
                            default-classes="mt-2 text-lg font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                            {{ $post->title }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="post_excerpt" tag="p"
                            default-classes="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                            {{ $post->excerpt }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="post_date"
                            default-classes="mt-3 text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $post->published_at?->format('M j, Y') }}
                        </x-dl.wrapper>
                    </x-dl.group>
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
        ->limit(3)
        ->get();
}
--}}
