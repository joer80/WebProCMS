{{--
@name 404 - With Search
@description 404 page with a search input below the error message.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center px-6"
    default-container-classes="text-center max-w-lg mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="error_code"
        default-classes="text-8xl font-black text-zinc-200 dark:text-zinc-800">
        404
    </x-dl.wrapper>
    <x-dl.heading slug="__SLUG__" prefix="headline" default="We Lost That Page"
        default-tag="h1"
        default-classes="font-heading mt-2 text-3xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Try searching for what you need, or head back to the homepage."
        default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
    <x-dl.wrapper slug="__SLUG__" prefix="search_form" tag="form"
        default-classes="mt-8 flex gap-2 max-w-sm mx-auto">
        <x-dl.wrapper slug="__SLUG__" prefix="search_input" tag="input"
            type="search"
            placeholder="Search..."
            default-classes="flex-1 px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition" />
        <x-dl.wrapper slug="__SLUG__" prefix="search_button" tag="button"
            type="submit"
            default-classes="px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            Search
        </x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.link slug="__SLUG__" prefix="home_link"
        default-label="← Back to Home"
        default-url="/"
        default-classes="mt-6 inline-block text-sm text-primary hover:text-primary/80 transition-colors" />
</x-dl.section>
