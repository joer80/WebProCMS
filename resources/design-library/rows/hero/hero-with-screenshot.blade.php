{{--
@name Hero - With Screenshot
@description Centered hero with headline and subheadline above a large product screenshot.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-hero px-6 bg-white dark:bg-zinc-900 text-center"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Your Product Does This"
        default-tag="h1"
        default-classes="font-heading text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Everything you need to build and launch faster. Trusted by thousands of teams worldwide."
        default-classes="mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-2xl mx-auto" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-10 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Get Started Free"
        default-primary-classes="btn-primary !px-8"
        default-secondary-label="See How It Works →"
        default-secondary-classes="btn-ghost !px-8" />
    <x-dl.media slug="__SLUG__"
        default-wrapper-classes="mt-16 rounded-card overflow-hidden shadow-card ring-1 ring-zinc-200 dark:ring-zinc-700"
        default-image-classes="w-full h-auto"
        default-image="https://placehold.co/1400x800" />
</x-dl.section>
