{{--
@name Header - Logo Left
@description Left-aligned logo with right-side navigation, no backdrop blur.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="sticky top-0 z-50 bg-white dark:bg-zinc-900 shadow-sm"
    default-container-classes="max-w-7xl mx-auto px-8 h-16 flex items-center justify-between">
    <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
        href="/"
        default-classes="font-heading text-xl font-bold text-zinc-900 dark:text-white">
        <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
            default-classes="" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="nav" tag="nav"
        default-classes="hidden md:flex items-center gap-8">
        <x-dl.wrapper slug="__SLUG__" prefix="link_home" tag="a" href="/"
            default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-primary transition-colors">Home</x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="link_about" tag="a" href="#"
            default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-primary transition-colors">About</x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="link_services" tag="a" href="#"
            default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-primary transition-colors">Services</x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="link_blog" tag="a" href="#"
            default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-primary transition-colors">Blog</x-dl.wrapper>
        <x-dl.link slug="__SLUG__" prefix="primary_cta"
            default-label="Contact Us"
            default-url="/contact"
            default-classes="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
    </x-dl.wrapper>
</x-dl.section>
