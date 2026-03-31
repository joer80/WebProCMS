{{--
@name Icon List - Icon Cards
@description Three-column grid of styled icon cards with hover effects.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Built for Scale"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Core capabilities powering thousands of teams."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="items"
        default-grid-classes="grid md:grid-cols-3 gap-6"
        default-items='[{"icon":"bolt","title":"Performance","desc":"Sub-100ms response times globally."},{"icon":"shield-check","title":"Security","desc":"SOC 2 Type II certified infrastructure."},{"icon":"globe-alt","title":"Scale","desc":"Handle millions of requests per minute."},{"icon":"chart-bar","title":"Insights","desc":"Real-time dashboards and reporting."},{"icon":"cog-6-tooth","title":"Automation","desc":"Automate workflows without code."},{"icon":"users","title":"Collaboration","desc":"Real-time editing and team spaces."}]'>
        @dlItems('__SLUG__', 'items', $items, '[{"icon":"bolt","title":"Performance","desc":"Sub-100ms response times globally."},{"icon":"shield-check","title":"Security","desc":"SOC 2 Type II certified infrastructure."},{"icon":"globe-alt","title":"Scale","desc":"Handle millions of requests per minute."},{"icon":"chart-bar","title":"Insights","desc":"Real-time dashboards and reporting."},{"icon":"cog-6-tooth","title":"Automation","desc":"Automate workflows without code."},{"icon":"users","title":"Collaboration","desc":"Real-time editing and team spaces."}]')
        @foreach ($items as $item)
            <x-dl.card slug="__SLUG__" prefix="icon_card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="p-6 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 hover:shadow-card transition-all">
                <x-dl.icon slug="__SLUG__" prefix="card_icon" name="{{ $item['icon'] }}"
                    default-wrapper-classes="mb-4 size-11 rounded-xl bg-primary/10 flex items-center justify-center text-primary"
                    default-classes="size-5" />
                <x-dl.wrapper slug="__SLUG__" prefix="card_title" tag="h3"
                    default-classes="font-semibold text-zinc-900 dark:text-white mb-1">
                    {{ $item['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="card_desc" tag="p"
                    default-classes="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $item['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
