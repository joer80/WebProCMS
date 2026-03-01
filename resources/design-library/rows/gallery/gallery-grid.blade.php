{{--
@name Gallery - Grid
@description Masonry-style photo gallery grid with hover overlay.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-6xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        <div class="text-center mb-12">
            <h2 class="font-heading text-4xl font-bold text-zinc-900 dark:text-white">{{ content('__SLUG__', 'headline', 'Our Gallery', 'text', 'content') }}</h2>
            <p class="mt-4 text-zinc-500 dark:text-zinc-400">{{ content('__SLUG__', 'subheadline', 'A glimpse into our work and culture.', 'text', 'content') }}</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @for ($i = 1; $i <= 6; $i++)
                <div class="group relative rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square">
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-sm">
                        Image {{ $i }}
                    </div>
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <span class="text-white text-sm font-medium">View Photo</span>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
