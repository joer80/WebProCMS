{{--
@name Footer - Columns
@description Multi-column footer with logo, link groups, and newsletter signup.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="bg-zinc-900 pt-16 pb-8 px-6"
    default-container-classes="max-w-container mx-auto grid grid-cols-2 md:grid-cols-4 gap-10">
    <x-dl.wrapper slug="__SLUG__" prefix="brand_col"
        default-classes="col-span-2 md:col-span-1">
        <x-dl.logo slug="__SLUG__" prefix="logo"
            default-classes="h-8 w-auto" />
        <x-dl.subheadline slug="__SLUG__" prefix="description" tag="p" default="A short description of your company and what makes you unique."
            default-classes="mt-3 text-sm text-zinc-400 leading-relaxed" />
    </x-dl.wrapper>
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="col1_heading" tag="h4" default="Company"
            default-classes="text-sm font-semibold text-white uppercase tracking-wider mb-4" />
        <x-dl.nav slug="__SLUG__" prefix="col1_nav"
            default-menu="main-navigation"
            default-accordion="1"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
    </div>
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="col2_heading" tag="h4" default="Product"
            default-classes="text-sm font-semibold text-white uppercase tracking-wider mb-4" />
        <x-dl.nav slug="__SLUG__" prefix="col2_nav"
            default-menu="main-navigation"
            default-accordion="1"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
    </div>
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="col3_heading" tag="h4" default="Legal"
            default-classes="text-sm font-semibold text-white uppercase tracking-wider mb-4" />
        <x-dl.nav slug="__SLUG__" prefix="col3_nav"
            default-menu="legal"
            default-accordion="1"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
    </div>
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="col-span-full border-t border-zinc-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-zinc-500">
        <x-dl.subheadline slug="__SLUG__" prefix="copyright" tag="span" :default="'© '.date('Y').' All rights reserved.'"
            default-classes="" />
        <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="span"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-white transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
