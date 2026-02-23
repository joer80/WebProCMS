<?php

use App\Models\Post;
use App\Support\ShortcodeProcessor;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public')] class extends Component {
    public Post $post;

    public function mount(string $slug): void
    {
        $this->post = Post::query()
            ->accessible()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function title(): string
    {
        return $this->post->meta_title ?: ($this->post->title.' — '.config('app.name'));
    }

    public function getProcessedContentProperty(): string
    {
        return ShortcodeProcessor::process($this->post->content);
    }

    public function getProcessedExcerptProperty(): ?string
    {
        return $this->post->excerpt ? ShortcodeProcessor::process($this->post->excerpt) : null;
    }

    public function getNextPostProperty(): ?Post
    {
        return Post::query()
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
}; ?>

@push('head')
    <link rel="canonical" href="{{ route('blog.show', $post->slug) }}" />

    @if ($post->meta_description || $post->excerpt)
        <meta name="description" content="{{ $post->meta_description ?? strip_tags($post->excerpt) }}" />
    @endif

    @if ($post->is_noindex)
        <meta name="robots" content="noindex, nofollow" />
    @endif

    {{-- Open Graph --}}
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ $post->og_title ?? $post->meta_title ?? $post->title }}" />
    <meta property="og:url" content="{{ route('blog.show', $post->slug) }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    @if ($post->og_description || $post->meta_description || $post->excerpt)
        <meta property="og:description" content="{{ $post->og_description ?? $post->meta_description ?? strip_tags($post->excerpt) }}" />
    @endif

    @php
        $ogImageUrl = $post->og_image ?: $post->featuredImageUrl() ?: config('seo.og.default_image');
    @endphp
    @if ($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}" />
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    @if (config('seo.twitter.handle'))
        <meta name="twitter:site" content="{{ config('seo.twitter.handle') }}" />
    @endif
    <meta name="twitter:title" content="{{ $post->og_title ?? $post->meta_title ?? $post->title }}" />
    @if ($post->og_description || $post->meta_description || $post->excerpt)
        <meta name="twitter:description" content="{{ $post->og_description ?? $post->meta_description ?? strip_tags($post->excerpt) }}" />
    @endif
    @if ($ogImageUrl)
        <meta name="twitter:image" content="{{ $ogImageUrl }}" />
    @endif

    {{-- Article Schema --}}
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

        $publisherName = config('seo.schema.name') ?: config('app.name');
        $articleSchema['publisher'] = array_filter([
            '@type' => config('seo.schema.type', 'Organization'),
            'name' => $publisherName,
            'url' => config('seo.schema.url') ?: config('app.url'),
            'logo' => config('seo.schema.logo')
                ? ['@type' => 'ImageObject', 'url' => config('seo.schema.logo')]
                : null,
        ]);
    @endphp
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

