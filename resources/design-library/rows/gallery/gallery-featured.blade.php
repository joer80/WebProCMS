{{--
@name Gallery - Featured
@description Large featured image with smaller thumbnail grid below.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Featured Gallery"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Our most celebrated photography."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"https://placehold.co/1200x600","alt":"Featured Photo","caption":"The main event"},{"image":"https://placehold.co/600x400","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 6","caption":""}]')
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes=""
        default-items='[{"image":"https://placehold.co/1200x600","alt":"Featured Photo","caption":"The main event"},{"image":"https://placehold.co/600x400","alt":"Photo 2","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 3","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 4","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 5","caption":""},{"image":"https://placehold.co/600x400","alt":"Photo 6","caption":""}]'>
        @php $featured = $galleryImages[0] ?? null; $rest = array_slice($galleryImages, 1); @endphp
        @if ($featured)
            @php $featuredUrl = $featured['image'] ? (str_starts_with($featured['image'], 'http') ? $featured['image'] : \Illuminate\Support\Facades\Storage::url($featured['image'])) : null; @endphp
            <x-dl.wrapper slug="__SLUG__" prefix="featured_wrapper"
                default-classes="rounded-card overflow-hidden aspect-[21/9] bg-zinc-100 dark:bg-zinc-800 mb-4">
                @if ($featuredUrl)
                    <img src="{{ $featuredUrl }}" alt="{{ $featured['alt'] }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">{{ $featured['alt'] ?: 'Featured Image' }}</div>
                @endif
            </x-dl.wrapper>
            @if ($featured['caption'] ?? '')
                <x-dl.wrapper slug="__SLUG__" prefix="featured_caption" tag="p"
                    default-classes="text-center text-sm text-zinc-400 mb-6">
                    {{ $featured['caption'] }}
                </x-dl.wrapper>
            @endif
        @endif
        <x-dl.wrapper slug="__SLUG__" prefix="thumb_grid"
            default-classes="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach ($rest as $img)
                @php $imgUrl = $img['image'] ? (str_starts_with($img['image'], 'http') ? $img['image'] : \Illuminate\Support\Facades\Storage::url($img['image'])) : null; @endphp
                <x-dl.card slug="__SLUG__" prefix="gallery_item"
                    data-editor-item-index="{{ $loop->index }}"
                    default-classes="rounded-card overflow-hidden aspect-square bg-zinc-100 dark:bg-zinc-800">
                    @if ($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $img['alt'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-400 text-xs">{{ $img['alt'] }}</div>
                    @endif
                </x-dl.card>
            @endforeach
        </x-dl.wrapper>
    </x-dl.gallery>
</x-dl.section>
