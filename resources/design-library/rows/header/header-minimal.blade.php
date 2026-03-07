{{--
@name Header - Minimal
@description Ultra-clean header with just logo and a single CTA link.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    tag="header"
    default-section-classes="py-4 px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto flex items-center justify-between">
    <x-dl.logo slug="__SLUG__" prefix="logo"
        default-classes="h-8 w-auto" />
    <x-dl.link slug="__SLUG__" prefix="primary_cta"
        default-label="Contact Us"
        default-url="/contact"
        default-classes="text-sm font-semibold text-primary hover:text-primary/80 transition-colors" />
</x-dl.section>
