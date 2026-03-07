{{--
@name Footer - Mega
@description Large footer with many link columns, social, newsletter, and awards.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="bg-zinc-900 px-6"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-5 gap-10">
    <x-dl.wrapper slug="__SLUG__" prefix="brand_col"
        default-classes="md:col-span-2 py-16">
        <x-dl.logo slug="__SLUG__" prefix="logo"
            default-classes="h-8 w-auto mb-3" />
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
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="col1_heading" tag="h4" default="Product"
            default-classes="pt-16 text-xs font-semibold text-white uppercase tracking-widest mb-4" />
        <x-dl.nav slug="__SLUG__" prefix="col1_nav"
            default-menu="main-navigation"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
    </div>
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="col2_heading" tag="h4" default="Company"
            default-classes="pt-16 text-xs font-semibold text-white uppercase tracking-widest mb-4" />
        <x-dl.nav slug="__SLUG__" prefix="col2_nav"
            default-menu="main-navigation"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
    </div>
    <div>
        <x-dl.subheadline slug="__SLUG__" prefix="col3_heading" tag="h4" default="Legal"
            default-classes="pt-16 text-xs font-semibold text-white uppercase tracking-widest mb-4" />
        <x-dl.nav slug="__SLUG__" prefix="col3_nav"
            default-menu="legal"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-400 hover:text-white transition-colors" />
    </div>
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="col-span-full border-t border-zinc-800 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
        <x-dl.subheadline slug="__SLUG__" prefix="copyright" tag="span" :default="'© '.date('Y').' All rights reserved.'"
            default-classes="" />
        <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="span"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-zinc-300 transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
