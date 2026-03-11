{{--
@name Blog Detail - Article
@description Full blog post article with SEO meta, breadcrumb, content, gallery, and navigation.
@sort 5
--}}
@if(isset($post))
@push('head')
    <link rel="canonical" href="{{ route('blog.show', $post->slug) }}" />

    @if ($post->meta_description || $post->excerpt)
        <meta name="description" content="{{ $post->meta_description ?? strip_tags($post->excerpt) }}" />
    @endif

    @if ($post->is_noindex)
        <meta name="robots" content="noindex, nofollow" />
    @endif

    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ route('blog.show', $post->slug) }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    @if ($post->meta_description || $post->excerpt)
        <meta property="og:description" content="{{ $post->meta_description ?? strip_tags($post->excerpt) }}" />
        <meta name="twitter:description" content="{{ $post->meta_description ?? strip_tags($post->excerpt) }}" />
    @endif

    @php $ogImageUrl = $post->og_image ?: $post->featuredImageUrl() ?: \App\Models\Setting::get('seo.og.default_image', ''); @endphp
    @if ($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    @if (\App\Models\Setting::get('seo.twitter.handle', ''))
        <meta name="twitter:site" content="{{ \App\Models\Setting::get('seo.twitter.handle', '') }}" />
    @endif
    @if ($ogImageUrl)
        <meta name="twitter:image" content="{{ $ogImageUrl }}" />
    @endif

    @php
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'url' => route('blog.show', $post->slug),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
        ];
        if ($post->meta_description || $post->excerpt) {
            $articleSchema['description'] = $post->meta_description ?? strip_tags($post->excerpt);
        }
        if ($post->featuredImageUrl()) {
            $articleSchema['image'] = $post->featuredImageUrl();
        }
        $publisherName = config('app.name');
        $seoSchema = \App\Models\Setting::get('seo.schema', []);
        $articleSchema['publisher'] = array_filter([
            '@type' => $seoSchema['type'] ?? 'Organization',
            'name' => $publisherName,
            'url' => config('app.url'),
            'logo' => ! empty($seoSchema['logo'])
                ? ['@type' => 'ImageObject', 'url' => $seoSchema['logo']]
                : null,
        ]);
    @endphp
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@endif

