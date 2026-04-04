{{--
@name 404 - Minimal
@description Ultra-minimal 404 with small typography and a subtle label instead of a large error number.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center px-6"
    default-container-classes="max-w-xs mx-auto text-center">
    <x-dl.wrapper slug="__SLUG__" prefix="error_label"
        default-classes="text-sm font-semibold text-primary uppercase tracking-widest">
        Error 404
    </x-dl.wrapper>
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Page not found"
        default-tag="h1"
        default-classes="font-heading mt-3 text-2xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Sorry, we couldn't find the page you're looking for."
        default-classes="mt-3 text-sm text-zinc-500 dark:text-zinc-400" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-6 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Go Home"
        default-primary-classes="btn-primary !px-5 !py-2.5 !text-sm"
        default-secondary-label="Contact Us"
        default-secondary-classes="text-sm text-zinc-500 dark:text-zinc-400 font-medium hover:text-zinc-900 dark:hover:text-white transition-colors" />
</x-dl.section>
