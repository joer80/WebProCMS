{{--
@name E-Commerce - Bundle
@description Product bundle offer with included items list and CTA.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="columns_wrapper"
        default-classes="grid md:grid-cols-2 gap-12 items-center">
        <x-dl.wrapper slug="__SLUG__" prefix="bundle_info"
            default-classes="">
            <x-dl.wrapper slug="__SLUG__" prefix="bundle_badge" tag="span"
                default-classes="inline-block text-xs font-bold uppercase tracking-widest bg-primary/10 text-primary px-3 py-1 rounded-full mb-4">
                Bundle Deal
            </x-dl.wrapper>
            <x-dl.heading slug="__SLUG__" prefix="headline" default="The Complete Kit"
                default-tag="h2"
                default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-4" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Everything you need to get started, bundled together at a special price."
                default-classes="text-zinc-500 dark:text-zinc-400 mb-8" />
            <x-dl.grid slug="__SLUG__" prefix="bundle_items"
                default-grid-classes="space-y-4 mb-8"
                default-items='[{"name":"Main Product","value":"$99.99"},{"name":"Accessory Pack","value":"$29.99"},{"name":"Premium Case","value":"$24.99"},{"name":"1-Year Warranty","value":"$19.99"}]'>
                @dlItems('__SLUG__', 'bundle_items', $bundleItems, '[{"name":"Main Product","value":"$99.99"},{"name":"Accessory Pack","value":"$29.99"},{"name":"Premium Case","value":"$24.99"},{"name":"1-Year Warranty","value":"$19.99"}]')
                @foreach ($bundleItems as $item)
                    <x-dl.card slug="__SLUG__" prefix="bundle_item"
            data-editor-item-index="{{ $loop->index }}"
                        default-classes="flex items-center justify-between">
                        <x-dl.wrapper slug="__SLUG__" prefix="item_name_row"
                            default-classes="flex items-center gap-3">
                            <x-dl.icon slug="__SLUG__" prefix="item_icon" name="check-circle:solid"
                                default-classes="size-4 text-primary shrink-0" />
                            <x-dl.wrapper slug="__SLUG__" prefix="item_name" tag="span"
                                default-classes="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $item['name'] }}
                            </x-dl.wrapper>
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="item_value" tag="span"
                            default-classes="text-sm text-zinc-400 line-through">
                            {{ $item['value'] }}
                        </x-dl.wrapper>
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
            <x-dl.wrapper slug="__SLUG__" prefix="price_block"
                default-classes="flex items-center gap-4 mb-6">
                <x-dl.wrapper slug="__SLUG__" prefix="bundle_price" tag="span"
                    default-classes="text-4xl font-black text-primary">
                    $129.99
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="original_price" tag="span"
                    default-classes="text-xl text-zinc-400 line-through">
                    $174.96
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="savings_badge" tag="span"
                    default-classes="text-sm font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                    Save 26%
                </x-dl.wrapper>
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="add_to_cart_btn" tag="button"
                default-classes="w-full md:w-auto px-8 py-4 bg-primary text-white font-bold rounded-lg hover:bg-primary/90 transition-colors text-base">
                Add Bundle to Cart
            </x-dl.wrapper>
        </x-dl.wrapper>
        <x-dl.image slug="__SLUG__" prefix="bundle_image"
            default-wrapper-classes="rounded-card overflow-hidden aspect-square bg-zinc-100 dark:bg-zinc-800"
            default-image-classes="w-full h-full object-cover" />
    </x-dl.wrapper>
</x-dl.section>
