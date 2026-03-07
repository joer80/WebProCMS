{{--
@name Header - Logo Left
@description Left-aligned logo with right-side navigation, no backdrop blur.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="sticky top-0 z-50 bg-white dark:bg-zinc-900 shadow-sm"
    default-container-classes="max-w-7xl mx-auto px-8 h-16 flex items-center justify-between">
    <x-dl.logo slug="__SLUG__" prefix="logo"
        default-classes="h-8 w-auto" />
    <x-dl.nav slug="__SLUG__" prefix="main_nav"
        default-menu="main-navigation"
        default-classes="hidden md:flex items-center gap-8"
        default-item-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-primary transition-colors" />
    <x-dl.link slug="__SLUG__" prefix="primary_cta"
        default-label="Contact Us"
        default-url="/contact"
        default-classes="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
</x-dl.section>
