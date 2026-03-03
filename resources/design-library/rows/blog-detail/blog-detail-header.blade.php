{{--
@name Blog Detail - Header
@description Blog post header with category, title, author, date, and featured image.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    tag="article"
    default-section-classes="pt-20 pb-12 px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
        @if ($post->category ?? null)
            <x-dl.wrapper slug="__SLUG__" prefix="category_link" tag="a"
                href="/blog"
                default-classes="text-sm font-semibold text-primary uppercase tracking-wider hover:text-primary/80">
                {{ $post->category->name }}
            </x-dl.wrapper>
        @endif
        <x-dl.wrapper slug="__SLUG__" prefix="title" tag="h1"
            default-classes="mt-3 text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white leading-tight">
            {{ $post->title ?? 'Post Title' }}
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="meta"
            default-classes="mt-6 flex items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400">
            <span>{{ ($post->published_at ?? now())->format('F j, Y') }}</span>
            <span>·</span>
            <span>5 min read</span>
        </x-dl.wrapper>
        @if ($post->featured_image ?? null)
            <x-dl.wrapper slug="__SLUG__" prefix="image_wrapper"
                default-classes="mt-8 rounded-2xl overflow-hidden aspect-video">
                <x-dl.wrapper slug="__SLUG__" prefix="image" tag="img"
                    src="{{ Storage::url($post->featured_image) }}"
                    alt="{{ $post->featured_image_alt ?? '' }}"
                    default-classes="w-full h-full object-cover" />
            </x-dl.wrapper>
        @else
            <x-dl.wrapper slug="__SLUG__" prefix="image_placeholder"
                default-classes="mt-8 rounded-2xl bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center">
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Featured Image</span>
            </x-dl.wrapper>
        @endif
</x-dl.section>
