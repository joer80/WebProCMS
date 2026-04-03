{{--
@name Gallery - With Captions
@description Photo grid with overlay captions on hover.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Photo Gallery"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Hover to reveal captions."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"https://placehold.co/600x600","alt":"Photo 1","caption":"A beautiful moment"},{"image":"https://placehold.co/600x600","alt":"Photo 2","caption":"Nature at its finest"},{"image":"https://placehold.co/600x600","alt":"Photo 3","caption":"City lights at dusk"},{"image":"https://placehold.co/600x600","alt":"Photo 4","caption":"Mountain reflections"},{"image":"https://placehold.co/600x600","alt":"Photo 5","caption":"Golden hour"},{"image":"https://placehold.co/600x600","alt":"Photo 6","caption":"Ocean breeze"}]')
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes="grid grid-cols-2 md:grid-cols-3 gap-4"
        default-items='[{"image":"https://placehold.co/600x600","alt":"Photo 1","caption":"A beautiful moment"},{"image":"https://placehold.co/600x600","alt":"Photo 2","caption":"Nature at its finest"},{"image":"https://placehold.co/600x600","alt":"Photo 3","caption":"City lights at dusk"},{"image":"https://placehold.co/600x600","alt":"Photo 4","caption":"Mountain reflections"},{"image":"https://placehold.co/600x600","alt":"Photo 5","caption":"Golden hour"},{"image":"https://placehold.co/600x600","alt":"Photo 6","caption":"Ocean breeze"}]'>
        @foreach ($galleryImages as $img)
            @php $imgUrl = $img['image'] ? (str_starts_with($img['image'], 'http') ? $img['image'] : \Illuminate\Support\Facades\Storage::url($img['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="gallery_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="group relative rounded-card overflow-hidden aspect-square bg-zinc-100 dark:bg-zinc-800">
                @if ($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $img['alt'] }}"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    @if ($img['caption'] ?? '')
                        <x-dl.wrapper slug="__SLUG__" prefix="caption_overlay"
                            default-classes="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <x-dl.wrapper slug="__SLUG__" prefix="caption_text" tag="span"
                                default-classes="text-white text-sm font-medium">
                                {{ $img['caption'] }}
                            </x-dl.wrapper>
                        </x-dl.wrapper>
                    @endif
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">
                        {{ $img['alt'] ?: 'Image ' . ($loop->index + 1) }}
                    </div>
                @endif
            </x-dl.card>
        @endforeach
    </x-dl.gallery>
</x-dl.section>
