{{--
@name Footer - With Newsletter
@description Footer with email newsletter signup above the link columns.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="bg-zinc-900 px-6"
    default-container-classes="max-w-container mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="newsletter_bar"
        default-classes="py-12 flex flex-col md:flex-row items-center justify-between gap-6 border-b border-zinc-700">
        <x-dl.group slug="__SLUG__" prefix="newsletter_text"
            default-classes="">
            <x-dl.wrapper slug="__SLUG__" prefix="newsletter_heading" tag="h3"
                default-classes="text-lg font-semibold text-white">
                Stay updated
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="newsletter_desc" tag="p"
                default-classes="text-sm text-zinc-400 mt-1">
                Subscribe for product news and updates.
            </x-dl.wrapper>
        </x-dl.group>
        <x-dl.wrapper slug="__SLUG__" prefix="newsletter_form"
            default-classes="flex gap-3 w-full md:w-auto">
            <x-dl.wrapper slug="__SLUG__" prefix="newsletter_input" tag="input"
                type="email" placeholder="Enter your email"
                default-classes="flex-1 md:w-64 rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-primary/40" />
            <x-dl.wrapper slug="__SLUG__" prefix="newsletter_btn" tag="button"
                type="submit"
                default-classes="px-5 py-2.5 rounded-lg bg-primary text-white font-semibold text-sm hover:bg-primary/90 transition-colors shrink-0">
                Subscribe
            </x-dl.wrapper>
        </x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="columns_grid"
        default-classes="grid grid-cols-2 md:grid-cols-4 gap-8 py-12">
        <x-dl.wrapper slug="__SLUG__" prefix="col1_heading" tag="h4"
            default-classes="text-sm font-semibold text-white uppercase tracking-wider mb-4">
            Product
        </x-dl.wrapper>
        <x-dl.grid slug="__SLUG__" prefix="col1_links"
            default-grid-classes="col-span-1 space-y-2"
            default-items='[{"label":"Features","url":"#"},{"label":"Pricing","url":"#"},{"label":"Changelog","url":"#"}]'>
            @dlItems('__SLUG__', 'col1_links', $col1Links, '[{"label":"Features","url":"#"},{"label":"Pricing","url":"#"},{"label":"Changelog","url":"#"}]')
            @foreach ($col1Links as $link)
                <x-dl.card slug="__SLUG__" prefix="col_link" tag="a"
                    data-editor-item-index="{{ $loop->index }}"
                    href="{{ $link['url'] }}"
                    default-classes="block text-sm text-zinc-400 hover:text-white transition-colors">
                    {{ $link['label'] }}
                </x-dl.card>
            @endforeach
        </x-dl.grid>
        <x-dl.wrapper slug="__SLUG__" prefix="col2_heading" tag="h4"
            default-classes="text-sm font-semibold text-white uppercase tracking-wider mb-4">
            Company
        </x-dl.wrapper>
        <x-dl.grid slug="__SLUG__" prefix="col2_links"
            default-grid-classes="col-span-1 space-y-2"
            default-items='[{"label":"About","url":"#"},{"label":"Blog","url":"#"},{"label":"Careers","url":"#"}]'>
            @dlItems('__SLUG__', 'col2_links', $col2Links, '[{"label":"About","url":"#"},{"label":"Blog","url":"#"},{"label":"Careers","url":"#"}]')
            @foreach ($col2Links as $link)
                <x-dl.card slug="__SLUG__" prefix="col2_link" tag="a"
                    data-editor-item-index="{{ $loop->index }}"
                    href="{{ $link['url'] }}"
                    default-classes="block text-sm text-zinc-400 hover:text-white transition-colors">
                    {{ $link['label'] }}
                </x-dl.card>
            @endforeach
        </x-dl.grid>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="py-6 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-zinc-500">
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
