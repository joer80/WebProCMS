{{--
@name Page Title - Banner
@description Page banner with an H1 heading. Defaults to the page name set in Page Settings. Supports a background image via the design panel.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-banner px-6 bg-zinc-800 bg-cover bg-center"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="{{ $pageName ?? 'Page Title' }}"
        default-tag="h1"
        default-classes="font-heading text-4xl sm:text-5xl font-bold text-white" />
</x-dl.section>
