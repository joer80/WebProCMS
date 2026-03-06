{{--
@name Blank - Wide
@description Empty section with a wide full-width layout.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-screen-2xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Section Heading"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="A wider container for full-width content layouts."
        default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
</x-dl.section>
