{{--
@name Gallery - Masonry
@description CSS column-based masonry photo gallery.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Gallery"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="A collection of our finest work."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"https://placehold.co/600x800","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x900","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x500","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x700","alt":"Photo 6","caption":""}]')
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes="columns-2 md:columns-3 gap-4 space-y-4"
        default-items='[{"image":"https://placehold.co/600x800","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x900","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x500","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x700","alt":"Photo 6","caption":""}]'>
        @foreach ($galleryImages as $img)
            @php $imgUrl = $img['image'] ? (str_starts_with($img['image'], 'http') ? $img['image'] : \Illuminate\Support\Facades\Storage::url($img['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="gallery_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="rounded-card overflow-hidden break-inside-avoid bg-zinc-100 dark:bg-zinc-800">
                @if ($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $img['alt'] }}"
                        class="w-full h-auto object-cover">
                @else
                    <div class="w-full aspect-square flex items-center justify-center text-zinc-400 text-sm">
                        {{ $img['alt'] ?: 'Image ' . ($loop->index + 1) }}
                    </div>
                @endif
            </x-dl.card>
        @endforeach
    </x-dl.gallery>
</x-dl.section>
