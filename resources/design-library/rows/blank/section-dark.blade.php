{{--
@name Blank - Dark Section
@description Empty dark section with optional heading and subheadline.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900"
    default-container-classes="max-w-container mx-auto text-center">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Section Heading"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Optional supporting text for this section."
        default-classes="mt-4 text-lg text-zinc-400" />
</x-dl.section>
