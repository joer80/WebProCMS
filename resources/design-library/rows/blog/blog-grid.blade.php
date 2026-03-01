{{--
@name Blog - Grid
@description Three-column blog post grid with featured image, category, title, and date.
@sort 10
--}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-12">
            <div>
                @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
                @if($showHeadline)
                @php $headlineText = content('__SLUG__', 'headline', 'Latest Articles', 'text', 'headline'); @endphp
                @php $headlineClasses = content('__SLUG__', 'headline_classes', 'text-4xl font-bold text-zinc-900 dark:text-white', 'classes', 'headline'); @endphp
                <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
                @endif
                @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
                @if($showSubheadline)
                @php $subheadlineText = content('__SLUG__', 'subheadline', 'Insights, tutorials, and company news.', 'text', 'subheadline'); @endphp
                @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-2 text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
                <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
                @endif
            </div>
            <a href="/blog" class="text-primary font-semibold hover:text-primary/80 transition-colors text-sm">
                View all →
            </a>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach ($this->recentPosts ?? [] as $post)
                <article class="group">
                    <a href="{{ route('blog.show', $post->slug) }}" class="block">
                        <div class="rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video mb-4">
                            @if ($post->featured_image)
                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->featured_image_alt }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500">No image</div>
                            @endif
                        </div>
                        @if ($post->category)
                            <span class="text-xs font-semibold text-primary uppercase tracking-wider">{{ $post->category->name }}</span>
                        @endif
                        <h3 class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                            {{ $post->title }}
                        </h3>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">{{ $post->excerpt }}</p>
                        <div class="mt-3 text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $post->published_at?->format('M j, Y') }}
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
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
