{{--
@name E-Commerce - Categories
@description Category cards with image and label, linking to product category pages.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Shop by Category"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Explore our collections."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="categories"
        default-grid-classes="grid grid-cols-2 md:grid-cols-4 gap-6"
        default-items='[{"name":"Electronics","count":"128 products","image":"","url":"#"},{"name":"Clothing","count":"256 products","image":"","url":"#"},{"name":"Home & Garden","count":"89 products","image":"","url":"#"},{"name":"Sports","count":"64 products","image":"","url":"#"}]'>
        @dlItems('__SLUG__', 'categories', $categories, '[{"name":"Electronics","count":"128 products","image":"","url":"#"},{"name":"Clothing","count":"256 products","image":"","url":"#"},{"name":"Home & Garden","count":"89 products","image":"","url":"#"},{"name":"Sports","count":"64 products","image":"","url":"#"}]')
        @foreach ($categories as $cat)
            @php $imgUrl = $cat['image'] ? (str_starts_with($cat['image'], 'http') ? $cat['image'] : \Illuminate\Support\Facades\Storage::url($cat['image'])) : null; @endphp
            <x-dl.card slug="__SLUG__" prefix="category_card" tag="a"
                data-editor-item-index="{{ $loop->index }}"
                href="{{ $cat['url'] }}"
                default-classes="group relative rounded-card overflow-hidden aspect-square bg-zinc-100 dark:bg-zinc-800 block">
                @if ($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $cat['name'] }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @endif
                <x-dl.wrapper slug="__SLUG__" prefix="category_overlay"
                    default-classes="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                    <x-dl.group slug="__SLUG__" prefix="category_text"
                        default-classes="">
                        <x-dl.wrapper slug="__SLUG__" prefix="category_name" tag="h3"
                            default-classes="text-white font-semibold">
                            {{ $cat['name'] }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="category_count" tag="p"
                            default-classes="text-white/70 text-xs">
                            {{ $cat['count'] }}
                        </x-dl.wrapper>
                    </x-dl.group>
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
