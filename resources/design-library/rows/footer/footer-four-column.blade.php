{{--
@name Footer - Four Column
@description Dark footer with logo and tagline alongside four navigation link columns and a bottom bar.
@sort 110
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="bg-zinc-900 pt-16 pb-8 px-6"
    default-container-classes="max-w-container mx-auto grid grid-cols-2 md:grid-cols-5 gap-10">
    <x-dl.wrapper slug="__SLUG__" prefix="brand_col"
        default-classes="col-span-2 md:col-span-1">
        <x-dl.logo slug="__SLUG__" prefix="logo"
            default-classes="h-8 w-auto mb-3" />
        <x-dl.subheadline slug="__SLUG__" prefix="tagline" tag="p" default="Sick today, seen today. Walk-in or book online."
            default-classes="text-sm text-zinc-400 leading-relaxed" />
    </x-dl.wrapper>
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="col1_heading" tag="h4" default="Patient Care"
            default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-4" />
        <x-dl.nav slug="__SLUG__" prefix="col1_nav"
            default-menu="main-navigation"
            default-accordion="1"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
    </div>
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="col2_heading" tag="h4" default="Resources"
            default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-4" />
        <x-dl.nav slug="__SLUG__" prefix="col2_nav"
            default-menu="main-navigation"
            default-accordion="1"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
    </div>
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="col3_heading" tag="h4" default="Company"
            default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-4" />
        <x-dl.nav slug="__SLUG__" prefix="col3_nav"
            default-menu="main-navigation"
            default-accordion="1"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
    </div>
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="col4_heading" tag="h4" default="Locations"
            default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-4" />
        <x-dl.nav slug="__SLUG__" prefix="col4_nav"
            default-menu="main-navigation"
            default-accordion="1"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
    </div>
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="col-span-full border-t border-zinc-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
        <x-dl.subheadline slug="__SLUG__" prefix="copyright" tag="span" :default="'© '.date('Y').' All rights reserved.'"
            default-classes="" />
        <x-dl.nav slug="__SLUG__" prefix="legal_nav"
            default-menu="legal"
            default-accordion="1"
            default-classes="flex flex-wrap gap-4"
            default-item-classes="text-zinc-500 hover:text-white transition-colors" />
        <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="span"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-zinc-300 transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
