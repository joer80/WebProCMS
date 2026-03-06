{{--
@name 404 - Dark
@description Dark-themed 404 with bold centered layout on a deep background.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="min-h-screen bg-zinc-900 flex items-center justify-center px-6"
    default-container-classes="text-center">
    <x-dl.wrapper slug="__SLUG__" prefix="error_code"
        default-classes="text-[10rem] leading-none font-black text-white/5 select-none">
        404
    </x-dl.wrapper>
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Lost in the Dark"
        default-tag="h1"
        default-classes="font-heading mt-4 text-4xl font-bold text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="The page you're looking for has vanished into the void."
        default-classes="mt-4 text-zinc-400 max-w-sm mx-auto" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Take Me Home"
        default-primary-classes="px-6 py-3 bg-white text-zinc-900 font-semibold rounded-lg hover:bg-zinc-100 transition-colors"
        default-secondary-label="Report Issue"
        default-secondary-classes="px-6 py-3 border border-zinc-600 text-zinc-300 font-semibold rounded-lg hover:bg-zinc-800 transition-colors" />
</x-dl.section>
