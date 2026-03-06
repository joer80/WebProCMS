{{--
@name Footer - Centered
@description Centered footer with logo, nav links, social icons, and copyright.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="py-12 px-6 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-4xl mx-auto text-center">
    <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
        href="/"
        default-classes="inline-block text-xl font-bold text-zinc-900 dark:text-white mb-6">
        <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Your Brand"
            default-classes="" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="nav_links"
        default-grid-classes="flex flex-wrap items-center justify-center gap-6 mb-8"
        default-items='[{"label":"Home","url":"/"},{"label":"About","url":"/about"},{"label":"Services","url":"/services"},{"label":"Blog","url":"/blog"},{"label":"Contact","url":"/contact"}]'>
        @dlItems('__SLUG__', 'nav_links', $navLinks, '[{"label":"Home","url":"/"},{"label":"About","url":"/about"},{"label":"Services","url":"/services"},{"label":"Blog","url":"/blog"},{"label":"Contact","url":"/contact"}]')
        @foreach ($navLinks as $link)
            <x-dl.card slug="__SLUG__" prefix="nav_link" tag="a"
                href="{{ $link['url'] }}"
                default-classes="text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                {{ $link['label'] }}
            </x-dl.card>
        @endforeach
    </x-dl.grid>
    <x-dl.grid slug="__SLUG__" prefix="social_links"
        default-grid-classes="flex items-center justify-center gap-4 mb-8"
        default-items='[{"icon":"globe-alt","url":"#"},{"icon":"chat-bubble-oval-left-ellipsis","url":"#"}]'>
        @dlItems('__SLUG__', 'social_links', $socialLinks, '[{"icon":"globe-alt","url":"#"},{"icon":"chat-bubble-oval-left-ellipsis","url":"#"}]')
        @foreach ($socialLinks as $social)
            <x-dl.card slug="__SLUG__" prefix="social_link" tag="a"
                href="{{ $social['url'] }}"
                default-classes="size-9 rounded-full border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:border-primary hover:text-primary transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="social_icon" name="{{ $social['icon'] }}"
                    default-classes="size-4" />
            </x-dl.card>
        @endforeach
    </x-dl.grid>
    <x-dl.wrapper slug="__SLUG__" prefix="bottom_bar"
        default-classes="flex flex-col sm:flex-row items-center justify-center gap-4 text-xs text-zinc-400">
        <x-dl.wrapper slug="__SLUG__" prefix="copyright" tag="span"
            default-classes="">
            © {{ date('Y') }} All rights reserved.
        </x-dl.wrapper>
        <span>·</span>
        <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="span"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-primary transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
