{{--
@name Hero - Dark
@description Dark background centered hero with badge, headline, subheadline, and CTA buttons.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-hero px-6 bg-zinc-900 text-center"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.subheadline slug="__SLUG__" prefix="badge" tag="span" default="New Release"
        default-classes="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/20 text-primary rounded-full mb-6" />
    <x-dl.heading slug="__SLUG__" prefix="headline" default="The Future Is Here"
        default-tag="h1"
        default-classes="font-heading text-5xl sm:text-6xl font-bold text-white leading-tight" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="A powerful platform designed for modern teams who want to move fast without breaking things."
        default-classes="mt-6 text-xl text-zinc-400 leading-relaxed" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-10 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Get Started"
        default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
        default-secondary-label="Learn More"
        default-secondary-classes="px-6 py-3 border border-zinc-700 text-zinc-300 font-semibold rounded-lg hover:bg-zinc-800 transition-colors" />
</x-dl.section>
