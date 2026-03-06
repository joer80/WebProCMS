{{--
@name Social Proof - Featured Quote
@description Large centered pull quote from a notable customer or publication.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-3xl mx-auto text-center">
    <x-dl.wrapper slug="__SLUG__" prefix="quote_mark"
        default-classes="text-8xl leading-none text-primary/20 font-serif select-none mb-0">
        "
    </x-dl.wrapper>
    <x-dl.subheadline slug="__SLUG__" prefix="quote" tag="blockquote" default="This is hands-down the best platform we've ever used. It transformed how our entire team operates and cut our project turnaround time in half."
        default-classes="text-2xl text-zinc-700 dark:text-zinc-200 italic font-medium leading-relaxed" />
    <x-dl.wrapper slug="__SLUG__" prefix="stars"
        default-classes="mt-8 flex justify-center text-primary text-2xl">
        ★★★★★
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="author_name"
        default-classes="mt-4 font-semibold text-zinc-900 dark:text-white">
        Sarah Johnson
    </x-dl.wrapper>
    <x-dl.subheadline slug="__SLUG__" prefix="author_role" default="CEO at Acme Corporation"
        default-classes="text-sm text-zinc-500 dark:text-zinc-400 mt-1" />
</x-dl.section>
