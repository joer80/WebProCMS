{{--
@name Footer - Dark Minimal
@description Minimal dark footer with a single nav row and powered by.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="py-8 px-6 bg-zinc-950"
    default-container-classes="max-w-container mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
    <x-dl.logo slug="__SLUG__" prefix="logo"
        default-classes="h-8 w-auto" />
    <x-dl.nav slug="__SLUG__" prefix="main_nav"
        default-menu="main-navigation"
        default-accordion="1"
        default-classes="flex flex-wrap items-center justify-center gap-6"
        default-item-classes="text-sm text-zinc-400 hover:text-white transition-colors" />
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="flex flex-col sm:flex-row items-center gap-3 text-xs text-zinc-600">
        <x-dl.subheadline slug="__SLUG__" prefix="copyright" tag="span" :default="'© '.date('Y').' All rights reserved.'"
            default-classes="" />
        <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="span"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-zinc-400 transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
