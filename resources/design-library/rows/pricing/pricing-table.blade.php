{{--
@name Pricing - Feature Table
@description Comparison table showing features across multiple plans.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Compare Plans"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="See exactly what you get with each plan."
        default-classes="text-center text-lg text-zinc-500 dark:text-zinc-400 mb-12" />
    <x-dl.wrapper slug="__SLUG__" prefix="table_wrapper"
        default-classes="overflow-x-auto rounded-card border border-zinc-200 dark:border-zinc-700">
        <x-dl.wrapper slug="__SLUG__" prefix="table" tag="table"
            default-classes="w-full text-sm">
            <x-dl.wrapper slug="__SLUG__" prefix="table_head" tag="thead"
                default-classes="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <x-dl.wrapper slug="__SLUG__" prefix="col_feature" tag="th"
                        default-classes="text-left px-6 py-4 text-zinc-900 dark:text-white font-semibold">
                        Feature
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="col_starter" tag="th"
                        default-classes="px-6 py-4 text-zinc-900 dark:text-white font-semibold text-center">
                        Starter
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="col_pro" tag="th"
                        default-classes="px-6 py-4 text-primary font-semibold text-center">
                        Pro
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="col_enterprise" tag="th"
                        default-classes="px-6 py-4 text-zinc-900 dark:text-white font-semibold text-center">
                        Enterprise
                    </x-dl.wrapper>
                </tr>
            </x-dl.wrapper>
            <x-dl.grid slug="__SLUG__" prefix="rows"
                default-grid-classes=""
                default-items='[{"feature":"Projects","starter":"5","pro":"Unlimited","enterprise":"Unlimited"},{"feature":"Storage","starter":"10 GB","pro":"100 GB","enterprise":"1 TB"},{"feature":"Team members","starter":"1","pro":"10","enterprise":"Unlimited"},{"feature":"Analytics","starter":"Basic","pro":"Advanced","enterprise":"Custom"},{"feature":"API access","starter":"","pro":"1","enterprise":"1"},{"feature":"Priority support","starter":"","pro":"1","enterprise":"1"}]'>
                @dlItems('__SLUG__', 'rows', $rows, '[{"feature":"Projects","starter":"5","pro":"Unlimited","enterprise":"Unlimited"},{"feature":"Storage","starter":"10 GB","pro":"100 GB","enterprise":"1 TB"},{"feature":"Team members","starter":"1","pro":"10","enterprise":"Unlimited"},{"feature":"Analytics","starter":"Basic","pro":"Advanced","enterprise":"Custom"},{"feature":"API access","starter":"","pro":"1","enterprise":"1"},{"feature":"Priority support","starter":"","pro":"1","enterprise":"1"}]')
                <x-dl.wrapper slug="__SLUG__" prefix="table_body" tag="tbody"
                    default-classes="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($rows as $row)
                        <x-dl.card slug="__SLUG__" prefix="table_row" tag="tr"
                            data-editor-item-index="{{ $loop->index }}"
                            default-classes="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <x-dl.wrapper slug="__SLUG__" prefix="row_feature" tag="td"
                                default-classes="px-6 py-4 font-medium text-zinc-700 dark:text-zinc-300">
                                {{ $row['feature'] }}
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="__SLUG__" prefix="row_starter" tag="td"
                                default-classes="px-6 py-4 text-center text-zinc-500 dark:text-zinc-400">
                                @if ($row['starter'] === '1')
                                    <x-dl.icon slug="__SLUG__" prefix="check_icon" name="check-circle:solid" default-classes="size-5 text-primary mx-auto" />
                                @elseif (empty($row['starter']))
                                    <x-dl.icon slug="__SLUG__" prefix="x_icon" name="x-mark" default-classes="size-4 text-zinc-300 mx-auto" />
                                @else
                                    {{ $row['starter'] }}
                                @endif
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="__SLUG__" prefix="row_pro" tag="td"
                                default-classes="px-6 py-4 text-center text-zinc-900 dark:text-white font-medium bg-primary/5 dark:bg-primary/10">
                                @if ($row['pro'] === '1')
                                    <x-dl.icon slug="__SLUG__" prefix="check_pro_icon" name="check-circle:solid" default-classes="size-5 text-primary mx-auto" />
                                @elseif (empty($row['pro']))
                                    <x-dl.icon slug="__SLUG__" prefix="x_pro_icon" name="x-mark" default-classes="size-4 text-zinc-300 mx-auto" />
                                @else
                                    {{ $row['pro'] }}
                                @endif
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="__SLUG__" prefix="row_enterprise" tag="td"
                                default-classes="px-6 py-4 text-center text-zinc-500 dark:text-zinc-400">
                                @if ($row['enterprise'] === '1')
                                    <x-dl.icon slug="__SLUG__" prefix="check_ent_icon" name="check-circle:solid" default-classes="size-5 text-primary mx-auto" />
                                @elseif (empty($row['enterprise']))
                                    <x-dl.icon slug="__SLUG__" prefix="x_ent_icon" name="x-mark" default-classes="size-4 text-zinc-300 mx-auto" />
                                @else
                                    {{ $row['enterprise'] }}
                                @endif
                            </x-dl.wrapper>
                        </x-dl.card>
                    @endforeach
                </x-dl.wrapper>
            </x-dl.grid>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
