{{--
@name Header - Dark
@description Dark background sticky header with logo and nav.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="sticky top-0 z-50 bg-zinc-900 border-b border-zinc-800"
    default-container-classes="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
    <x-dl.logo slug="__SLUG__" prefix="logo"
        default-classes="h-8 w-auto" />
    <x-dl.nav slug="__SLUG__" prefix="main_nav"
        default-menu="main-navigation"
        default-classes="hidden md:flex items-center gap-8"
        default-item-classes="text-sm text-zinc-300 hover:text-white transition-colors" />
    <x-dl.link slug="__SLUG__" prefix="primary_cta"
        default-label="Get Started"
        default-url="#"
        default-classes="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
</x-dl.section>
