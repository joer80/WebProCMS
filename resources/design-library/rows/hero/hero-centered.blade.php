{{--
@name Hero - Centered
@description Full-width centered hero with headline, subheadline, and dual CTA buttons.
@sort 10
--}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900 text-center"
    default-container-classes="max-w-3xl mx-auto">
        <x-dl-subheadline slug="__SLUG__" prefix="badge" tag="span" default="Welcome"
            default-classes="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6" />
        <x-dl-heading slug="__SLUG__" prefix="headline" default="Your Headline Goes Here"
            default-tag="h1"
            default-classes="font-heading text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight" />
        <x-dl-subheadline slug="__SLUG__" prefix="subheadline" default="A compelling subheadline that explains what you do and why it matters to your audience."
            default-classes="mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed" />
        <x-dl-buttons slug="__SLUG__"
            default-wrapper-classes="mt-10 flex flex-wrap items-center justify-center gap-4"
            default-primary-label="Get Started"
            default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="Learn More"
            default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
</x-dl-section>
