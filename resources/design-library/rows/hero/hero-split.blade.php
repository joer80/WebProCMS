{{--
@name Hero - Split
@description Two-column hero with text on the left and image placeholder on the right.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Build Something Amazing"
                default-tag="h1"
                default-classes="font-heading text-5xl font-bold text-zinc-900 dark:text-white leading-tight" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Describe your product or service here. Keep it concise and focused on the value you deliver to customers."
                default-classes="mt-6 text-lg text-zinc-500 dark:text-zinc-400" />
            <x-dl.buttons slug="__SLUG__"
                default-wrapper-classes="mt-8 flex flex-wrap gap-4"
                default-primary-label="Start Free Trial"
                default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
                default-secondary-label="Watch Demo →"
                default-secondary-classes="px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors" />
        </div>
        <x-dl.media slug="__SLUG__"
            default-wrapper-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center"
            default-image-classes="w-full h-full object-cover" />
</x-dl.section>
