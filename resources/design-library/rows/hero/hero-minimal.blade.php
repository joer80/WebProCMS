{{--
@name Hero - Minimal
@description Left-aligned minimal hero with oversized headline and subheadline — clean and typographic.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-hero px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-4xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Simple. Powerful. Yours."
        default-tag="h1"
        default-classes="font-heading text-6xl sm:text-7xl font-bold text-zinc-900 dark:text-white leading-none tracking-tight" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Everything your team needs to do great work. No fluff, no complexity — just results."
        default-classes="mt-8 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-2xl" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-10 flex flex-wrap gap-4"
        default-primary-label="Get Started →"
        default-primary-classes="px-6 py-3 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold rounded-lg hover:bg-zinc-700 dark:hover:bg-zinc-100 transition-colors"
        default-secondary-label="Contact Us"
        default-secondary-classes="btn-ghost underline underline-offset-4" />
</x-dl.section>
