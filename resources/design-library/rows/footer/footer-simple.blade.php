{{--
@name Footer - Simple
@description Clean footer with logo, nav links, and copyright.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="bg-zinc-900 text-zinc-400 py-12 px-6"
    default-container-classes="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
                href="/"
                default-classes="text-xl font-bold text-white">
                <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
                    default-classes="" />
            </x-dl.wrapper>
            <x-dl.subheadline slug="__SLUG__" prefix="tagline" tag="p" default="Helping you build better things."
                default-classes="mt-2 text-sm" />
        </div>
        <x-dl.wrapper slug="__SLUG__" prefix="nav" tag="nav"
            default-classes="flex flex-wrap gap-6 text-sm">
            <x-dl.wrapper slug="__SLUG__" prefix="nav_link" tag="a" href="#" default-classes="hover:text-white transition-colors">About</x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="nav_link" tag="a" href="#" default-classes="hover:text-white transition-colors">Blog</x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="nav_link" tag="a" href="#" default-classes="hover:text-white transition-colors">Contact</x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="nav_link" tag="a" href="#" default-classes="hover:text-white transition-colors">Privacy</x-dl.wrapper>
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="copyright"
            default-classes="text-sm text-right">
            <p>&copy; {{ date('Y') }} <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
                default-classes="" />. All rights reserved.</p>
            <p>Powered by <a href="https://www.webprocms.com" class="hover:text-white transition-colors">WebProCMS</a></p>
        </x-dl.wrapper>
</x-dl.section>
