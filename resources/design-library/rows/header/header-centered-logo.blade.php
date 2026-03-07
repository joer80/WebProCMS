{{--
@name Header - Centered Logo
@description Header with logo centered and navigation links on both sides.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="sticky top-0 z-50 bg-white/90 dark:bg-zinc-900/90 backdrop-blur border-b border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-6xl mx-auto px-6 h-16 grid grid-cols-3 items-center">
    <x-dl.nav slug="__SLUG__" prefix="left_nav"
        default-menu="main-navigation"
        default-classes="hidden md:flex items-center gap-6"
        default-item-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors" />
    <x-dl.logo slug="__SLUG__" prefix="logo"
        default-classes="h-8 w-auto" />
    <div class="flex items-center justify-end gap-6">
        <x-dl.nav slug="__SLUG__" prefix="right_nav"
            default-menu="main-navigation"
            default-classes="hidden md:flex items-center gap-6"
            default-item-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors" />
        <x-dl.link slug="__SLUG__" prefix="primary_cta"
            default-label="Get Started"
            default-url="#"
            default-classes="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
    </div>
</x-dl.section>
