{{--
@name Header - Transparent
@description Transparent header designed to overlay a hero image.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="absolute top-0 inset-x-0 z-50"
    default-container-classes="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
    <x-dl.logo slug="__SLUG__" prefix="logo"
        default-classes="h-8 w-auto" />
    <x-dl.nav slug="__SLUG__" prefix="main_nav"
        default-menu="main-navigation"
        default-classes="hidden md:flex items-center gap-8"
        default-item-classes="text-sm text-white/80 hover:text-white transition-colors" />
    <x-dl.link slug="__SLUG__" prefix="primary_cta"
        default-label="Get Started"
        default-url="#"
        default-classes="px-4 py-2 bg-white text-zinc-900 text-sm font-semibold rounded-lg hover:bg-zinc-100 transition-colors" />
</x-dl.section>
