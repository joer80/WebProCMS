{{--
@name Stats Row
@description Row of large statistics with number, label, and optional description.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="By the Numbers"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Real results from real customers."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="stats"
        default-grid-classes="grid grid-cols-2 md:grid-cols-4 gap-8 text-center"
        default-items='[{"number":"10K+","label":"Happy Customers","desc":"Across 50 countries"},{"number":"99.9%","label":"Uptime","desc":"In the last 12 months"},{"number":"50M","label":"Events Processed","desc":"Every single day"},{"number":"4.9/5","label":"Average Rating","desc":"On G2 and Capterra"}]'>
        @dlItems('__SLUG__', 'stats', $stats, '[{"number":"10K+","label":"Happy Customers","desc":"Across 50 countries"},{"number":"99.9%","label":"Uptime","desc":"In the last 12 months"},{"number":"50M","label":"Events Processed","desc":"Every single day"},{"number":"4.9/5","label":"Average Rating","desc":"On G2 and Capterra"}]')
        @foreach ($stats as $stat)
            <x-dl.card slug="__SLUG__" prefix="stat_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="p-6">
                <x-dl.wrapper slug="__SLUG__" prefix="stat_number"
                    default-classes="text-4xl font-black text-primary">
                    {{ $stat['number'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="stat_label" tag="p"
                    default-classes="mt-2 font-semibold text-zinc-900 dark:text-white">
                    {{ $stat['label'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="stat_desc" tag="p"
                    default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $stat['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
