{{--
@name Header - With CTA
@description Sticky header with logo, nav links, and two CTA buttons.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    x-data="{ mobileOpen: false }"
    default-section-classes="sticky top-0 z-50 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
    <x-dl.logo slug="__SLUG__" prefix="logo"
        default-classes="h-8 w-auto" />
    <x-dl.nav slug="__SLUG__" prefix="main_nav"
        default-menu="main-navigation"
        default-classes="hidden md:flex items-center gap-8"
        default-item-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors" />
    <x-dl.wrapper slug="__SLUG__" prefix="header_button"
        default-classes="flex items-center gap-3">
        <x-dl.link slug="__SLUG__" prefix="secondary_cta"
            label-toggle="Show Login"
            label-text="Login Text"
            label-url="Login Link"
            default-label="Log In"
            default-url="#"
            default-classes="hidden md:inline-flex px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-200 hover:text-zinc-900 dark:hover:text-white transition-colors" />
        <x-dl.link slug="__SLUG__" prefix="primary_cta"
            label-toggle="Show Button"
            label-text="Button Text"
            label-url="Button Link"
            default-label="Start Free Trial"
            default-url="#"
            default-classes="hidden md:inline-flex px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
        <x-dl.group slug="__SLUG__" prefix="mobile_btn" tag="button"
            default-classes="md:hidden p-1 rounded text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
            @click="mobileOpen = !mobileOpen"
            aria-label="Toggle menu">
            <svg x-show="!mobileOpen" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="mobileOpen" style="display:none;" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </x-dl.group>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="mobile_panel"
        x-show="mobileOpen"
        x-transition
        style="display:none;"
        default-classes="absolute top-full left-0 right-0 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-lg px-6 py-4 flex flex-col">
        <x-dl.nav slug="__SLUG__" prefix="mobile_nav"
            default-menu="main-navigation"
            default-classes="flex flex-col"
            default-item-classes="block py-3 text-base font-medium text-zinc-700 dark:text-zinc-200 hover:text-zinc-900 dark:hover:text-white border-b border-zinc-100 dark:border-zinc-800 last:border-0 transition-colors" />
        <x-dl.wrapper slug="__SLUG__" prefix="mobile_actions"
            default-classes="mt-4 flex flex-col gap-2">
            <x-dl.link slug="__SLUG__" prefix="mobile_login"
            label-toggle="Show Login"
            label-text="Login Text"
            label-url="Login Link"
                default-label="Log In"
                default-url="#"
                default-classes="inline-flex justify-center px-4 py-2.5 border border-zinc-300 dark:border-zinc-600 text-sm font-semibold text-zinc-700 dark:text-zinc-200 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
            <x-dl.link slug="__SLUG__" prefix="mobile_cta"
            label-toggle="Show Button"
            label-text="Button Text"
            label-url="Button Link"
                default-label="Start Free Trial"
                default-url="#"
                default-classes="inline-flex justify-center px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
