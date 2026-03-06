{{--
@name Social Proof - Logo Cloud
@description Row of company logos showing "trusted by" brands.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-12 px-6 bg-white dark:bg-zinc-900 border-y border-zinc-200 dark:border-zinc-800"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.subheadline slug="__SLUG__" prefix="label" default="Trusted by industry leaders"
        default-classes="text-center text-sm text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-10" />
    <x-dl.grid slug="__SLUG__" prefix="logos"
        default-grid-classes="grid grid-cols-3 md:grid-cols-6 gap-8 items-center justify-items-center"
        default-items='[{"name":"Acme Corp"},{"name":"TechCorp"},{"name":"BuildIt"},{"name":"StartupXYZ"},{"name":"Innovate"},{"name":"FutureLabs"}]'>
        @dlItems('__SLUG__', 'logos', $logos, '[{"name":"Acme Corp"},{"name":"TechCorp"},{"name":"BuildIt"},{"name":"StartupXYZ"},{"name":"Innovate"},{"name":"FutureLabs"}]')
        @foreach ($logos as $logo)
            <x-dl.card slug="__SLUG__" prefix="logo_item"
                default-classes="flex items-center justify-center">
                <x-dl.wrapper slug="__SLUG__" prefix="logo_name" tag="span"
                    default-classes="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-center">
                    {{ $logo['name'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
