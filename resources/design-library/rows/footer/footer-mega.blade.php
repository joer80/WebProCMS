{{--
@name Footer - Mega
@description Large footer with many link columns, social, newsletter, and awards.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="bg-zinc-900 px-6"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="top_grid"
        default-classes="grid md:grid-cols-5 gap-10 py-16 border-b border-zinc-800">
        <x-dl.wrapper slug="__SLUG__" prefix="brand_col"
            default-classes="md:col-span-2">
            <x-dl.wrapper slug="__SLUG__" prefix="brand_name" tag="a"
                href="/"
                default-classes="text-xl font-bold text-white block mb-3">
                Your Brand
            </x-dl.wrapper>
            <x-dl.subheadline slug="__SLUG__" prefix="brand_desc" tag="p" default="We build tools that help teams ship faster and grow smarter."
                default-classes="text-sm text-zinc-400 leading-relaxed mb-6 max-w-xs" />
            <x-dl.grid slug="__SLUG__" prefix="social_links"
                default-grid-classes="flex items-center gap-3"
                default-items='[{"icon":"globe-alt","url":"#"},{"icon":"chat-bubble-oval-left-ellipsis","url":"#"},{"icon":"film","url":"#"}]'>
                @dlItems('__SLUG__', 'social_links', $socialLinks, '[{"icon":"globe-alt","url":"#"},{"icon":"chat-bubble-oval-left-ellipsis","url":"#"},{"icon":"film","url":"#"}]')
                @foreach ($socialLinks as $social)
                    <x-dl.card slug="__SLUG__" prefix="social_link" tag="a"
                        href="{{ $social['url'] }}"
                        default-classes="size-9 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400 hover:bg-primary hover:text-white transition-colors">
                        <x-dl.icon slug="__SLUG__" prefix="social_icon" name="{{ $social['icon'] }}"
                            default-classes="size-4" />
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
        </x-dl.wrapper>
        <x-dl.grid slug="__SLUG__" prefix="col1_links"
            default-grid-classes="space-y-2"
            default-items='[{"label":"Features","url":"#"},{"label":"Pricing","url":"#"},{"label":"Docs","url":"#"},{"label":"Changelog","url":"#"}]'>
            @dlItems('__SLUG__', 'col1_links', $col1Links, '[{"label":"Features","url":"#"},{"label":"Pricing","url":"#"},{"label":"Docs","url":"#"},{"label":"Changelog","url":"#"}]')
            <x-dl.wrapper slug="__SLUG__" prefix="col1_heading" tag="h4"
                default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-4">
                Product
            </x-dl.wrapper>
            @foreach ($col1Links as $link)
                <x-dl.card slug="__SLUG__" prefix="col1_link" tag="a"
                    href="{{ $link['url'] }}"
                    default-classes="block text-sm text-zinc-400 hover:text-white transition-colors">
                    {{ $link['label'] }}
                </x-dl.card>
            @endforeach
        </x-dl.grid>
        <x-dl.grid slug="__SLUG__" prefix="col2_links"
            default-grid-classes="space-y-2"
            default-items='[{"label":"About","url":"#"},{"label":"Blog","url":"#"},{"label":"Careers","url":"#"},{"label":"Press","url":"#"}]'>
            @dlItems('__SLUG__', 'col2_links', $col2Links, '[{"label":"About","url":"#"},{"label":"Blog","url":"#"},{"label":"Careers","url":"#"},{"label":"Press","url":"#"}]')
            <x-dl.wrapper slug="__SLUG__" prefix="col2_heading" tag="h4"
                default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-4">
                Company
            </x-dl.wrapper>
            @foreach ($col2Links as $link)
                <x-dl.card slug="__SLUG__" prefix="col2_link" tag="a"
                    href="{{ $link['url'] }}"
                    default-classes="block text-sm text-zinc-400 hover:text-white transition-colors">
                    {{ $link['label'] }}
                </x-dl.card>
            @endforeach
        </x-dl.grid>
        <x-dl.grid slug="__SLUG__" prefix="col3_links"
            default-grid-classes="space-y-2"
            default-items='[{"label":"Privacy Policy","url":"#"},{"label":"Terms of Service","url":"#"},{"label":"Security","url":"#"},{"label":"Cookies","url":"#"}]'>
            @dlItems('__SLUG__', 'col3_links', $col3Links, '[{"label":"Privacy Policy","url":"#"},{"label":"Terms of Service","url":"#"},{"label":"Security","url":"#"},{"label":"Cookies","url":"#"}]')
            <x-dl.wrapper slug="__SLUG__" prefix="col3_heading" tag="h4"
                default-classes="text-xs font-semibold text-white uppercase tracking-widest mb-4">
                Legal
            </x-dl.wrapper>
            @foreach ($col3Links as $link)
                <x-dl.card slug="__SLUG__" prefix="col3_link" tag="a"
                    href="{{ $link['url'] }}"
                    default-classes="block text-sm text-zinc-400 hover:text-white transition-colors">
                    {{ $link['label'] }}
                </x-dl.card>
            @endforeach
        </x-dl.grid>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
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
