{{--
@name Page Title - Banner Light
@description Light page banner with an H1 heading. Defaults to the page name set in Page Settings. Overlay is off by default.
@sort 11
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="relative py-section-banner px-6 bg-white bg-cover bg-center"
    default-container-classes="max-w-container mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="overlay" tag="div"
        default-toggle="0"
        default-classes="absolute inset-0 bg-white/80" />
    <x-dl.heading slug="__SLUG__" prefix="headline" default="{{ $pageName ?: 'Page Title' }}"
        default-tag="h1"
        default-classes="relative z-10 font-heading text-4xl sm:text-5xl font-bold text-[#252525]" />
</x-dl.section>
