{{--
@name Page Title - Banner Dark
@description Dark page banner with an H1 heading and semi-transparent overlay. Defaults to the page name set in Page Settings. Supports a background image.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="relative py-section-banner px-6 bg-zinc-800 bg-cover bg-center"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="overlay" tag="div"
        default-toggle="1"
        default-classes="absolute inset-0 bg-black/50" />
    <x-dl.heading slug="__SLUG__" prefix="headline" default="{{ $pageName ?? 'Page Title' }}"
        default-tag="h1"
        default-classes="relative z-10 font-heading text-4xl sm:text-5xl font-bold text-white" />
</x-dl.section>
