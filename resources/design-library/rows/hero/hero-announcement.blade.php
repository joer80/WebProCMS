{{--
@name Hero - Announcement
@description Centered hero with a clickable announcement banner above the main headline.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-hero px-6 bg-white dark:bg-zinc-900 text-center"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.link slug="__SLUG__" prefix="announcement"
        default-label="🎉 We just launched v2.0 — Read the announcement →"
        default-url="#"
        default-classes="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-primary/10 text-primary rounded-full hover:bg-primary/20 transition-colors mb-8" />
    <x-dl.heading slug="__SLUG__" prefix="headline" default="The Platform Built For Growth"
        default-tag="h1"
        default-classes="font-heading text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Start, scale, and succeed with a platform that grows with you. Join thousands of successful teams."
        default-classes="mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-10 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Start Free"
        default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
        default-secondary-label="Book a Demo"
        default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
</x-dl.section>
