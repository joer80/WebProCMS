{{--
@name Gallery - Full Width
@description Edge-to-edge image strip with no container max-width constraint.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section bg-white dark:bg-zinc-900"
    default-container-classes="">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-10 px-6">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Work"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Edge to edge. No limits."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"https://placehold.co/600x400","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 5","caption":""}]')
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes="grid grid-cols-2 md:grid-cols-5 gap-0"
        default-items='[{"image":"https://placehold.co/600x400","alt":"Photo 1","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 5","caption":""}]'>
        @foreach ($galleryImages as $img)
            @php $imgUrl = $img['image'] ? (str_starts_with($img['image'], 'http') ? $img['image'] : \Illuminate\Support\Facades\Storage::url($img['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="gallery_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="aspect-square overflow-hidden bg-zinc-200 dark:bg-zinc-800 group">
                @if ($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $img['alt'] }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">{{ $img['alt'] }}</div>
                @endif
            </x-dl.card>
        @endforeach
    </x-dl.gallery>
</x-dl.section>
