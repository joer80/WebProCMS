{{--
@name Blog Detail - Next & Previous
@description Navigation links to the next and previous blog posts.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-4xl mx-auto grid sm:grid-cols-2 gap-8">
    <x-dl.wrapper slug="__SLUG__" prefix="prev_post"
        default-classes="group">
        <x-dl.subheadline slug="__SLUG__" prefix="prev_label" tag="span" default="← Previous Post"
            default-classes="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider" />
        <x-dl.link slug="__SLUG__" prefix="prev_link"
            default-label="The Art of Effective Communication in Remote Teams"
            default-url="/blog/previous-post"
            default-classes="mt-2 block font-semibold text-zinc-900 dark:text-white hover:text-primary transition-colors leading-snug" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="next_post"
        default-classes="group text-right">
        <x-dl.subheadline slug="__SLUG__" prefix="next_label" tag="span" default="Next Post →"
            default-classes="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider" />
        <x-dl.link slug="__SLUG__" prefix="next_link"
            default-label="10 Productivity Hacks Every Team Should Know"
            default-url="/blog/next-post"
            default-classes="mt-2 block font-semibold text-zinc-900 dark:text-white hover:text-primary transition-colors leading-snug" />
    </x-dl.wrapper>
</x-dl.section>
