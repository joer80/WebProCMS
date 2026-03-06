{{--
@name E-Commerce - Reviews
@description Customer reviews with star ratings and review text.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Customer Reviews"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.wrapper slug="__SLUG__" prefix="rating_summary"
            default-classes="flex items-center justify-center gap-2">
            <x-dl.wrapper slug="__SLUG__" prefix="rating_score" tag="span"
                default-classes="text-3xl font-black text-zinc-900 dark:text-white">
                4.8
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="rating_stars" tag="span"
                default-classes="text-yellow-400 text-xl">
                ★★★★★
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="rating_count" tag="span"
                default-classes="text-zinc-400 text-sm">
                (2,847 reviews)
            </x-dl.wrapper>
        </x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="reviews"
        default-grid-classes="grid md:grid-cols-3 gap-6"
        default-items='[{"name":"Sarah M.","stars":"5","review":"Absolutely love this product! Exceeded all my expectations. Quality is top-notch and delivery was fast."},{"name":"James K.","stars":"5","review":"Best purchase I have made this year. The build quality is exceptional and it looks even better in person."},{"name":"Anna R.","stars":"4","review":"Great product overall. Minor issues with setup but customer support was very helpful. Would recommend."}]'>
        @dlItems('__SLUG__', 'reviews', $reviews, '[{"name":"Sarah M.","stars":"5","review":"Absolutely love this product! Exceeded all my expectations. Quality is top-notch and delivery was fast."},{"name":"James K.","stars":"5","review":"Best purchase I have made this year. The build quality is exceptional and it looks even better in person."},{"name":"Anna R.","stars":"4","review":"Great product overall. Minor issues with setup but customer support was very helpful. Would recommend."}]')
        @foreach ($reviews as $review)
            <x-dl.card slug="__SLUG__" prefix="review_card"
                default-classes="rounded-card border border-zinc-200 dark:border-zinc-700 p-6">
                <x-dl.wrapper slug="__SLUG__" prefix="review_stars" tag="div"
                    default-classes="text-yellow-400 text-base mb-3">
                    {{ str_repeat('★', (int) ($review['stars'] ?? 5)) }}{{ str_repeat('☆', 5 - (int) ($review['stars'] ?? 5)) }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="review_text" tag="p"
                    default-classes="text-zinc-600 dark:text-zinc-300 text-sm leading-relaxed mb-4">
                    "{{ $review['review'] }}"
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="review_author" tag="span"
                    default-classes="text-sm font-semibold text-zinc-900 dark:text-white">
                    {{ $review['name'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