<div>
    {{-- Breadcrumb --}}
    <nav class="mb-8 flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
        <a href="{{ route('blog.index') }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors">Blog</a>
        <span>/</span>
        @if ($post->category)
            <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors">{{ $post->category->name }}</a>
            <span>/</span>
        @endif
        <span class="text-[#1b1b18] dark:text-[#EDEDEC] truncate">{{ $post->title }}</span>
    </nav>

    @if ($post->layout === 'image-right')
        {{-- Image Right layout: 50/50 grid with text left, image right --}}
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-start">
            <article>
                <h1 class="text-4xl font-semibold leading-tight mb-4">{{ $post->title }}</h1>

                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
                    {{ $post->published_at?->format('F j, Y') }}
                </p>

                @if ($post->excerpt)
                    <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-relaxed mb-8 border-l-2 border-[#e3e3e0] dark:border-[#3E3E3A] pl-4">
                        {!! $this->processedExcerpt !!}
                    </p>
                @endif

                <div class="leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC] whitespace-pre-wrap">
                    {!! $this->processedContent !!}
                </div>

                @if ($post->cta_buttons)
                    <div class="mt-8 flex flex-wrap gap-3">
                        @foreach ($post->cta_buttons as $button)
                            <a
                                href="{{ $button['url'] }}"
                                target="{{ $button['target'] ?? '_self' }}"
                                @if (($button['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif
                                class="inline-flex items-center px-6 py-3 bg-[#1b1b18] dark:bg-[#EDEDEC] text-[#EDEDEC] dark:text-[#1b1b18] rounded-lg font-medium hover:opacity-90 transition-opacity"
                            >
                                {{ $button['text'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </article>

            @if ($post->featured_image)
                <div class="lg:sticky lg:top-8">
                    <img
                        src="{{ $post->featuredImageUrl() }}"
                        alt="{{ $post->featured_image_alt ?? $post->title }}"
                        class="w-full rounded-lg object-cover"
                    />
                </div>
            @endif
        </div>
    @else
        {{-- Image Top layout (default) --}}
        <div>
            <h1 class="text-4xl font-semibold leading-tight mb-4">{{ $post->title }}</h1>

            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-8">
                {{ $post->published_at?->format('F j, Y') }}
            </p>

            @if ($post->featured_image)
                <img
                    src="{{ $post->featuredImageUrl() }}"
                    alt="{{ $post->featured_image_alt ?? $post->title }}"
                    class="w-full rounded-lg object-cover max-h-96 mb-8"
                />
            @endif

            <div class="max-w-2xl mx-auto">
                @if ($post->excerpt)
                    <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-relaxed mb-8 border-l-2 border-[#e3e3e0] dark:border-[#3E3E3A] pl-4">
                        {!! $this->processedExcerpt !!}
                    </p>
                @endif

                <div class="leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC] whitespace-pre-wrap">
                    {!! $this->processedContent !!}
                </div>

                @if ($post->cta_buttons)
                    <div class="mt-8 flex flex-wrap gap-3">
                        @foreach ($post->cta_buttons as $button)
                            <a
                                href="{{ $button['url'] }}"
                                target="{{ $button['target'] ?? '_self' }}"
                                @if (($button['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif
                                class="inline-flex items-center px-6 py-3 bg-[#1b1b18] dark:bg-[#EDEDEC] text-[#EDEDEC] dark:text-[#1b1b18] rounded-lg font-medium hover:opacity-90 transition-opacity"
                            >
                                {{ $button['text'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ($post->gallery_images)
        <div
            class="mt-10"
            x-data="{
                images: @js($post->galleryImagesData()),
                isOpen: false,
                currentIndex: 0,
                open(index) {
                    this.currentIndex = index;
                    this.isOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                close() {
                    this.isOpen = false;
                    document.body.style.overflow = '';
                },
                prev() {
                    this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                },
                next() {
                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                }
            }"
            @keydown.escape.window="if (isOpen) close()"
            @keydown.arrow-left.window="if (isOpen) prev()"
            @keydown.arrow-right.window="if (isOpen) next()"
        >
            <div
                class="grid gap-3"
                style="grid-template-columns: repeat({{ $post->gallery_columns ?? 4 }}, minmax(0, 1fr))"
            >
                @foreach ($post->galleryImagesData() as $index => $image)
                    <button
                        type="button"
                        @click="open({{ $index }})"
                        class="aspect-square overflow-hidden rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:ring-offset-2"
                    >
                        <img
                            src="{{ $image['url'] }}"
                            alt="{{ $image['alt'] ?: 'Gallery photo ' . ($index + 1) }}"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-200"
                        />
                    </button>
                @endforeach
            </div>

            {{-- Lightbox overlay --}}
            <div
                x-show="isOpen"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
                @click.self="close()"
            >
                {{-- Close button --}}
                <button
                    type="button"
                    @click="close()"
                    class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors p-2"
                    aria-label="Close"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Prev button --}}
                <button
                    type="button"
                    @click="prev()"
                    x-show="images.length > 1"
                    class="absolute left-4 text-white/80 hover:text-white transition-colors p-2"
                    aria-label="Previous photo"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>

                {{-- Image --}}
                <div class="max-w-5xl max-h-screen w-full px-16 py-8 flex items-center justify-center">
                    <template x-for="(image, i) in images" :key="i">
                        <img
                            x-show="currentIndex === i"
                            :src="image.url"
                            :alt="image.alt || `Gallery photo ${i + 1}`"
                            class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl"
                        />
                    </template>
                </div>

                {{-- Next button --}}
                <button
                    type="button"
                    @click="next()"
                    x-show="images.length > 1"
                    class="absolute right-4 text-white/80 hover:text-white transition-colors p-2"
                    aria-label="Next photo"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>

                {{-- Counter --}}
                <div
                    x-show="images.length > 1"
                    x-text="`${currentIndex + 1} / ${images.length}`"
                    class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/70 text-sm tabular-nums"
                ></div>
            </div>
        </div>
    @endif

    <div class="mt-12 pt-8 border-t border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between gap-4">
        <a
            href="{{ route('blog.index') }}"
            class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors"
        >
            <flux:icon name="arrow-left" class="size-4" />
            Back to Blog
        </a>

        @if ($this->nextPost)
            <a
                href="{{ route('blog.show', $this->nextPost->slug) }}"
                class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors text-end"
            >
                <span class="truncate max-w-48">{{ $this->nextPost->title }}</span>
                <flux:icon name="arrow-right" class="size-4 shrink-0" />
            </a>
        @endif
    </div>
</div>
