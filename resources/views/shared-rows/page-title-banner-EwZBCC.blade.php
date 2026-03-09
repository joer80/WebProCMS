<x-dl.section slug="page-title-banner:EwZBCC"
    default-section-classes="relative py-section-banner px-6 bg-zinc-800 bg-cover bg-center"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="page-title-banner:EwZBCC" prefix="overlay" tag="div"
        default-toggle="1"
        default-classes="absolute inset-0 bg-[#6b6b6b]/90" />
    <x-dl.heading slug="page-title-banner:EwZBCC" prefix="headline" default="{{ $pageName ?? 'Page Title' }}"
        default-tag="h1"
        default-classes="relative z-10 font-heading text-4xl sm:text-5xl font-bold text-[#252525]" />
</x-dl.section>
