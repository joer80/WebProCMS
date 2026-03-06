{{--
@name Header - Split
@description Header with nav links on the left and logo in the center-right.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="sticky top-0 z-50 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-6xl mx-auto px-6 h-16 flex items-center gap-8">
    <x-dl.wrapper slug="__SLUG__" prefix="nav" tag="nav"
        default-classes="hidden md:flex items-center gap-6 flex-1">
        <x-dl.wrapper slug="__SLUG__" prefix="link_features" tag="a" href="#"
            default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Features</x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="link_pricing" tag="a" href="#"
            default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Pricing</x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="link_blog" tag="a" href="#"
            default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Blog</x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="link_contact" tag="a" href="#"
            default-classes="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Contact</x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="brand" tag="a"
        href="/"
        default-classes="font-heading text-xl font-bold text-zinc-900 dark:text-white mx-auto">
        <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Brand"
            default-classes="" />
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="right_actions"
        default-classes="flex-1 flex items-center justify-end gap-3">
        <x-dl.link slug="__SLUG__" prefix="secondary_cta"
            default-label="Log In"
            default-url="#"
            default-classes="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors hidden md:block" />
        <x-dl.link slug="__SLUG__" prefix="primary_cta"
            default-label="Sign Up"
            default-url="#"
            default-classes="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors" />
    </x-dl.wrapper>
</x-dl.section>
