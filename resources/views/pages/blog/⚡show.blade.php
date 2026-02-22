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

    <article class="max-w-2xl">
        @if ($post->category)
            <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="text-xs font-semibold uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors">
                {{ $post->category->name }}
            </a>
        @endif

        <h1 class="text-4xl font-semibold leading-tight mt-2 mb-4">{{ $post->title }}</h1>

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

    <div class="mt-12 pt-8 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
        <a
            href="{{ route('blog.index') }}"
            class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors"
        >
            <flux:icon name="arrow-left" class="size-4" />
            Back to Blog
        </a>
    </div>
</div>
