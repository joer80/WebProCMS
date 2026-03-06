{{--
@name CTA - With Image
@description Two-column call-to-action with text on the left and decorative image on the right.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="eyebrow" tag="span" default="Ready to grow?"
            default-classes="text-sm font-semibold text-primary uppercase tracking-wider" />
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Take Your Business to the Next Level"
            default-tag="h2"
            default-classes="font-heading mt-3 text-4xl font-bold text-zinc-900 dark:text-white leading-tight" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Powerful tools, simple pricing, and world-class support. Everything you need to succeed."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-8 flex flex-wrap items-center gap-4"
            default-primary-label="Start Free Trial"
            default-primary-classes="px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="Talk to Sales"
            default-secondary-classes="px-8 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors" />
    </div>
    <x-dl.media slug="__SLUG__"
        default-wrapper-classes="rounded-card overflow-hidden aspect-video bg-zinc-100 dark:bg-zinc-800"
        default-image-classes="w-full h-full object-cover"
        default-image="https://placehold.co/1200x675" />
</x-dl.section>
