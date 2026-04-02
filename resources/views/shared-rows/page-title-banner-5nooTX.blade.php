<x-dl.section slug="page-title-banner:5nooTX"
    default-section-classes="relative py-section-banner px-6 bg-zinc-800 bg-cover bg-center"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="page-title-banner:5nooTX" prefix="overlay" tag="div"
        default-toggle="1"
        default-classes="absolute inset-0 bg-black/50" />
    <x-dl.heading slug="page-title-banner:5nooTX" prefix="headline" default="{{ $pageName ?: 'Page Title' }}"
        default-tag="h1"
        default-classes="relative z-10 font-heading text-4xl sm:text-5xl font-bold text-white" />
</x-dl.section>