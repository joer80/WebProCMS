{{--
@name Footer - Social
@description Footer emphasizing social media links with icon grid.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="py-12 px-6 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-4xl mx-auto text-center">
    <x-dl.logo slug="__SLUG__" prefix="logo"
        default-classes="h-8 w-auto mb-2" />
    <x-dl.subheadline slug="__SLUG__" prefix="tagline" tag="p" default="Follow us for updates and inspiration."
        default-classes="text-zinc-500 dark:text-zinc-400 mb-8" />
    <x-dl.grid slug="__SLUG__" prefix="social_links"
        default-grid-classes="flex items-center justify-center gap-4 mb-10"
        default-items='[{"icon":"globe-alt","label":"Website","url":"#"},{"icon":"chat-bubble-oval-left-ellipsis","label":"Twitter","url":"#"},{"icon":"film","label":"YouTube","url":"#"},{"icon":"photo","label":"Instagram","url":"#"}]'>
        @dlItems('__SLUG__', 'social_links', $socialLinks, '[{"icon":"globe-alt","label":"Website","url":"#"},{"icon":"chat-bubble-oval-left-ellipsis","label":"Twitter","url":"#"},{"icon":"film","label":"YouTube","url":"#"},{"icon":"photo","label":"Instagram","url":"#"}]')
        @foreach ($socialLinks as $social)
            <x-dl.card slug="__SLUG__" prefix="social_link" tag="a"
                data-editor-item-index="{{ $loop->index }}"
                href="{{ $social['url'] }}"
                title="{{ $social['label'] }}"
                default-classes="size-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-primary hover:text-white transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="social_icon" name="{{ $social['icon'] }}"
                    default-classes="size-5" />
            </x-dl.card>
        @endforeach
    </x-dl.grid>
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="flex flex-col sm:flex-row items-center justify-center gap-4 text-xs text-zinc-400">
        <x-dl.subheadline slug="__SLUG__" prefix="copyright" tag="span" :default="'© '.date('Y').' All rights reserved.'"
            default-classes="" />
        <span>·</span>
        <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="span"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-primary transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
