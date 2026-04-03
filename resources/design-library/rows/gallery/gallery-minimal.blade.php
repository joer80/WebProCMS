{{--
@name Gallery - Minimal
@description No-frills photo grid with tight gaps and no rounded corners.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"https://placehold.co/600x400","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 6","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 7","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 8","caption":""}]')
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes="grid grid-cols-2 md:grid-cols-4 gap-1"
        default-items='[{"image":"https://placehold.co/600x400","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 6","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 7","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 8","caption":""}]'>
        @foreach ($galleryImages as $img)
            @php $imgUrl = $img['image'] ? (str_starts_with($img['image'], 'http') ? $img['image'] : \Illuminate\Support\Facades\Storage::url($img['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="gallery_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="aspect-square overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                @if ($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $img['alt'] }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 text-xs">
                        {{ $img['alt'] ?: 'Image ' . ($loop->index + 1) }}
                    </div>
                @endif
            </x-dl.card>
        @endforeach
    </x-dl.gallery>
</x-dl.section>
