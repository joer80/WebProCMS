{{--
@name Social Proof - Review Wall
@description Masonry-style wall of short customer reviews.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="What People Are Saying"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="reviews"
        default-grid-classes="columns-1 md:columns-3 gap-4 space-y-4"
        default-items='[{"quote":"Amazing product! Changed the way our team works.","name":"@alex_t","platform":"Twitter"},{"quote":"Seriously impressed with how fast support responds. 5 stars.","name":"Taylor M.","platform":"G2"},{"quote":"Finally a tool that does exactly what it promises. No bloat, no confusion.","name":"@dev_jamie","platform":"Twitter"},{"quote":"We saw a 40% improvement in productivity in the first month.","name":"Sam R.","platform":"Capterra"},{"quote":"Best investment we made this year. ROI was almost immediate.","name":"Casey L.","platform":"G2"},{"quote":"Incredible onboarding. We were up and running in under an hour.","name":"@morgan_builds","platform":"Twitter"}]'>
        @dlItems('__SLUG__', 'reviews', $reviews, '[{"quote":"Amazing product! Changed the way our team works.","name":"@alex_t","platform":"Twitter"},{"quote":"Seriously impressed with how fast support responds. 5 stars.","name":"Taylor M.","platform":"G2"},{"quote":"Finally a tool that does exactly what it promises. No bloat, no confusion.","name":"@dev_jamie","platform":"Twitter"},{"quote":"We saw a 40% improvement in productivity in the first month.","name":"Sam R.","platform":"Capterra"},{"quote":"Best investment we made this year. ROI was almost immediate.","name":"Casey L.","platform":"G2"},{"quote":"Incredible onboarding. We were up and running in under an hour.","name":"@morgan_builds","platform":"Twitter"}]')
        @foreach ($reviews as $review)
            <x-dl.card slug="__SLUG__" prefix="review_card"
                default-classes="break-inside-avoid p-5 rounded-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 inline-block w-full mb-4">
                <x-dl.wrapper slug="__SLUG__" prefix="review_quote" tag="p"
                    default-classes="text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed italic">
                    "{{ $review['quote'] }}"
                </x-dl.wrapper>
                <x-dl.group slug="__SLUG__" prefix="review_footer"
                    default-classes="mt-4 flex items-center justify-between">
                    <x-dl.wrapper slug="__SLUG__" prefix="review_name" tag="span"
                        default-classes="text-xs font-semibold text-zinc-900 dark:text-white">
                        {{ $review['name'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="review_platform" tag="span"
                        default-classes="text-xs px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400">
                        {{ $review['platform'] }}
                    </x-dl.wrapper>
                </x-dl.group>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
