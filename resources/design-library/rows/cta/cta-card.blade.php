{{--
@name CTA - Card
@description Centered card-style call-to-action with rounded corners on a neutral background.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto bg-white dark:bg-zinc-800 rounded-card shadow-card px-10 py-12 text-center">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Start Your Free Trial Today"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="No setup fees. Cancel anytime."
        default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Get Started"
        default-primary-classes="btn-primary !px-8"
        default-secondary-label="Learn More"
        default-secondary-classes="btn-secondary !px-8" />
</x-dl.section>
