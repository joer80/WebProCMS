{{--
@name Footer - Light
@description Light background footer with columns, nav links, and social.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="py-16 px-6 bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="top_grid"
        default-classes="grid md:grid-cols-4 gap-10 mb-12">
        <x-dl.wrapper slug="__SLUG__" prefix="brand_col"
            default-classes="md:col-span-2">
            <x-dl.wrapper slug="__SLUG__" prefix="brand_name" tag="a"
                href="/"
                default-classes="text-xl font-bold text-zinc-900 dark:text-white block mb-3">
                Your Brand
            </x-dl.wrapper>
            <x-dl.subheadline slug="__SLUG__" prefix="brand_desc" tag="p" default="A short description of your company goes here."
                default-classes="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-sm" />
        </x-dl.wrapper>
        <x-dl.grid slug="__SLUG__" prefix="quick_links"
            default-grid-classes="space-y-2"
            default-items='[{"label":"Home","url":"/"},{"label":"About","url":"/about"},{"label":"Services","url":"/services"},{"label":"Contact","url":"/contact"}]'>
            @dlItems('__SLUG__', 'quick_links', $quickLinks, '[{"label":"Home","url":"/"},{"label":"About","url":"/about"},{"label":"Services","url":"/services"},{"label":"Contact","url":"/contact"}]')
            <x-dl.wrapper slug="__SLUG__" prefix="quick_heading" tag="h4"
                default-classes="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">
                Quick Links
            </x-dl.wrapper>
            @foreach ($quickLinks as $link)
                <x-dl.card slug="__SLUG__" prefix="quick_link" tag="a"
                    href="{{ $link['url'] }}"
                    default-classes="block text-sm text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors">
                    {{ $link['label'] }}
                </x-dl.card>
            @endforeach
        </x-dl.grid>
        <x-dl.grid slug="__SLUG__" prefix="legal_links"
            default-grid-classes="space-y-2"
            default-items='[{"label":"Privacy Policy","url":"/privacy"},{"label":"Terms of Service","url":"/terms"},{"label":"Cookie Policy","url":"/cookies"}]'>
            @dlItems('__SLUG__', 'legal_links', $legalLinks, '[{"label":"Privacy Policy","url":"/privacy"},{"label":"Terms of Service","url":"/terms"},{"label":"Cookie Policy","url":"/cookies"}]')
            <x-dl.wrapper slug="__SLUG__" prefix="legal_heading" tag="h4"
                default-classes="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">
                Legal
            </x-dl.wrapper>
            @foreach ($legalLinks as $link)
                <x-dl.card slug="__SLUG__" prefix="legal_link" tag="a"
                    href="{{ $link['url'] }}"
                    default-classes="block text-sm text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors">
                    {{ $link['label'] }}
                </x-dl.card>
            @endforeach
        </x-dl.grid>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="pt-8 border-t border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-400">
        <x-dl.wrapper slug="__SLUG__" prefix="copyright" tag="span"
            default-classes="">
            © {{ date('Y') }} All rights reserved.
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="span"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-primary transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
