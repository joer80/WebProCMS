{{--
@name Footer - Dark Minimal
@description Minimal dark footer with a single nav row and powered by.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="py-8 px-6 bg-zinc-950"
    default-container-classes="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
    <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
        href="/"
        default-classes="text-lg font-bold text-white">
        <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Your Brand"
            default-classes="" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="nav_links"
        default-grid-classes="flex flex-wrap items-center justify-center gap-6"
        default-items='[{"label":"About","url":"/about"},{"label":"Services","url":"/services"},{"label":"Blog","url":"/blog"},{"label":"Contact","url":"/contact"}]'>
        @dlItems('__SLUG__', 'nav_links', $navLinks, '[{"label":"About","url":"/about"},{"label":"Services","url":"/services"},{"label":"Blog","url":"/blog"},{"label":"Contact","url":"/contact"}]')
        @foreach ($navLinks as $link)
            <x-dl.card slug="__SLUG__" prefix="nav_link" tag="a"
                href="{{ $link['url'] }}"
                default-classes="text-sm text-zinc-400 hover:text-white transition-colors">
                {{ $link['label'] }}
            </x-dl.card>
        @endforeach
    </x-dl.grid>
    <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="p"
        default-classes="text-xs text-zinc-600">
        Powered by <a href="https://www.webprocms.com" class="hover:text-zinc-400 transition-colors">WebProCMS</a>
    </x-dl.wrapper>
</x-dl.section>