<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">

    {{-- Breadcrumb --}}
    <x-dl.wrapper slug="__SLUG__" prefix="breadcrumb" tag="nav"
        default-classes="mb-8 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
        <a href="{{ route('blog.index') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">Blog</a>
        <span>/</span>
        @if (isset($post->category) && $post->category)
            <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">{{ $post->category->name }}</a>
            <span>/</span>
        @endif
        <span class="text-zinc-900 dark:text-zinc-100 truncate">{{ $post->title ?? 'Post Title' }}</span>
    </x-dl.wrapper>

    {{-- Article --}}
    <article>
        <x-dl.wrapper slug="__SLUG__" prefix="post_title" tag="h1"
            default-classes="text-4xl font-semibold leading-tight mb-4">
            {{ $post->title ?? 'Post Title' }}
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="post_date" tag="p"
            default-classes="text-sm text-zinc-500 dark:text-zinc-400 mb-8">
            {{ ($post->published_at ?? now())->format('F j, Y') }}
        </x-dl.wrapper>

        @if (isset($post) && $post->featured_image)
            <x-dl.wrapper slug="__SLUG__" prefix="featured_image" tag="img"
                src="{{ $post->featuredImageUrl() }}"
                alt="{{ $post->featured_image_alt ?? $post->title }}"
                default-classes="w-full rounded-lg object-cover max-h-96 mb-8" />
        @endif

        @if (isset($post) && $post->excerpt)
            <x-dl.wrapper slug="__SLUG__" prefix="excerpt" tag="p"
                default-classes="text-lg text-zinc-500 dark:text-zinc-400 leading-relaxed mb-8 border-l-2 border-zinc-200 dark:border-zinc-700 pl-4">
                {!! $this->processedExcerpt ?? '' !!}
            </x-dl.wrapper>
        @endif

        <x-dl.wrapper slug="__SLUG__" prefix="article_content"
            default-classes="leading-relaxed text-zinc-900 dark:text-zinc-100 whitespace-pre-wrap">
            {!! $this->processedContent ?? '' !!}
        </x-dl.wrapper>

        @if (isset($post) && $post->cta_buttons)
            <x-dl.wrapper slug="__SLUG__" prefix="cta_buttons"
                default-classes="mt-8 flex flex-wrap gap-3">
                @foreach ($post->cta_buttons as $button)
                    <x-dl.wrapper slug="__SLUG__" prefix="cta_button_link" tag="a"
                        href="{{ $button['url'] }}"
                        target="{{ $button['target'] ?? '_self' }}"
                        rel="{{ ($button['target'] ?? '_self') === '_blank' ? 'noopener noreferrer' : '' }}"
                        default-classes="inline-flex items-center px-6 py-3 bg-zinc-900 dark:bg-zinc-100 text-zinc-100 dark:text-zinc-900 rounded-lg font-medium hover:opacity-90 transition-opacity">
                        {{ $button['text'] }}
                    </x-dl.wrapper>
                @endforeach
            </x-dl.wrapper>
        @endif
    </article>

    {{-- Gallery --}}
    @if (isset($post) && $post->gallery_images)
        <x-dl.wrapper slug="__SLUG__" prefix="gallery"
            default-classes="mt-10"
            x-data="{
                images: @js($post->galleryImagesData()),
                isOpen: false,
                currentIndex: 0,
                open(index) { this.currentIndex = index; this.isOpen = true; document.body.style.overflow = 'hidden'; },
                close() { this.isOpen = false; document.body.style.overflow = ''; },
                prev() { this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length; },
                next() { this.currentIndex = (this.currentIndex + 1) % this.images.length; }
            }"
            @keydown.escape.window="if (isOpen) close()"
            @keydown.arrow-left.window="if (isOpen) prev()"
            @keydown.arrow-right.window="if (isOpen) next()">
            <x-dl.wrapper slug="__SLUG__" prefix="gallery_grid" tag="div"
                default-classes="grid gap-3"
                style="grid-template-columns: repeat({{ $post->gallery_columns ?? 4 }}, minmax(0, 1fr))">
                @foreach ($post->galleryImagesData() as $index => $image)
                    <button type="button" @click="open({{ $index }})" class="aspect-square overflow-hidden rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 focus:ring-offset-2">
                        <img src="{{ $image['url'] }}" alt="{{ $image['alt'] ?: 'Gallery photo ' . ($index + 1) }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-200" />
                    </button>
                @endforeach
            </x-dl.wrapper>
            {{-- Lightbox --}}
            <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/90" @click.self="close()">
                <button type="button" @click="close()" class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors p-2" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
                <button type="button" @click="prev()" x-show="images.length > 1" class="absolute left-4 text-white/80 hover:text-white transition-colors p-2" aria-label="Previous photo">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                </button>
                <div class="max-w-5xl max-h-screen w-full px-16 py-8 flex items-center justify-center">
                    <template x-for="(image, i) in images" :key="i">
                        <img x-show="currentIndex === i" :src="image.url" :alt="image.alt || `Gallery photo ${i + 1}`" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl" />
                    </template>
                </div>
                <button type="button" @click="next()" x-show="images.length > 1" class="absolute right-4 text-white/80 hover:text-white transition-colors p-2" aria-label="Next photo">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                </button>
                <div x-show="images.length > 1" x-text="`${currentIndex + 1} / ${images.length}`" class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/70 text-sm tabular-nums"></div>
            </div>
        </x-dl.wrapper>
    @endif

    {{-- Post navigation --}}
    <x-dl.wrapper slug="__SLUG__" prefix="post_nav"
        default-classes="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-4">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" /></svg>
            Back to Blog
        </a>
        @if (isset($this->nextBlogPost) && $this->nextBlogPost)
            <a href="{{ route('blog.show', $this->nextBlogPost->slug) }}" class="inline-flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors text-end">
                <span class="truncate max-w-48">{{ $this->nextBlogPost->title }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
            </a>
        @endif
    </x-dl.wrapper>

</x-dl.section>
{{--
@php
public \App\Models\Post $post;

public function mount(string $slug): void
{
    $this->post = \App\Models\Post::query()
        ->accessible()
        ->with('category')
        ->where('slug', $slug)
        ->firstOrFail();
}

public function title(): string
{
    return $this->post->meta_title ?: ($this->post->title . ' — ' . config('app.name'));
}

public function getProcessedContentProperty(): string
{
    return \App\Support\ShortcodeProcessor::process($this->post->content);
}

public function getProcessedExcerptProperty(): ?string
{
    return $this->post->excerpt ? \App\Support\ShortcodeProcessor::process($this->post->excerpt) : null;
}

public function getNextBlogPostProperty(): ?\App\Models\Post
{
    return \App\Models\Post::query()
        ->accessible()
        ->where('published_at', '>', $this->post->published_at)
        ->orWhere(function ($query): void {
            $query->where('published_at', $this->post->published_at)
                ->where('id', '>', $this->post->id);
        })
        ->orderBy('published_at')
        ->orderBy('id')
        ->first();
}
--}}
