{{--
@name Logo Cloud
@description Horizontal row of company logos or partner brands.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-12 px-6 bg-zinc-50 dark:bg-zinc-800/50 border-y border-zinc-200 dark:border-zinc-700"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.subheadline slug="__SLUG__" prefix="label" default="Trusted by leading companies worldwide"
        default-classes="text-center text-sm text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-10" />
    <x-dl.grid slug="__SLUG__" prefix="logos"
        default-grid-classes="grid grid-cols-3 md:grid-cols-6 gap-8 items-center justify-items-center"
        default-items='[{"name":"Acme Corp"},{"name":"TechCorp"},{"name":"BuildIt Inc"},{"name":"StartupXYZ"},{"name":"Innovate Co"},{"name":"Future Labs"}]'>
        @dlItems('__SLUG__', 'logos', $logos, '[{"name":"Acme Corp"},{"name":"TechCorp"},{"name":"BuildIt Inc"},{"name":"StartupXYZ"},{"name":"Innovate Co"},{"name":"Future Labs"}]')
        @foreach ($logos as $logo)
            <x-dl.card slug="__SLUG__" prefix="logo_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="flex items-center justify-center">
                <x-dl.wrapper slug="__SLUG__" prefix="logo_name" tag="span"
                    default-classes="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                    {{ $logo['name'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
