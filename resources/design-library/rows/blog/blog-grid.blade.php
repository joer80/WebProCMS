{{--
@name Blog - Grid
@description Three-column blog post grid with featured image, category, title, and date.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-6xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'flex items-center justify-between mb-12'); @endphp
        @php $viewAllClasses = content('__SLUG__', 'view_all_classes', 'text-primary font-semibold hover:text-primary/80 transition-colors text-sm'); @endphp
        @php $postsGridClasses = content('__SLUG__', 'posts_grid_classes', 'grid md:grid-cols-3 gap-8'); @endphp
        @php $articleClasses = content('__SLUG__', 'article_classes', 'group'); @endphp
        @php $imageWrapperClasses = content('__SLUG__', 'image_wrapper_classes', 'rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video mb-4'); @endphp
        @php $imageClasses = content('__SLUG__', 'image_classes', 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300'); @endphp
        @php $categoryClasses = content('__SLUG__', 'category_classes', 'text-xs font-semibold text-primary uppercase tracking-wider'); @endphp
        @php $postTitleClasses = content('__SLUG__', 'post_title_classes', 'mt-2 text-lg font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2'); @endphp
        @php $postExcerptClasses = content('__SLUG__', 'post_excerpt_classes', 'mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2'); @endphp
        @php $postDateClasses = content('__SLUG__', 'post_date_classes', 'mt-3 text-xs text-zinc-400 dark:text-zinc-500'); @endphp
        @php $postLinkClasses = content('__SLUG__', 'post_link_classes', 'block'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            <div>
                @php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
                @if($toggleHeadline)
                @php $headlineText = content('__SLUG__', 'headline', 'Latest Articles'); @endphp
                @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
                <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
                @endif
                @php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
                @if($toggleSubheadline)
                @php $subheadlineText = content('__SLUG__', 'subheadline', 'Insights, tutorials, and company news.'); @endphp
                @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-2 text-zinc-500 dark:text-zinc-400'); @endphp
                <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
                @endif
            </div>
            <a href="/blog" class="{{ $viewAllClasses }}">
                View all →
            </a>
        </div>
        <div class="{{ $postsGridClasses }}">
            @foreach ($this->recentPosts ?? [] as $post)
                <article class="{{ $articleClasses }}">
                    <a href="{{ route('blog.show', $post->slug) }}" class="{{ $postLinkClasses }}">
                        <div class="{{ $imageWrapperClasses }}">
                            @if ($post->featured_image)
                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->featured_image_alt }}" class="{{ $imageClasses }}" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500">No image</div>
                            @endif
                        </div>
                        @if ($post->category)
                            <span class="{{ $categoryClasses }}">{{ $post->category->name }}</span>
                        @endif
                        <h3 class="{{ $postTitleClasses }}">
                            {{ $post->title }}
                        </h3>
                        <p class="{{ $postExcerptClasses }}">{{ $post->excerpt }}</p>
                        <div class="{{ $postDateClasses }}">
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
