{{--
@name Footer - Two Column
@description Two-column footer: brand left, links and copyright right.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="py-12 px-6 bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="main_grid"
        default-classes="grid md:grid-cols-2 gap-10 items-start mb-12">
        <x-dl.wrapper slug="__SLUG__" prefix="brand_col"
            default-classes="">
            <x-dl.wrapper slug="__SLUG__" prefix="brand_name" tag="a"
                href="/"
                default-classes="text-xl font-bold text-white block mb-3">
                Your Brand
            </x-dl.wrapper>
            <x-dl.subheadline slug="__SLUG__" prefix="brand_desc" tag="p" default="Building great products for great people."
                default-classes="text-sm text-zinc-400 leading-relaxed max-w-xs" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="links_col"
            default-classes="grid grid-cols-2 gap-8">
            <x-dl.grid slug="__SLUG__" prefix="product_links"
                default-grid-classes="space-y-2"
                default-items='[{"label":"Features","url":"#"},{"label":"Pricing","url":"#"},{"label":"Changelog","url":"#"}]'>
                @dlItems('__SLUG__', 'product_links', $productLinks, '[{"label":"Features","url":"#"},{"label":"Pricing","url":"#"},{"label":"Changelog","url":"#"}]')
                <x-dl.wrapper slug="__SLUG__" prefix="product_col_heading" tag="h4"
                    default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-3">
                    Product
                </x-dl.wrapper>
                @foreach ($productLinks as $link)
                    <x-dl.card slug="__SLUG__" prefix="product_link" tag="a"
                        href="{{ $link['url'] }}"
                        default-classes="block text-sm text-zinc-400 hover:text-white transition-colors">
                        {{ $link['label'] }}
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
            <x-dl.grid slug="__SLUG__" prefix="company_links"
                default-grid-classes="space-y-2"
                default-items='[{"label":"About","url":"#"},{"label":"Blog","url":"#"},{"label":"Contact","url":"#"}]'>
                @dlItems('__SLUG__', 'company_links', $companyLinks, '[{"label":"About","url":"#"},{"label":"Blog","url":"#"},{"label":"Contact","url":"#"}]')
                <x-dl.wrapper slug="__SLUG__" prefix="company_col_heading" tag="h4"
                    default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-3">
                    Company
                </x-dl.wrapper>
                @foreach ($companyLinks as $link)
                    <x-dl.card slug="__SLUG__" prefix="company_link" tag="a"
                        href="{{ $link['url'] }}"
                        default-classes="block text-sm text-zinc-400 hover:text-white transition-colors">
                        {{ $link['label'] }}
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="pt-8 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
        <x-dl.wrapper slug="__SLUG__" prefix="copyright" tag="span"
            default-classes="">
            © {{ date('Y') }} All rights reserved.
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="span"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-zinc-300 transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
