{{--
@name 404 - Card
@description Centered 404 content inside a shadowed card on a soft gray background.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-zinc-100 dark:bg-zinc-950 flex items-center justify-center px-6"
    default-container-classes="w-full max-w-md mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="card"
        default-classes="bg-white dark:bg-zinc-900 rounded-2xl shadow-card p-10 text-center">
        <x-dl.wrapper slug="__SLUG__" prefix="error_code"
            default-classes="text-7xl font-black text-zinc-200 dark:text-zinc-700">
            404
        </x-dl.wrapper>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Page Not Found"
            default-tag="h1"
            default-classes="font-heading mt-4 text-2xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Sorry, we couldn't find the page you're looking for."
            default-classes="mt-3 text-sm text-zinc-500 dark:text-zinc-400" />
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3"
            default-primary-label="Go Home"
            default-primary-classes="w-full sm:w-auto px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="Go Back"
            default-secondary-classes="w-full sm:w-auto px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
    </x-dl.wrapper>
</x-dl.section>
