{{--
@name Hero - Reversed
@description Two-column hero with image on the left and text on the right.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-hero px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto grid md:grid-cols-2 gap-12 items-center">
    <x-dl.media slug="__SLUG__"
        default-wrapper-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center"
        default-image-classes="w-full h-full object-cover"
        default-image="https://placehold.co/1200x675" />
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Built For Your Team"
            default-tag="h1"
            default-classes="font-heading text-5xl font-bold text-zinc-900 dark:text-white leading-tight" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Powerful tools that fit the way you work. Collaborate, ship, and grow together."
            default-classes="mt-6 text-lg text-zinc-500 dark:text-zinc-400" />
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-8 flex flex-wrap gap-4"
            default-primary-label="Get Started"
            default-primary-classes="btn-primary"
            default-secondary-label="Learn More →"
            default-secondary-classes="btn-ghost" />
    </div>
</x-dl.section>
