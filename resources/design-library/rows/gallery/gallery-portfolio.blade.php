{{--
@name Gallery - Portfolio
@description Portfolio grid with title, category label, and image per item.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="flex items-end justify-between mb-10">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Portfolio"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.link slug="__SLUG__" prefix="view_all"
            default-label="View all projects →"
            default-url="/portfolio"
            default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
    </x-dl.wrapper>
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"https://placehold.co/800x600","alt":"Project Alpha","caption":"Web Design"},{"image":"https://placehold.co/800x600","alt":"Project Beta","caption":"Branding"},{"image":"https://placehold.co/800x600","alt":"Project Gamma","caption":"Mobile App"},{"image":"https://placehold.co/800x600","alt":"Project Delta","caption":"E-commerce"},{"image":"https://placehold.co/800x600","alt":"Project Epsilon","caption":"Motion"},{"image":"https://placehold.co/800x600","alt":"Project Zeta","caption":"UI/UX"}]')
    <x-dl.gallery slug="__SLUG__" prefix="images"
        default-grid-classes="grid md:grid-cols-3 gap-6"
        default-items='[{"image":"https://placehold.co/800x600","alt":"Project Alpha","caption":"Web Design"},{"image":"https://placehold.co/800x600","alt":"Project Beta","caption":"Branding"},{"image":"https://placehold.co/800x600","alt":"Project Gamma","caption":"Mobile App"},{"image":"https://placehold.co/800x600","alt":"Project Delta","caption":"E-commerce"},{"image":"https://placehold.co/800x600","alt":"Project Epsilon","caption":"Motion"},{"image":"https://placehold.co/800x600","alt":"Project Zeta","caption":"UI/UX"}]'>
        @foreach ($galleryImages as $img)
            @php $imgUrl = $img['image'] ? (str_starts_with($img['image'], 'http') ? $img['image'] : \Illuminate\Support\Facades\Storage::url($img['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="gallery_item"
                default-classes="group">
                <x-dl.wrapper slug="__SLUG__" prefix="image_wrapper"
                    default-classes="rounded-card overflow-hidden aspect-video bg-zinc-200 dark:bg-zinc-800 mb-4">
                    @if ($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $img['alt'] }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-zinc-400 text-sm">{{ $img['alt'] }}</div>
                    @endif
                </x-dl.wrapper>
                @if ($img['caption'] ?? '')
                    <x-dl.wrapper slug="__SLUG__" prefix="item_category" tag="span"
                        default-classes="text-xs font-semibold uppercase tracking-widest text-primary">
                        {{ $img['caption'] }}
                    </x-dl.wrapper>
                @endif
                <x-dl.wrapper slug="__SLUG__" prefix="item_title" tag="h3"
                    default-classes="text-base font-semibold text-zinc-900 dark:text-white mt-1">
                    {{ $img['alt'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.gallery>
</x-dl.section>
