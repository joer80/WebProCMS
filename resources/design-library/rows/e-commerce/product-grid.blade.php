{{--
@name E-Commerce - Product Grid
@description Four-column product card grid with image, name, price, and add-to-cart button.
@sort 10
--}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'flex items-center justify-between mb-12'); @endphp
        @php $viewAllClasses = content('__SLUG__', 'view_all_classes', 'text-primary font-semibold hover:text-primary/80 transition-colors text-sm'); @endphp
        @php $productsGridClasses = content('__SLUG__', 'products_grid_classes', 'grid grid-cols-2 md:grid-cols-4 gap-6'); @endphp
        @php $productCardClasses = content('__SLUG__', 'product_card_classes', 'group'); @endphp
        @php $imageWrapperClasses = content('__SLUG__', 'image_wrapper_classes', 'rounded-card bg-zinc-100 dark:bg-zinc-800 aspect-square mb-4 overflow-hidden'); @endphp
        @php $productNameClasses = content('__SLUG__', 'product_name_classes', 'font-semibold text-zinc-900 dark:text-white'); @endphp
        @php $productDescClasses = content('__SLUG__', 'product_desc_classes', 'text-sm text-zinc-500 dark:text-zinc-400 mt-1'); @endphp
        @php $priceRowClasses = content('__SLUG__', 'price_row_classes', 'mt-3 flex items-center justify-between'); @endphp
        @php $priceClasses = content('__SLUG__', 'price_classes', 'font-bold text-zinc-900 dark:text-white'); @endphp
        @php $buttonClasses = content('__SLUG__', 'button_classes', 'px-3 py-1.5 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary/90 transition-colors'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            <x-dl-heading slug="__SLUG__" prefix="headline" default="Featured Products"
                default-tag="h2"
                default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
            <a href="/products" class="{{ $viewAllClasses }}">View all →</a>
        </div>
        <div class="{{ $productsGridClasses }}">
            @foreach (['Product Alpha', 'Product Beta', 'Product Gamma', 'Product Delta'] as $product)
                <div class="{{ $productCardClasses }}">
                    <div class="{{ $imageWrapperClasses }}">
                        <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-sm">Image</div>
                    </div>
                    <h3 class="{{ $productNameClasses }}">{{ $product }}</h3>
                    <p class="{{ $productDescClasses }}">Short product description here.</p>
                    <div class="{{ $priceRowClasses }}">
                        <span class="{{ $priceClasses }}">$49.99</span>
                        <button class="{{ $buttonClasses }}">
                            Add to Cart
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
</x-dl-section>
