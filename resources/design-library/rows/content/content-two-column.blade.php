{{--
@name Two Column Text & Image
@description Two-column content section with text and image placeholder.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            <x-dl.subheadline slug="__SLUG__" prefix="badge" tag="span" default="Our Story"
                default-classes="text-sm font-semibold text-primary uppercase tracking-wider" />
            <x-dl.heading slug="__SLUG__" prefix="headline" default="We Are Building the Future of Work"
                default-tag="h2"
                default-classes="font-heading mt-3 text-4xl font-bold text-zinc-900 dark:text-white leading-tight" />
            <x-dl.subheadline slug="__SLUG__" prefix="body" tag="p" default="Founded in 2020, we have been on a mission to help teams collaborate more effectively. Our platform combines the best of communication, project management, and automation into one seamless experience."
                default-classes="mt-6 text-zinc-500 dark:text-zinc-400 leading-relaxed" />
            <x-dl.subheadline slug="__SLUG__" prefix="body_secondary" tag="p" default="Today, we are trusted by over 10,000 companies worldwide, from startups to Fortune 500 enterprises."
                default-classes="mt-4 text-zinc-500 dark:text-zinc-400 leading-relaxed" />
            <x-dl.link slug="__SLUG__" prefix="cta"
                default-label="Learn more about us →"
                default-url="#"
                default-classes="mt-8 inline-flex items-center text-primary font-semibold hover:text-primary/80 transition-colors" />
        </div>
        <x-dl.media slug="__SLUG__"
            default-wrapper-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square flex items-center justify-center"
            default-image-classes="w-full h-full object-cover"
            default-image="https://placehold.co/800x800" />
</x-dl.section>
