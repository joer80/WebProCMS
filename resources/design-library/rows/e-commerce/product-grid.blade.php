{{--
@name E-Commerce - Product Grid
@description Four-column product card grid with image, name, price, and add-to-cart button.
@sort 10
--}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white">Featured Products</h2>
            <a href="/products" class="text-primary font-semibold hover:text-primary/80 transition-colors text-sm">View all →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach (['Product Alpha', 'Product Beta', 'Product Gamma', 'Product Delta'] as $product)
                <div class="group">
                    <div class="rounded-xl bg-zinc-100 dark:bg-zinc-800 aspect-square mb-4 overflow-hidden">
                        <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-sm">Image</div>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $product }}</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Short product description here.</p>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="font-bold text-zinc-900 dark:text-white">$49.99</span>
                        <button class="px-3 py-1.5 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                            Add to Cart
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
