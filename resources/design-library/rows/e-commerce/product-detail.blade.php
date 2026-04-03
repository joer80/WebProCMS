{{--
@name E-Commerce - Product Detail
@description Full product detail section with image, specs, and add-to-cart.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="columns_wrapper"
        default-classes="grid md:grid-cols-2 gap-12 items-start">
        <x-dl.image slug="__SLUG__" prefix="product_image"
            default-wrapper-classes="rounded-card overflow-hidden aspect-square bg-zinc-100 dark:bg-zinc-800 sticky top-20"
            default-image-classes="w-full h-full object-cover" />
        <x-dl.wrapper slug="__SLUG__" prefix="product_details"
            default-classes="">
            <x-dl.wrapper slug="__SLUG__" prefix="breadcrumb" tag="nav"
                default-classes="text-xs text-zinc-400 mb-4">
                Products / Category / This Product
            </x-dl.wrapper>
            <x-dl.heading slug="__SLUG__" prefix="product_name" default="Premium Product Name"
                default-tag="h1"
                default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-2" />
            <x-dl.wrapper slug="__SLUG__" prefix="rating_row"
                default-classes="flex items-center gap-2 mb-4">
                <x-dl.wrapper slug="__SLUG__" prefix="stars" tag="span"
                    default-classes="text-yellow-400">
                    ★★★★★
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="review_count" tag="span"
                    default-classes="text-sm text-zinc-400">
                    (124 reviews)
                </x-dl.wrapper>
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="product_price" tag="span"
                default-classes="text-4xl font-black text-zinc-900 dark:text-white block mb-6">
                $149.99
            </x-dl.wrapper>
            <x-dl.subheadline slug="__SLUG__" prefix="product_desc" default="This premium product is crafted with the finest materials and designed to last a lifetime. It is the perfect choice for professionals and enthusiasts alike."
                default-classes="text-zinc-500 dark:text-zinc-400 leading-relaxed mb-8" />
            <x-dl.grid slug="__SLUG__" prefix="product_specs"
                default-grid-classes="grid grid-cols-2 gap-4 mb-8"
                default-items='[{"label":"Material","value":"Premium Aluminum"},{"label":"Weight","value":"320g"},{"label":"Dimensions","value":"15 × 10 × 3 cm"},{"label":"Warranty","value":"2 Years"}]'>
                @dlItems('__SLUG__', 'product_specs', $productSpecs, '[{"label":"Material","value":"Premium Aluminum"},{"label":"Weight","value":"320g"},{"label":"Dimensions","value":"15 x 10 x 3 cm"},{"label":"Warranty","value":"2 Years"}]')
                @foreach ($productSpecs as $spec)
                    <x-dl.card slug="__SLUG__" prefix="spec_item"
            data-editor-item-index="{{ $loop->index }}"
                        default-classes="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3">
                        <x-dl.wrapper slug="__SLUG__" prefix="spec_label" tag="span"
                            default-classes="block text-xs text-zinc-400 mb-0.5">
                            {{ $spec['label'] }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="spec_value" tag="span"
                            default-classes="text-sm font-semibold text-zinc-900 dark:text-white">
                            {{ $spec['value'] }}
                        </x-dl.wrapper>
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
            <x-dl.wrapper slug="__SLUG__" prefix="cta_group"
                default-classes="flex gap-4">
                <x-dl.wrapper slug="__SLUG__" prefix="add_to_cart" tag="button"
                    default-classes="flex-1 px-6 py-4 bg-primary text-white font-bold rounded-lg hover:bg-primary/90 transition-colors">
                    Add to Cart
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="wishlist_btn" tag="button"
                    default-classes="size-14 border border-zinc-300 dark:border-zinc-600 rounded-lg flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-zinc-600 dark:text-zinc-400">
                    <x-dl.icon slug="__SLUG__" prefix="wishlist_icon" name="heart"
                        default-classes="size-5" />
                </x-dl.wrapper>
            </x-dl.wrapper>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
