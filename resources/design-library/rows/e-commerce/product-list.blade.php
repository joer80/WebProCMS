{{--
@name E-Commerce - Product List
@description Horizontal product list with image, details, and CTA button.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-4xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Our Products"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-10" />
    <x-dl.grid slug="__SLUG__" prefix="products"
        default-grid-classes="space-y-6"
        default-items='[{"name":"Product Alpha","desc":"The most popular choice for professionals who need speed and reliability.","price":"$49.99","badge":""},{"name":"Product Beta","desc":"Advanced features for teams that need more power and integrations.","price":"$79.99","badge":"Best Seller"},{"name":"Product Gamma","desc":"Enterprise-grade with dedicated support and custom SLA agreements.","price":"$129.99","badge":""}]'>
        @dlItems('__SLUG__', 'products', $products, '[{"name":"Product Alpha","desc":"The most popular choice for professionals who need speed and reliability.","price":"$49.99","badge":""},{"name":"Product Beta","desc":"Advanced features for teams that need more power and integrations.","price":"$79.99","badge":"Best Seller"},{"name":"Product Gamma","desc":"Enterprise-grade with dedicated support and custom SLA agreements.","price":"$129.99","badge":""}]')
        @foreach ($products as $product)
            <x-dl.card slug="__SLUG__" prefix="product_row"
                default-classes="flex items-center gap-6 p-6 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary transition-colors">
                <x-dl.wrapper slug="__SLUG__" prefix="product_image"
                    default-classes="size-20 shrink-0 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 text-xs overflow-hidden">
                    Image
                </x-dl.wrapper>
                <x-dl.group slug="__SLUG__" prefix="product_info"
                    default-classes="flex-1 min-w-0">
                    <x-dl.wrapper slug="__SLUG__" prefix="product_name" tag="h3"
                        default-classes="font-semibold text-zinc-900 dark:text-white mb-1">
                        {{ $product['name'] }}
                        @if ($product['badge'] ?? '')
                            <x-dl.wrapper slug="__SLUG__" prefix="product_badge" tag="span"
                                default-classes="ml-2 text-xs font-medium bg-primary/10 text-primary px-2 py-0.5 rounded-full">
                                {{ $product['badge'] }}
                            </x-dl.wrapper>
                        @endif
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="product_desc" tag="p"
                        default-classes="text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                        {{ $product['desc'] }}
                    </x-dl.wrapper>
                </x-dl.group>
                <x-dl.group slug="__SLUG__" prefix="product_action"
                    default-classes="shrink-0 text-right">
                    <x-dl.wrapper slug="__SLUG__" prefix="product_price" tag="span"
                        default-classes="block text-xl font-bold text-zinc-900 dark:text-white mb-2">
                        {{ $product['price'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="product_cta" tag="button"
                        default-classes="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                        Add to Cart
                    </x-dl.wrapper>
                </x-dl.group>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
