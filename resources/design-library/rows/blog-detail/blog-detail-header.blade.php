{{--
@name Blog Detail - Header
@description Blog post header with category, title, author, date, and featured image.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'pt-20 pb-12 px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<article class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-3xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        @php $categoryLinkClasses = content('__SLUG__', 'category_link_classes', 'text-sm font-semibold text-primary uppercase tracking-wider hover:text-primary/80', 'classes', 'content'); @endphp
        @php $titleClasses = content('__SLUG__', 'title_classes', 'mt-3 text-4xl md:text-5xl font-bold text-zinc-900 dark:text-white leading-tight', 'classes', 'content'); @endphp
        @php $metaClasses = content('__SLUG__', 'meta_classes', 'mt-6 flex items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400', 'classes', 'content'); @endphp
        @php $imageWrapperClasses = content('__SLUG__', 'image_wrapper_classes', 'mt-8 rounded-2xl overflow-hidden aspect-video', 'classes', 'content'); @endphp
        @php $imageClasses = content('__SLUG__', 'image_classes', 'w-full h-full object-cover', 'classes', 'content'); @endphp
        @php $imagePlaceholderClasses = content('__SLUG__', 'image_placeholder_classes', 'mt-8 rounded-2xl bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center', 'classes', 'content'); @endphp
        @if ($post->category ?? null)
            <a href="/blog" class="{{ $categoryLinkClasses }}">
                {{ $post->category->name }}
            </a>
        @endif
        <h1 class="{{ $titleClasses }}">
            {{ $post->title ?? 'Post Title' }}
        </h1>
        <div class="{{ $metaClasses }}">
            <span>{{ ($post->published_at ?? now())->format('F j, Y') }}</span>
            <span>·</span>
            <span>5 min read</span>
        </div>
        @if ($post->featured_image ?? null)
            <div class="{{ $imageWrapperClasses }}">
                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->featured_image_alt ?? '' }}" class="{{ $imageClasses }}" />
            </div>
        @else
            <div class="{{ $imagePlaceholderClasses }}">
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Featured Image</span>
            </div>
        @endif
    </div>
</article>
