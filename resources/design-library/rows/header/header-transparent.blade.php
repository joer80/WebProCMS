{{--
@name Header - Transparent
@description Transparent header designed to overlay a hero image.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="absolute top-0 inset-x-0 z-50"
    default-container-classes="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
    <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
        href="/"
        default-classes="font-heading text-xl font-bold text-white">
        <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
            default-classes="" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="nav" tag="nav"
        default-classes="hidden md:flex items-center gap-8">
        <x-dl.wrapper slug="__SLUG__" prefix="link_features" tag="a" href="#"
            default-classes="text-sm text-white/80 hover:text-white transition-colors">Features</x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="link_pricing" tag="a" href="#"
            default-classes="text-sm text-white/80 hover:text-white transition-colors">Pricing</x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="link_about" tag="a" href="#"
            default-classes="text-sm text-white/80 hover:text-white transition-colors">About</x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.link slug="__SLUG__" prefix="primary_cta"
        default-label="Get Started"
        default-url="#"
        default-classes="px-4 py-2 bg-white text-zinc-900 text-sm font-semibold rounded-lg hover:bg-zinc-100 transition-colors" />
</x-dl.section>
