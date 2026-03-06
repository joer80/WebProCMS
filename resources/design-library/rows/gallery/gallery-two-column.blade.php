{{--
@name Gallery - Two Column
@description Simple two-column image gallery with captions.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Work"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Projects we're proud of."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"https://placehold.co/800x600","alt":"Project 1","caption":"Modern Web App"},{"image":"https://placehold.co/800x600","alt":"Project 2","caption":"Mobile Experience"},{"image":"https://placehold.co/800x600","alt":"Project 3","caption":"Brand Identity"},{"image":"https://placehold.co/800x600","alt":"Project 4","caption":"E-commerce Platform"}]')
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes="grid md:grid-cols-2 gap-8"
        default-items='[{"image":"https://placehold.co/800x600","alt":"Project 1","caption":"Modern Web App"},{"image":"https://placehold.co/800x600","alt":"Project 2","caption":"Mobile Experience"},{"image":"https://placehold.co/800x600","alt":"Project 3","caption":"Brand Identity"},{"image":"https://placehold.co/800x600","alt":"Project 4","caption":"E-commerce Platform"}]'>
        @foreach ($galleryImages as $img)
            @php $imgUrl = $img['image'] ? (str_starts_with($img['image'], 'http') ? $img['image'] : \Illuminate\Support\Facades\Storage::url($img['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="gallery_item"
                default-classes="group">
                <x-dl.wrapper slug="__SLUG__" prefix="image_wrapper"
                    default-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800 mb-3">
                    @if ($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $img['alt'] }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">{{ $img['alt'] }}</div>
                    @endif
                </x-dl.wrapper>
                @if ($img['caption'] ?? '')
                    <x-dl.wrapper slug="__SLUG__" prefix="image_caption" tag="p"
                        default-classes="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                        {{ $img['caption'] }}
                    </x-dl.wrapper>
                @endif
            </x-dl.card>
        @endforeach
    </x-dl.gallery>
</x-dl.section>
