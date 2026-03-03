{{--
@name Gallery - Grid
@description Masonry-style photo gallery grid with hover overlay.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-6xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'text-center mb-12'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            <x-dl-heading slug="__SLUG__" prefix="headline" default="Our Gallery"
                default-tag="h2"
                default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
            <x-dl-subheadline slug="__SLUG__" prefix="subheadline" default="A glimpse into our work and culture."
                default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
        </div>
        @php $galleryGridClasses = content('__SLUG__', 'gallery_grid_classes', 'grid grid-cols-2 md:grid-cols-3 gap-4'); @endphp
        @php $itemClasses = content('__SLUG__', 'item_classes', 'group relative rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square'); @endphp
        @php $overlayClasses = content('__SLUG__', 'overlay_classes', 'absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center'); @endphp
        @php $overlayTextClasses = content('__SLUG__', 'overlay_text_classes', 'text-white text-sm font-medium'); @endphp
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
