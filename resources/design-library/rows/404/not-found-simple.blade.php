{{--
@name 404 - Simple
@description Clean 404 not found page with large error code and navigation links.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center px-6"
    default-container-classes="text-center">
        <x-dl.wrapper slug="__SLUG__" prefix="error_code"
            default-classes="text-8xl font-black text-zinc-200 dark:text-zinc-700">
            404
        </x-dl.wrapper>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Page Not Found"
            default-tag="h1"
            default-classes="font-heading mt-4 text-3xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Sorry, we couldn't find the page you're looking for. It may have been moved or deleted."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto" />
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
            default-primary-label="Go Home"
            default-primary-classes="btn-primary"
            default-secondary-label="Contact Support"
            default-secondary-classes="btn-secondary" />
</x-dl.section>
