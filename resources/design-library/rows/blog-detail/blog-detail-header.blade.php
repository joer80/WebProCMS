{{--
@name Blog Detail - Header
@description Blog post header with category, title, author, date, and featured image.
@sort 10
--}}
<article class="pt-20 pb-12 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-3xl mx-auto">
        @if ($post->category ?? null)
            <a href="/blog" class="text-sm font-semibold text-primary uppercase tracking-wider hover:text-primary/80">
                {{ $post->category->name }}
            </a>
        @endif
        <h1 class="mt-3 text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white leading-tight">
            {{ $post->title ?? 'Post Title' }}
        </h1>
        <div class="mt-6 flex items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400">
            <span>{{ ($post->published_at ?? now())->format('F j, Y') }}</span>
            <span>·</span>
            <span>5 min read</span>
        </div>
        @if ($post->featured_image ?? null)
            <div class="mt-8 rounded-2xl overflow-hidden aspect-video">
                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->featured_image_alt ?? '' }}" class="w-full h-full object-cover" />
            </div>
        @else
            <div class="mt-8 rounded-2xl bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center">
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Featured Image</span>
            </div>
        @endif
    </div>
</article>
