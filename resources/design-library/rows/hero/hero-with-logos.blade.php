{{--
@name Hero - With Logos
@description Centered hero with CTA buttons and a "trusted by" company logos row below.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section-hero px-6 bg-white dark:bg-zinc-900 text-center"
    default-container-classes="max-w-4xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Trusted by the World's Best Teams"
        default-tag="h1"
        default-classes="font-heading text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Join over 10,000 companies that rely on us every day to power their most important work."
        default-classes="mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-10 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Get Started Free"
        default-primary-classes="btn-primary"
        default-secondary-label="See How It Works"
        default-secondary-classes="btn-secondary" />
    <x-dl.wrapper slug="__SLUG__" prefix="logos_section"
        default-classes="mt-16 pt-10 border-t border-zinc-200 dark:border-zinc-800">
        <x-dl.subheadline slug="__SLUG__" prefix="logos_label" default="Trusted by industry leaders"
            default-classes="text-sm text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-8" />
        <x-dl.grid slug="__SLUG__" prefix="logos"
            default-grid-classes="grid grid-cols-3 md:grid-cols-6 gap-8 items-center justify-items-center"
            default-items='[{"name":"Acme Corp"},{"name":"TechCorp"},{"name":"BuildIt"},{"name":"StartupXYZ"},{"name":"Innovate"},{"name":"FutureLabs"}]'>
            @dlItems('__SLUG__', 'logos', $logos, '[{"name":"Acme Corp"},{"name":"TechCorp"},{"name":"BuildIt"},{"name":"StartupXYZ"},{"name":"Innovate"},{"name":"FutureLabs"}]')
            @foreach ($logos as $logo)
                <x-dl.card slug="__SLUG__" prefix="logo_item"
                    data-editor-item-index="{{ $loop->index }}"
                    default-classes="flex items-center justify-center">
                    <x-dl.wrapper slug="__SLUG__" prefix="logo_name" tag="span"
                        default-classes="text-sm font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                        {{ $logo['name'] }}
                    </x-dl.wrapper>
                </x-dl.card>
            @endforeach
        </x-dl.grid>
    </x-dl.wrapper>
</x-dl.section>
