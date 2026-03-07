{{--
@name Footer - Simple
@description Clean footer with logo, nav links, and copyright.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="bg-zinc-900 text-zinc-400 py-12 px-6"
    default-container-classes="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
    <div>
        <x-dl.logo slug="__SLUG__" prefix="logo"
            default-classes="h-8 w-auto" />
        <x-dl.subheadline slug="__SLUG__" prefix="tagline" tag="p" default="Helping you build better things."
            default-classes="mt-2 text-sm" />
    </div>
    <x-dl.nav slug="__SLUG__" prefix="main_nav"
        default-menu="main-navigation"
        default-classes="flex flex-wrap gap-6 text-sm"
        default-item-classes="hover:text-white transition-colors" />
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="text-sm text-right">
        <x-dl.subheadline slug="__SLUG__" prefix="copyright" tag="p" :default="'© '.date('Y').' All rights reserved.'"
            default-classes="" />
        <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="p"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-white transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
