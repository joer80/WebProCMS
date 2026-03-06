{{--
@name Blank - Narrow
@description Empty section with a narrow centered container.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-2xl mx-auto text-center">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Section Heading"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="A narrower container for focused, single-column content."
        default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
</x-dl.section>
