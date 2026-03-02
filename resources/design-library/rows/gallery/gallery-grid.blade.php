{{--
@name Gallery - Grid
@description Masonry-style photo gallery grid with hover overlay.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-6xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'text-center mb-12', 'classes', 'content'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Our Gallery', 'text', 'headline'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white', 'classes', 'headline'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
            @if($showSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'A glimpse into our work and culture.', 'text', 'subheadline'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        @php $galleryGridClasses = content('__SLUG__', 'gallery_grid_classes', 'grid grid-cols-2 md:grid-cols-3 gap-4', 'classes', 'content'); @endphp
        @php $itemClasses = content('__SLUG__', 'item_classes', 'group relative rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square', 'classes', 'content'); @endphp
        @php $overlayClasses = content('__SLUG__', 'overlay_classes', 'absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center', 'classes', 'content'); @endphp
        @php $overlayTextClasses = content('__SLUG__', 'overlay_text_classes', 'text-white text-sm font-medium', 'classes', 'content'); @endphp
        <div class="{{ $galleryGridClasses }}">
            @for ($i = 1; $i <= 6; $i++)
                <div class="{{ $itemClasses }}">
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-sm">
                        Image {{ $i }}
                    </div>
                    <div class="{{ $overlayClasses }}">
                        <span class="{{ $overlayTextClasses }}">View Photo</span>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
