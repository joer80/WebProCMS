{{--
@name CTA - Dark
@description Dark background call-to-action with bold headline and high-contrast buttons.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-banner px-6 bg-zinc-900 dark:bg-zinc-950 text-center"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Take the Next Step"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="No contracts. No credit card required. Cancel anytime."
        default-classes="mt-4 text-lg text-zinc-400" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Start Free Trial"
        default-primary-classes="btn-primary !px-8"
        default-secondary-label="View Demo"
        default-secondary-classes="btn-outline-dark !px-8" />
</x-dl.section>
