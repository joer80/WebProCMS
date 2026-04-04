{{--
@name 404 - Illustrated
@description Split 404 with an illustration or image on the left and message on the right.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-white dark:bg-zinc-900 flex items-center px-6"
    default-container-classes="max-w-5xl mx-auto grid md:grid-cols-2 gap-12 items-center w-full py-20">
    <x-dl.media slug="__SLUG__"
        default-wrapper-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square flex items-center justify-center"
        default-image-classes="w-full h-full object-cover"
        default-image="https://placehold.co/800x800" />
    <div>
        <x-dl.wrapper slug="__SLUG__" prefix="error_badge"
            default-classes="inline-flex items-center px-3 py-1 rounded-full bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400 text-sm font-semibold mb-6">
            Error 404
        </x-dl.wrapper>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Oops! Page Not Found"
            default-tag="h1"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="The page you're looking for doesn't exist. It may have been moved, renamed, or temporarily unavailable."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-8 flex flex-wrap gap-4"
            default-primary-label="Go Home"
            default-primary-classes="btn-primary"
            default-secondary-label="Contact Support"
            default-secondary-classes="btn-secondary" />
    </div>
</x-dl.section>
