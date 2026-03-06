{{--
@name Gallery - Dark
@description Dark background image gallery grid.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Gallery"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Captured moments in time."
            default-classes="text-lg text-zinc-400" />
    </x-dl.wrapper>
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"https://placehold.co/600x600","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 6","caption":""}]')
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes="grid grid-cols-2 md:grid-cols-3 gap-3"
        default-items='[{"image":"https://placehold.co/600x600","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x600","alt":"Photo 6","caption":""}]'>
        @foreach ($galleryImages as $img)
            @php $imgUrl = $img['image'] ? (str_starts_with($img['image'], 'http') ? $img['image'] : \Illuminate\Support\Facades\Storage::url($img['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="gallery_item"
                default-classes="rounded-card overflow-hidden aspect-square bg-zinc-800 group">
                @if ($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $img['alt'] }}"
                        class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-600 text-sm">
                        {{ $img['alt'] ?: 'Image ' . ($loop->index + 1) }}
                    </div>
                @endif
            </x-dl.card>
        @endforeach
    </x-dl.gallery>
</x-dl.section>
