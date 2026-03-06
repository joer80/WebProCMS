{{--
@name Header - With Banner
@description Announcement banner above a standard sticky header.
@sort 70
--}}
<x-dl.wrapper slug="__SLUG__" prefix="header_outer" tag="div"
    default-classes="sticky top-0 z-50">
    <x-dl.wrapper slug="__SLUG__" prefix="announcement_bar"
        default-classes="bg-primary py-2 px-6 text-center text-sm text-white font-medium">
        🎉 <x-dl.subheadline slug="__SLUG__" prefix="announcement" tag="span" default="New feature launched! Read the announcement →"
            default-classes="inline" />
    </x-dl.wrapper>
    <x-dl.section slug="__SLUG__"
        tag="header"
        default-section-classes="bg-white/95 dark:bg-zinc-900/95 backdrop-blur border-b border-zinc-200 dark:border-zinc-800"
        default-container-classes="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
            href="/"
            default-classes="font-heading text-xl font-bold text-zinc-900 dark:text-white">
            <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
                default-classes="" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="nav" tag="nav"
            default-classes="hidden md:flex items-center gap-8">
            <x-dl.wrapper slug="__SLUG__" prefix="link_features" tag="a" href="#"
                default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Features</x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="link_pricing" tag="a" href="#"
                default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Pricing</x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="link_blog" tag="a" href="#"
                default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Blog</x-dl.wrapper>
        </x-dl.wrapper>
        <x-dl.link slug="__SLUG__" prefix="primary_cta"
            default-label="Get Started"
            default-url="#"
            default-classes="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
    </x-dl.section>
</x-dl.wrapper>
