{{--
@name E-Commerce - Sale
@description Sale/promotional products grid with discount badges.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-red-50 dark:bg-zinc-950"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="flex items-center justify-between mb-10">
        <x-dl.wrapper slug="__SLUG__" prefix="headline_group"
            default-classes="flex items-center gap-4">
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Sale"
                default-tag="h2"
                default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
            <x-dl.wrapper slug="__SLUG__" prefix="sale_badge" tag="span"
                default-classes="px-3 py-1 bg-red-500 text-white text-sm font-bold rounded-full">
                Up to 50% off
            </x-dl.wrapper>
        </x-dl.wrapper>
        <x-dl.link slug="__SLUG__" prefix="view_all"
            default-label="View all sale items →"
            default-url="/sale"
            default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="products"
        default-grid-classes="grid grid-cols-2 md:grid-cols-4 gap-6"
        default-items='[{"name":"Product Alpha","original_price":"$99.99","sale_price":"$49.99","discount":"50% off"},{"name":"Product Beta","original_price":"$79.99","sale_price":"$55.99","discount":"30% off"},{"name":"Product Gamma","original_price":"$59.99","sale_price":"$39.99","discount":"33% off"},{"name":"Product Delta","original_price":"$149.99","sale_price":"$89.99","discount":"40% off"}]'>
        @dlItems('__SLUG__', 'products', $products, '[{"name":"Product Alpha","original_price":"$99.99","sale_price":"$49.99","discount":"50% off"},{"name":"Product Beta","original_price":"$79.99","sale_price":"$55.99","discount":"30% off"},{"name":"Product Gamma","original_price":"$59.99","sale_price":"$39.99","discount":"33% off"},{"name":"Product Delta","original_price":"$149.99","sale_price":"$89.99","discount":"40% off"}]')
        @foreach ($products as $product)
            <x-dl.card slug="__SLUG__" prefix="product_card"
            data-editor-item-index="{{ $loop->index }}"
                default-classes="group">
                <x-dl.wrapper slug="__SLUG__" prefix="image_wrapper"
                    default-classes="relative rounded-card aspect-square bg-zinc-100 dark:bg-zinc-800 mb-3 overflow-hidden">
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 text-xs">Image</div>
                    @if ($product['discount'] ?? '')
                        <x-dl.wrapper slug="__SLUG__" prefix="discount_badge" tag="span"
                            default-classes="absolute top-2 right-2 text-xs font-bold bg-red-500 text-white px-2 py-0.5 rounded-full">
                            {{ $product['discount'] }}
                        </x-dl.wrapper>
                    @endif
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="product_name" tag="h3"
                    default-classes="text-sm font-semibold text-zinc-900 dark:text-white mb-1 group-hover:text-primary transition-colors">
                    {{ $product['name'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="price_row"
                    default-classes="flex items-center gap-2">
                    <x-dl.wrapper slug="__SLUG__" prefix="sale_price" tag="span"
                        default-classes="font-bold text-red-500">
                        {{ $product['sale_price'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="original_price" tag="span"
                        default-classes="text-xs text-zinc-400 line-through">
                        {{ $product['original_price'] }}
                    </x-dl.wrapper>
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
