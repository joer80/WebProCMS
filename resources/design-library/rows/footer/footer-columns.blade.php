{{--
@name Footer - Columns
@description Multi-column footer with logo, link groups, and newsletter signup.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="bg-zinc-900 pt-16 pb-8 px-6"
    default-container-classes="max-w-6xl mx-auto">
        <x-dl.wrapper slug="__SLUG__" prefix="columns_grid"
            default-classes="grid grid-cols-2 md:grid-cols-4 gap-10 mb-12">
            <x-dl.group slug="__SLUG__" prefix="brand_col"
                default-classes="col-span-2 md:col-span-1">
                <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
                    href="/"
                    default-classes="text-xl font-bold text-white">
                    <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
                        default-classes="" />
                </x-dl.wrapper>
                <x-dl.subheadline slug="__SLUG__" prefix="description" tag="p" default="A short description of your company and what makes you unique."
                    default-classes="mt-3 text-sm text-zinc-400 leading-relaxed" />
            </x-dl.group>
            <div>
                <x-dl.wrapper slug="__SLUG__" prefix="column_heading" tag="h4"
                    default-classes="text-sm font-semibold text-white uppercase tracking-wider mb-4">
                    Company
                </x-dl.wrapper>
                <x-dl.group slug="__SLUG__" prefix="column_list" tag="ul"
                    default-classes="space-y-2 text-sm text-zinc-400">
                    <li><x-dl.wrapper slug="__SLUG__" prefix="column_link" tag="a" href="#" default-classes="hover:text-white transition-colors">About</x-dl.wrapper></li>
                    <li><x-dl.wrapper slug="__SLUG__" prefix="column_link" tag="a" href="#" default-classes="hover:text-white transition-colors">Blog</x-dl.wrapper></li>
                    <li><x-dl.wrapper slug="__SLUG__" prefix="column_link" tag="a" href="#" default-classes="hover:text-white transition-colors">Careers</x-dl.wrapper></li>
                </x-dl.group>
            </div>
            <div>
                <x-dl.wrapper slug="__SLUG__" prefix="column_heading" tag="h4"
                    default-classes="text-sm font-semibold text-white uppercase tracking-wider mb-4">
                    Product
                </x-dl.wrapper>
                <x-dl.group slug="__SLUG__" prefix="column_list" tag="ul"
                    default-classes="space-y-2 text-sm text-zinc-400">
                    <li><x-dl.wrapper slug="__SLUG__" prefix="column_link" tag="a" href="#" default-classes="hover:text-white transition-colors">Features</x-dl.wrapper></li>
                    <li><x-dl.wrapper slug="__SLUG__" prefix="column_link" tag="a" href="#" default-classes="hover:text-white transition-colors">Pricing</x-dl.wrapper></li>
                    <li><x-dl.wrapper slug="__SLUG__" prefix="column_link" tag="a" href="#" default-classes="hover:text-white transition-colors">Docs</x-dl.wrapper></li>
                </x-dl.group>
            </div>
            <div>
                <x-dl.wrapper slug="__SLUG__" prefix="column_heading" tag="h4"
                    default-classes="text-sm font-semibold text-white uppercase tracking-wider mb-4">
                    Legal
                </x-dl.wrapper>
                <x-dl.group slug="__SLUG__" prefix="column_list" tag="ul"
                    default-classes="space-y-2 text-sm text-zinc-400">
                    <li><x-dl.wrapper slug="__SLUG__" prefix="column_link" tag="a" href="#" default-classes="hover:text-white transition-colors">Privacy</x-dl.wrapper></li>
                    <li><x-dl.wrapper slug="__SLUG__" prefix="column_link" tag="a" href="#" default-classes="hover:text-white transition-colors">Terms</x-dl.wrapper></li>
                </x-dl.group>
            </div>
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
            default-classes="border-t border-zinc-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-zinc-500">
            <span>&copy; {{ date('Y') }} <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
                default-classes="" />, Inc. All rights reserved.</span>
            <span>Powered by <a href="https://www.webprocms.com" class="hover:text-white transition-colors">WebProCMS</a></span>
        </x-dl.wrapper>
</x-dl.section>
