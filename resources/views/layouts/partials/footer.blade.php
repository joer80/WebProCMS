<?php /** @layout-partial footer */ ?>
{{-- ROW:start:footer-light:footer --}}
<div class="contents">
<x-dl.section slug="footer-light:footer"
    tag="footer"
    default-section-classes="py-16 px-6 bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-5 gap-10">
    <x-dl.wrapper slug="footer-light:footer" prefix="brand_col"
        default-classes="md:col-span-2">
        <x-dl.logo slug="footer-light:footer" prefix="logo"
            default-classes="h-8 w-auto mb-3" />
        <x-dl.subheadline slug="footer-light:footer" prefix="brand_desc" tag="p" default="A short description of your company goes here."
            default-classes="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-sm" />
    </x-dl.wrapper>
    <div>
        <x-dl.subheadline slug="footer-light:footer" prefix="col1_heading" tag="h4" default="Quick Links"
            default-classes="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-4" />
        <x-dl.nav slug="footer-light:footer" prefix="col1_nav"
            default-menu="main-navigation"
            default-accordion="1"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors" />
    </div>
    <div>
        <x-dl.subheadline slug="footer-light:footer" prefix="col2_heading" tag="h4" default="Company"
            default-classes="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-4" />
        <x-dl.nav slug="footer-light:footer" prefix="col2_nav"
            default-menu="footer-company"
            default-accordion="1"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors" />
    </div>
    <div>
        <x-dl.subheadline slug="footer-light:footer" prefix="col3_heading" tag="h4" default="Legal"
            default-classes="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-4" />
        <x-dl.nav slug="footer-light:footer" prefix="col3_nav"
            default-menu="legal"
            default-accordion="1"
            default-classes="space-y-2"
            default-item-classes="block text-sm text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors" />
    </div>
    <x-dl.wrapper slug="footer-light:footer" prefix="bottom_bar"
        default-classes="col-span-full pt-8 border-t border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-400">
        <x-dl.subheadline slug="footer-light:footer" prefix="copyright" tag="span" :default="'© '.date('Y').' All rights reserved.'"
            default-classes="" />
        <x-dl.wrapper slug="footer-light:footer" prefix="powered_by" tag="span"
            default-classes="">
            Powered by <a href="https://www.webprocms.com" class="hover:text-primary transition-colors">WebProCMS</a>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
</div>
{{-- ROW:end:footer-light:footer --}}
