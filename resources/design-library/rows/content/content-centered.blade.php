{{--
@name Content - Centered
@description Centered content block with heading, description, and optional CTA.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900 text-center"
    default-container-classes="max-w-2xl mx-auto">
    <x-dl.subheadline slug="__SLUG__" prefix="eyebrow" tag="span" default="Our Mission"
        default-classes="text-sm font-semibold text-primary uppercase tracking-wider" />
    <x-dl.heading slug="__SLUG__" prefix="headline" default="We're on a Mission to Change the Way the World Works"
        default-tag="h2"
        default-classes="font-heading mt-3 text-4xl font-bold text-zinc-900 dark:text-white leading-tight" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Great software should be accessible to everyone. We believe in building tools that empower teams of any size to do their best work."
        default-classes="mt-6 text-lg text-zinc-500 dark:text-zinc-400 leading-relaxed" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Learn More"
        default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
        default-secondary-label="Meet the Team"
        default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
</x-dl.section>
