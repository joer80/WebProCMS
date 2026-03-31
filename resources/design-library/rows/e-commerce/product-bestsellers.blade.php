{{--
@name E-Commerce - Best Sellers
@description Horizontally scrollable best-seller product cards.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950 overflow-hidden"
    default-container-classes="max-w-6xl mx-auto"
    x-data="{
        scrollEl: null,
        init() { this.scrollEl = this.$el.querySelector('[data-scroll]'); },
        prev() { this.scrollEl.scrollBy({ left: -260, behavior: 'smooth' }); },
        next() { this.scrollEl.scrollBy({ left: 260, behavior: 'smooth' }); }
    }">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="flex items-center justify-between mb-8">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Best Sellers"
            default-tag="h2"
            default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.wrapper slug="__SLUG__" prefix="nav_wrapper"
            default-classes="flex items-center gap-2">
            <button @click="prev()" class="size-9 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-zinc-600 dark:text-zinc-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </button>
            <button @click="next()" class="size-9 rounded-full border border-zinc-300 dark:border-zinc-600 flex items-center justify-center hover:border-primary hover:text-primary transition-colors text-zinc-600 dark:text-zinc-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>
        </x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="products"
        default-grid-classes="flex gap-5 overflow-x-auto pb-4 snap-x snap-mandatory"
        default-items='[{"name":"Product Alpha","price":"$49.99","badge":"#1"},{"name":"Product Beta","price":"$69.99","badge":"#2"},{"name":"Product Gamma","price":"$39.99","badge":"#3"},{"name":"Product Delta","price":"$89.99","badge":"#4"},{"name":"Product Epsilon","price":"$59.99","badge":"#5"}]'
        data-scroll="">
        @dlItems('__SLUG__', 'products', $products, '[{"name":"Product Alpha","price":"$49.99","badge":"#1"},{"name":"Product Beta","price":"$69.99","badge":"#2"},{"name":"Product Gamma","price":"$39.99","badge":"#3"},{"name":"Product Delta","price":"$89.99","badge":"#4"},{"name":"Product Epsilon","price":"$59.99","badge":"#5"}]')
        @foreach ($products as $product)
            <x-dl.card slug="__SLUG__" prefix="product_card"
            data-editor-item-index="{{ $loop->index }}"
                default-classes="flex-none w-56 snap-start group">
                <x-dl.wrapper slug="__SLUG__" prefix="image_wrapper"
                    default-classes="relative rounded-card aspect-square bg-zinc-100 dark:bg-zinc-800 mb-3 overflow-hidden">
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 text-xs">Image</div>
                    @if ($product['badge'] ?? '')
                        <x-dl.wrapper slug="__SLUG__" prefix="badge" tag="span"
                            default-classes="absolute top-2 left-2 text-xs font-bold bg-primary text-white px-2 py-0.5 rounded-full">
                            {{ $product['badge'] }}
                        </x-dl.wrapper>
                    @endif
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="product_name" tag="h3"
                    default-classes="text-sm font-semibold text-zinc-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
                    {{ $product['name'] }}
                </x-dl.wrapper>
                <x-dl.group slug="__SLUG__" prefix="price_row"
                    default-classes="flex items-center justify-between">
                    <x-dl.wrapper slug="__SLUG__" prefix="product_price" tag="span"
                        default-classes="font-bold text-zinc-900 dark:text-white">
                        {{ $product['price'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="product_cta" tag="button"
                        default-classes="text-xs px-3 py-1.5 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                        Add
                    </x-dl.wrapper>
                </x-dl.group>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
