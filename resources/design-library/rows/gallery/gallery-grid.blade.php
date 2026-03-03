{{--
@name Gallery - Grid
@description Masonry-style photo gallery grid with hover overlay.
@sort 10
--}}
{{-- TODO: Convert @for placeholder loop to grid_images (items: image, alt, caption) and use x-dl-grid. Verify editor supports image-type keys within grid item sub-fields first. --}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
        <x-dl-wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
            <x-dl-heading slug="__SLUG__" prefix="headline" default="Our Gallery"
                default-tag="h2"
                default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
            <x-dl-subheadline slug="__SLUG__" prefix="subheadline" default="A glimpse into our work and culture."
                default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
        </x-dl-wrapper>
        <x-dl-wrapper slug="__SLUG__" prefix="gallery_grid"
            default-classes="grid grid-cols-2 md:grid-cols-3 gap-4">
            @for ($i = 1; $i <= 6; $i++)
                <x-dl-wrapper slug="__SLUG__" prefix="item"
                    default-classes="group relative rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square">
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-sm">
                        Image {{ $i }}
                    </div>
                    <x-dl-wrapper slug="__SLUG__" prefix="overlay"
                        default-classes="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <x-dl-wrapper slug="__SLUG__" prefix="overlay_text" tag="span"
                            default-classes="text-white text-sm font-medium">
                            View Photo
                        </x-dl-wrapper>
                    </x-dl-wrapper>
                </x-dl-wrapper>
            @endfor
        </x-dl-wrapper>
</x-dl-section>
