{{--
@name Hero - Gradient
@description Centered hero with a vibrant gradient background and CTA buttons.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-hero px-6 bg-gradient-to-br from-primary via-primary/80 to-primary/60 text-center"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.subheadline slug="__SLUG__" prefix="badge" tag="span" default="Now Available"
        default-classes="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-white/20 text-white rounded-full mb-6" />
    <x-dl.heading slug="__SLUG__" prefix="headline" default="The Future of Work Starts Here"
        default-tag="h1"
        default-classes="font-heading text-5xl sm:text-6xl font-bold text-white leading-tight" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Everything your team needs to move faster, collaborate better, and build something remarkable."
        default-classes="mt-6 text-xl text-white/80 leading-relaxed" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-10 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Get Started Free"
        default-primary-classes="px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors"
        default-secondary-label="Watch Demo"
        default-secondary-classes="px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors" />
</x-dl.section>
