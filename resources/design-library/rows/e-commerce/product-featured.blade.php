{{--
@name E-Commerce - Featured Product
@description Large two-column featured product showcase with details.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="columns_wrapper"
        default-classes="grid md:grid-cols-2 gap-12 items-center">
        <x-dl.image slug="__SLUG__" prefix="product_image"
            default-wrapper-classes="rounded-card overflow-hidden aspect-square bg-zinc-100 dark:bg-zinc-800"
            default-image-classes="w-full h-full object-cover" />
        <x-dl.wrapper slug="__SLUG__" prefix="product_details"
            default-classes="">
            <x-dl.wrapper slug="__SLUG__" prefix="product_badge" tag="span"
                default-classes="inline-block text-xs font-semibold uppercase tracking-widest text-primary mb-4">
                Featured Product
            </x-dl.wrapper>
            <x-dl.heading slug="__SLUG__" prefix="product_name" default="Product Name"
                default-tag="h1"
                default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-3" />
            <x-dl.subheadline slug="__SLUG__" prefix="product_desc" default="A detailed description of this incredible product and all the ways it will improve your life and workflow."
                default-classes="text-zinc-500 dark:text-zinc-400 mb-6" />
            <x-dl.grid slug="__SLUG__" prefix="product_features"
                default-grid-classes="space-y-2 mb-8"
                default-items='[{"feature":"Handcrafted with premium materials"},{"feature":"Lifetime warranty included"},{"feature":"Free shipping on all orders"},{"feature":"30-day return policy"}]'>
                @dlItems('__SLUG__', 'product_features', $productFeatures, '[{"feature":"Handcrafted with premium materials"},{"feature":"Lifetime warranty included"},{"feature":"Free shipping on all orders"},{"feature":"30-day return policy"}]')
                @foreach ($productFeatures as $item)
                    <x-dl.card slug="__SLUG__" prefix="product_feature"
                        default-classes="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                        <x-dl.icon slug="__SLUG__" prefix="feature_icon" name="check-circle:solid"
                            default-classes="size-4 text-primary shrink-0" />
                        {{ $item['feature'] }}
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
            <x-dl.wrapper slug="__SLUG__" prefix="price_cta_row"
                default-classes="flex items-center gap-4">
                <x-dl.wrapper slug="__SLUG__" prefix="product_price" tag="span"
                    default-classes="text-3xl font-black text-zinc-900 dark:text-white">
                    $149.99
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="add_to_cart_btn" tag="button"
                    default-classes="flex-1 px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                    Add to Cart
                </x-dl.wrapper>
            </x-dl.wrapper>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
