<?php /** @layout-partial header */ ?>
<div class="contents">
{{-- ROW:start:header-simple:header --}}
<x-dl.section slug="header-simple:header"
    tag="header"
    x-data="{ mobileOpen: false, scrolled: false }"
    @scroll.window="scrolled = window.scrollY > 20"
    x-bind:class="scrolled ? 'h-16' : 'h-20'"
    default-sticky="1"
    default-section-classes="z-50 bg-white/80 dark:bg-zinc-900/80 backdrop-blur border-b border-zinc-200 dark:border-zinc-800 transition-all duration-300"
    default-container-classes="max-w-6xl mx-auto px-6 h-full flex items-center justify-between">
    <x-dl.logo slug="header-simple:header" prefix="logo"
        default-classes="h-8 w-auto" />
    <x-dl.nav slug="header-simple:header" prefix="main_nav"
        default-menu="main-navigation"
        default-classes="hidden md:flex items-center gap-8"
        default-item-classes="text-sm uppercase tracking-wide font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors" />
    <x-dl.wrapper slug="header-simple:header" prefix="header_button"
        default-classes="flex items-center gap-3">
        <x-dl.link slug="header-simple:header" prefix="primary_cta"
            label-toggle="Show Button"
            label-text="Button Text"
            label-url="Button Link"
            default-label="Get Started"
            default-url="#"
            default-classes="hidden md:inline-flex px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
        <x-dl.group slug="header-simple:header" prefix="mobile_btn" tag="button"
            default-classes="md:hidden p-1 rounded text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
            @click="mobileOpen = !mobileOpen"
            aria-label="Toggle menu">
            <svg x-show="!mobileOpen" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="mobileOpen" style="display:none;" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </x-dl.group>
    </x-dl.wrapper>
    <x-dl.wrapper slug="header-simple:header" prefix="mobile_panel"
        x-show="mobileOpen"
        x-transition
        style="display:none;"
        default-classes="absolute top-full left-0 right-0 border-t border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur shadow-lg px-6 py-4 flex flex-col">
        <x-dl.nav slug="header-simple:header" prefix="mobile_nav"
            default-menu="main-navigation"
            default-classes="flex flex-col"
            default-item-classes="block py-3 text-base font-medium text-zinc-700 dark:text-zinc-200 hover:text-zinc-900 dark:hover:text-white border-b border-zinc-100 dark:border-zinc-800 last:border-0 transition-colors" />
        <x-dl.link slug="header-simple:header" prefix="mobile_cta"
            label-toggle="Show Button"
            label-text="Button Text"
            label-url="Button Link"
            default-label="Get Started"
            default-url="#"
            default-classes="mt-4 inline-flex px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
    </x-dl.wrapper>
</x-dl.section>
{{-- ROW:end:header-simple:header --}}
</div>
