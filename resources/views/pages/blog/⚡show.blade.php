<?php

use App\Models\Post;
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
        return $this->post->title.' — GetRows';
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
                        {{ $post->excerpt }}
                    </p>
                @endif

                <div class="leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC] whitespace-pre-wrap">
                    {{ $post->content }}
                </div>
            </article>

            @if ($post->featured_image)
                <div class="lg:sticky lg:top-8">
                    <img
                        src="{{ $post->featuredImageUrl() }}"
                        alt="{{ $post->title }}"
                        class="w-full rounded-lg object-cover"
                    />
                </div>
            @endif
        </div>
    @else
        {{-- Image Top layout (default) --}}
        <article class="max-w-2xl">
            <h1 class="text-4xl font-semibold leading-tight mb-4">{{ $post->title }}</h1>

            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-8">
                {{ $post->published_at?->format('F j, Y') }}
            </p>

            @if ($post->featured_image)
                <img
                    src="{{ $post->featuredImageUrl() }}"
                    alt="{{ $post->title }}"
                    class="w-full rounded-lg object-cover max-h-96 mb-8"
                />
            @endif

            @if ($post->excerpt)
                <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-relaxed mb-8 border-l-2 border-[#e3e3e0] dark:border-[#3E3E3A] pl-4">
                    {{ $post->excerpt }}
                </p>
            @endif

            <div class="leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC] whitespace-pre-wrap">
                {{ $post->content }}
            </div>
        </article>
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
