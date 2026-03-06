{{--
@name Header - Centered Logo
@description Header with logo centered and navigation links on both sides.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="sticky top-0 z-50 bg-white/90 dark:bg-zinc-900/90 backdrop-blur border-b border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-6xl mx-auto px-6 h-16 grid grid-cols-3 items-center">
    <x-dl.wrapper slug="__SLUG__" prefix="nav_left" tag="nav"
        default-classes="hidden md:flex items-center gap-6">
        <x-dl.wrapper slug="__SLUG__" prefix="link_features" tag="a" href="#"
            default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Features</x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="link_pricing" tag="a" href="#"
            default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Pricing</x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
        href="/"
        default-classes="text-center font-heading text-xl font-bold text-zinc-900 dark:text-white">
        <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
            default-classes="" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="nav_right"
        default-classes="flex items-center justify-end gap-6">
        <x-dl.wrapper slug="__SLUG__" prefix="link_blog" tag="a" href="#"
            default-classes="hidden md:block text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Blog</x-dl.wrapper>
        <x-dl.link slug="__SLUG__" prefix="primary_cta"
            default-label="Get Started"
            default-url="#"
            default-classes="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
    </x-dl.wrapper>
</x-dl.section>
