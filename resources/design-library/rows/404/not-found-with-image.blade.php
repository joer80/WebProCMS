{{--
@name 404 - With Image
@description 404 page with an illustration image above the error message.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center px-6"
    default-container-classes="text-center max-w-lg mx-auto">
    <x-dl.media slug="__SLUG__"
        default-wrapper-classes="mx-auto mb-10 max-w-xs"
        default-image-classes="w-full h-auto"
        default-image="https://placehold.co/400x300" />
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Oops! Page Not Found"
        default-tag="h1"
        default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="The page you're looking for doesn't exist or has been moved to a new location."
        default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Back to Home"
        default-primary-classes="btn-primary"
        default-secondary-label="Browse Sitemap"
        default-secondary-classes="btn-secondary" />
</x-dl.section>
