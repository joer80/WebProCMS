{{--
@name Footer - Minimal
@description Single-line minimal footer with copyright and powered-by link.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    tag="footer"
    default-section-classes="py-6 px-6 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
    <x-dl.wrapper slug="__SLUG__" prefix="copyright" tag="p"
        default-classes="text-sm text-zinc-500 dark:text-zinc-400">
        © {{ date('Y') }} <x-dl.subheadline slug="__SLUG__" prefix="brand_name" tag="span" default="Your Company"
            default-classes="" />. All rights reserved.
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="powered_by" tag="p"
        default-classes="text-xs text-zinc-400 dark:text-zinc-500">
        Powered by <a href="https://www.webprocms.com" class="hover:text-primary transition-colors">WebProCMS</a>
    </x-dl.wrapper>
</x-dl.section>
