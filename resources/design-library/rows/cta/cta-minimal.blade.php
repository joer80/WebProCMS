{{--
@name CTA - Minimal
@description Clean, minimal call-to-action with centered text and a single prominent button.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900 text-center border-t border-zinc-200 dark:border-zinc-700"
    default-container-classes="max-w-2xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Ready to Get Started?"
        default-tag="h2"
        default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Join over 10,000 happy customers using our platform."
        default-classes="mt-4 text-base text-zinc-500 dark:text-zinc-400" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-6 flex justify-center"
        default-primary-label="Start Now — It's Free"
        default-primary-classes="px-10 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
        default-secondary-label=""
        default-secondary-classes="" />
</x-dl.section>
