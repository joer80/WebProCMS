{{--
@name Gallery - Slider
@description Horizontally scrollable image gallery with prev/next buttons.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950 overflow-hidden"
    default-container-classes="max-w-container mx-auto"
    x-data="{
        scrollEl: null,
        init() { this.scrollEl = this.$el.querySelector('[data-scroll]'); },
        prev() { this.scrollEl.scrollBy({ left: -320, behavior: 'smooth' }); },
        next() { this.scrollEl.scrollBy({ left: 320, behavior: 'smooth' }); }
    }">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="flex items-center justify-between mb-8">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Gallery"
            default-tag="h2"
            default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.wrapper slug="__SLUG__" prefix="nav_wrapper"
            default-classes="flex items-center gap-3">
            <button @click="prev()" class="size-9 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-zinc-600 dark:text-zinc-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </button>
            <button @click="next()" class="size-9 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-zinc-600 dark:text-zinc-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>
        </x-dl.wrapper>
    </x-dl.wrapper>
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"https://placehold.co/600x400","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 6","caption":""}]')
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory"
        default-items='[{"image":"https://placehold.co/600x400","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 6","caption":""}]'
        data-scroll="">
        @foreach ($galleryImages as $img)
            @php $imgUrl = $img['image'] ? (str_starts_with($img['image'], 'http') ? $img['image'] : \Illuminate\Support\Facades\Storage::url($img['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="gallery_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="flex-none w-80 rounded-card overflow-hidden aspect-video bg-zinc-200 dark:bg-zinc-800 snap-start">
                @if ($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $img['alt'] }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">{{ $img['alt'] }}</div>
                @endif
            </x-dl.card>
        @endforeach
    </x-dl.gallery>
</x-dl.section>
