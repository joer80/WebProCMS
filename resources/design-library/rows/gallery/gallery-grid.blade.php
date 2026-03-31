{{--
@name Gallery - Grid
@description Masonry-style photo gallery grid with optional lightbox on click.
@sort 10
--}}
@php $galleryLightboxEnabled = content('__SLUG__', 'toggle_lightbox', '1') === '1'; @endphp
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto"
    x-data="{
        lightboxEnabled: {{ $galleryLightboxEnabled ? 'true' : 'false' }},
        lightboxOpen: false,
        lightboxIndex: 0,
        images: [],
        init() {
            const imgs = this.$el.querySelectorAll('img[data-lightbox-src]');
            this.images = Array.from(imgs).map(img => ({
                src: img.dataset.lightboxSrc,
                alt: img.alt,
                caption: img.dataset.lightboxCaption || ''
            }));
        },
        open(imgEl) {
            if (!this.lightboxEnabled || !imgEl) return;
            const idx = this.images.findIndex(i => i.src === imgEl.dataset.lightboxSrc);
            if (idx !== -1) { this.lightboxIndex = idx; this.lightboxOpen = true; }
        },
        prev() { this.lightboxIndex = (this.lightboxIndex - 1 + this.images.length) % this.images.length; },
        next() { this.lightboxIndex = (this.lightboxIndex + 1) % this.images.length; }
    }">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Gallery"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="A glimpse into our work and culture."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"https://placehold.co/600x600","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 6","caption":""}]')
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes="grid grid-cols-2 md:grid-cols-3 gap-4"
        default-items='[{"image":"https://placehold.co/600x600","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 6","caption":""}]'>
        @foreach ($galleryImages as $img)
            @php $imgUrl = $img['image'] ? (str_starts_with($img['image'], 'http') ? $img['image'] : Storage::url($img['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="gallery_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square"
                @click="open($el.querySelector('img[data-lightbox-src]'))"
                x-bind:class="lightboxEnabled && $el.querySelector('img[data-lightbox-src]') ? 'cursor-zoom-in' : ''">
                @if ($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $img['alt'] }}"
                        data-lightbox-src="{{ $imgUrl }}"
                        data-lightbox-caption="{{ $img['caption'] ?? '' }}"
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-sm">
                        {{ $img['alt'] ?: 'Image ' . ($loop->index + 1) }}
                    </div>
                @endif
            </x-dl.card>
        @endforeach
    </x-dl.gallery>

    {{-- Lightbox overlay — functional UI, teleported to body to escape stacking contexts --}}
    <template x-teleport="body">
        <div x-show="lightboxOpen" style="display:none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click.self="lightboxOpen = false"
            @keydown.escape.window="lightboxOpen = false"
            @keydown.arrow-left.window="if (lightboxOpen) prev()"
            @keydown.arrow-right.window="if (lightboxOpen) next()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90">

            {{-- Close --}}
            <button @click="lightboxOpen = false" aria-label="Close"
                class="absolute top-4 right-4 text-white/60 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Prev --}}
            <button x-show="images.length > 1" @click="prev()" aria-label="Previous"
                class="absolute left-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            {{-- Image --}}
            <div class="flex items-center justify-center px-16 max-h-[90vh]">
                <template x-for="(img, i) in images" :key="i">
                    <div x-show="lightboxIndex === i" class="flex flex-col items-center gap-3">
                        <img :src="img.src" :alt="img.alt" class="max-h-[82vh] max-w-[85vw] object-contain">
                        <p x-show="img.caption" x-text="img.caption" class="text-white/70 text-sm text-center"></p>
                    </div>
                </template>
            </div>

            {{-- Next --}}
            <button x-show="images.length > 1" @click="next()" aria-label="Next"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>

            {{-- Counter --}}
            <div x-show="images.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2">
                <span x-text="`${lightboxIndex + 1} / ${images.length}`" class="text-white/50 text-sm tabular-nums"></span>
            </div>
        </div>
    </template>
</x-dl.section>
