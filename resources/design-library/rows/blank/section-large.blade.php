{{--
@name Blank - Large Padding
@description Empty section with generous padding, ideal for visual breathing room.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-32 px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto text-center">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Section Heading"
        default-tag="h2"
        default-classes="font-heading text-5xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Optional supporting text for this section."
        default-classes="mt-6 text-xl text-zinc-500 dark:text-zinc-400" />
</x-dl.section>
