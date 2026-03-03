{{--
@name E-Commerce - Product Grid
@description Four-column product card grid with image, name, price, and add-to-cart button.
@sort 10
--}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
        <x-dl-wrapper slug="__SLUG__" prefix="header_wrapper"
            default-classes="flex items-center justify-between mb-12">
            <x-dl-heading slug="__SLUG__" prefix="headline" default="Featured Products"
                default-tag="h2"
                default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
            <x-dl-link slug="__SLUG__" prefix="view_all"
                default-label="View all →"
                default-url="/products"
                default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
        </x-dl-wrapper>
        <x-dl-grid slug="__SLUG__" prefix="products"
            default-grid-classes="grid grid-cols-2 md:grid-cols-4 gap-6"
            default-items='[{"name":"Product Alpha","desc":"Short product description here.","price":"$49.99"},{"name":"Product Beta","desc":"Short product description here.","price":"$59.99"},{"name":"Product Gamma","desc":"Short product description here.","price":"$79.99"},{"name":"Product Delta","desc":"Short product description here.","price":"$99.99"}]'>
            @dlItems('__SLUG__', 'products', $products)
            @foreach ($products as $product)
                <x-dl-wrapper slug="__SLUG__" prefix="product_card"
                    default-classes="group">
                    <x-dl-wrapper slug="__SLUG__" prefix="image_wrapper"
                        default-classes="rounded-card bg-zinc-100 dark:bg-zinc-800 aspect-square mb-4 overflow-hidden">
                        <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-sm">Image</div>
                    </x-dl-wrapper>
                    <x-dl-wrapper slug="__SLUG__" prefix="product_name" tag="h3"
                        default-classes="font-semibold text-zinc-900 dark:text-white">
                        {{ $product['name'] }}
                    </x-dl-wrapper>
                    <x-dl-wrapper slug="__SLUG__" prefix="product_desc" tag="p"
                        default-classes="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        {{ $product['desc'] }}
                    </x-dl-wrapper>
                    <x-dl-wrapper slug="__SLUG__" prefix="price_row"
                        default-classes="mt-3 flex items-center justify-between">
                        <x-dl-wrapper slug="__SLUG__" prefix="price" tag="span"
                            default-classes="font-bold text-zinc-900 dark:text-white">
                            {{ $product['price'] }}
                        </x-dl-wrapper>
                        <x-dl-wrapper slug="__SLUG__" prefix="button" tag="button"
                            default-classes="px-3 py-1.5 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                            Add to Cart
                        </x-dl-wrapper>
                    </x-dl-wrapper>
                </x-dl-wrapper>
            @endforeach
        </x-dl-grid>
</x-dl-section>
