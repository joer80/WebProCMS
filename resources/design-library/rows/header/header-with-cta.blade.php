{{--
@name Header - With CTA
@description Sticky header with logo, nav links, and two CTA buttons.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="sticky top-0 z-50 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
    <x-dl.logo slug="__SLUG__" prefix="logo"
        default-classes="h-8 w-auto" />
    <x-dl.nav slug="__SLUG__" prefix="main_nav"
        default-menu="main-navigation"
        default-classes="hidden md:flex items-center gap-8"
        default-item-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors" />
    <div class="flex items-center gap-3">
        <x-dl.link slug="__SLUG__" prefix="secondary_cta"
            default-label="Log In"
            default-url="#"
            default-classes="px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-200 hover:text-zinc-900 dark:hover:text-white transition-colors" />
        <x-dl.link slug="__SLUG__" prefix="primary_cta"
            default-label="Start Free Trial"
            default-url="#"
            default-classes="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
    </div>
</x-dl.section>
