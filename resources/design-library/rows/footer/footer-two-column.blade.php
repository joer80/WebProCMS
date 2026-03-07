{{--
@name Footer - Two Column
@description Two-column footer: brand left, links and copyright right.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="py-12 px-6 bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-10 items-start">
    <x-dl.wrapper slug="__SLUG__" prefix="brand_col"
        default-classes="">
        <x-dl.logo slug="__SLUG__" prefix="logo"
            default-classes="h-8 w-auto mb-3" />
        <x-dl.subheadline slug="__SLUG__" prefix="brand_desc" tag="p" default="Building great products for great people."
            default-classes="text-sm text-zinc-400 leading-relaxed max-w-xs" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="links_grid"
        default-classes="grid grid-cols-2 gap-8">
        <div>
            <x-dl.subheadline slug="__SLUG__" prefix="col1_heading" tag="h4" default="Product"
                default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-3" />
            <x-dl.nav slug="__SLUG__" prefix="col1_nav"
                default-menu="main-navigation"
                default-classes="space-y-2"
                default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
        </div>
        <div>
            <x-dl.subheadline slug="__SLUG__" prefix="col2_heading" tag="h4" default="Company"
                default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-3" />
            <x-dl.nav slug="__SLUG__" prefix="col2_nav"
                default-menu="main-navigation"
                default-classes="space-y-2"
                default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
        </div>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="col-span-full pt-8 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
        <x-dl.subheadline slug="__SLUG__" prefix="copyright" tag="span" :default="'© '.date('Y').' All rights reserved.'"
            default-classes="" />
        <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="span"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-zinc-300 transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
