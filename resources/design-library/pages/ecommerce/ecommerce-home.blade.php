{{--
@name eCommerce Home Page
@description eCommerce homepage with hero, featured products, and promotional banner.
@sort 10
--}}
<div>
    <section class="py-20 px-6 bg-zinc-900 text-white text-center">
        <div class="max-w-3xl mx-auto">
            <p class="text-sm font-semibold text-primary uppercase tracking-widest mb-4">New Arrivals</p>
            <h1 class="text-5xl font-bold leading-tight">Shop the Latest Collection</h1>
            <p class="mt-6 text-zinc-400 text-lg">Discover our handpicked selection of premium products.</p>
            <a href="/products" class="mt-8 inline-block px-8 py-3 bg-white text-zinc-900 font-semibold rounded-lg hover:bg-zinc-100 transition-colors">Shop Now</a>
        </div>
    </section>
    <section class="py-20 px-6 bg-white dark:bg-zinc-900">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-zinc-900 dark:text-white mb-10">Featured Products</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach (['Product One', 'Product Two', 'Product Three', 'Product Four'] as $p)
                    <div>
                        <div class="rounded-xl bg-zinc-100 dark:bg-zinc-800 aspect-square mb-3 flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-sm">Image</div>
                        <p class="font-medium text-zinc-900 dark:text-white text-sm">{{ $p }}</p>
                        <p class="font-bold text-zinc-900 dark:text-white mt-1">$49.99</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
