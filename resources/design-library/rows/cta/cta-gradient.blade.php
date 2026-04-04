{{--
@name CTA - Gradient
@description Full-width gradient call-to-action with headline and CTA buttons.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-banner px-6 bg-gradient-to-r from-primary to-primary/70 text-center"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Start Building Today"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="No credit card required. Free 14-day trial. Cancel anytime."
        default-classes="mt-4 text-lg text-white/80" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Get Started Free"
        default-primary-classes="btn-inverted"
        default-secondary-label="Schedule a Demo"
        default-secondary-classes="btn-outline-white" />
</x-dl.section>
