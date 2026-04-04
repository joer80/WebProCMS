{{--
@name 404 - Split
@description Two-column 404 with oversized error code on the left and message on the right.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-white dark:bg-zinc-900 flex items-center px-6"
    default-container-classes="max-w-5xl mx-auto grid md:grid-cols-2 gap-16 items-center w-full py-20">
    <div>
        <x-dl.wrapper slug="__SLUG__" prefix="error_code"
            default-classes="text-[12rem] leading-none font-black text-zinc-100 dark:text-zinc-800 select-none -ml-4">
            404
        </x-dl.wrapper>
    </div>
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Page Not Found"
            default-tag="h1"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="The page you're looking for doesn't exist. It may have been moved or deleted."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-8 flex flex-wrap gap-4"
            default-primary-label="Go Home"
            default-primary-classes="btn-primary"
            default-secondary-label="Contact Support"
            default-secondary-classes="btn-secondary" />
    </div>
</x-dl.section>
