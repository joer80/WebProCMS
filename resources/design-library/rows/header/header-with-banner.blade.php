{{--
@name Header - With Banner
@description Announcement banner above a standard sticky header.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="sticky top-0 z-50"
    default-container-classes="">
    <x-dl.wrapper slug="__SLUG__" prefix="announcement_bar"
        default-classes="bg-primary py-2 px-6 text-center text-sm text-white font-medium">
        <x-dl.subheadline slug="__SLUG__" prefix="announcement" tag="span" default="New feature launched! Read the announcement →"
            default-classes="inline" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="nav_bar"
        default-classes="bg-white/95 dark:bg-zinc-900/95 backdrop-blur border-b border-zinc-200 dark:border-zinc-800">
        <x-dl.wrapper slug="__SLUG__" prefix="nav_container"
            default-classes="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <x-dl.logo slug="__SLUG__" prefix="logo"
                default-classes="h-8 w-auto" />
            <x-dl.nav slug="__SLUG__" prefix="main_nav"
                default-menu="main-navigation"
                default-classes="hidden md:flex items-center gap-8"
                default-item-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors" />
            <x-dl.link slug="__SLUG__" prefix="primary_cta"
                default-label="Get Started"
                default-url="#"
                default-classes="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
