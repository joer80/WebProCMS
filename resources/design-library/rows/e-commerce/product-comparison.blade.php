{{--
@name E-Commerce - Comparison Table
@description Side-by-side product comparison with features matrix.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Compare Products"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Find the perfect product for your needs."
        default-classes="text-center text-lg text-zinc-500 dark:text-zinc-400 mb-12" />
    <x-dl.wrapper slug="__SLUG__" prefix="table_wrapper"
        default-classes="overflow-x-auto rounded-card border border-zinc-200 dark:border-zinc-700">
        <x-dl.wrapper slug="__SLUG__" prefix="table" tag="table"
            default-classes="w-full text-sm">
            <x-dl.wrapper slug="__SLUG__" prefix="table_head" tag="thead"
                default-classes="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <x-dl.wrapper slug="__SLUG__" prefix="col_feature" tag="th"
                        default-classes="text-left px-6 py-4 text-zinc-700 dark:text-zinc-300 font-semibold w-1/4">
                        Feature
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="col_basic" tag="th"
                        default-classes="px-6 py-4 text-center text-zinc-900 dark:text-white font-semibold">
                        Basic
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="col_standard" tag="th"
                        default-classes="px-6 py-4 text-center text-primary font-semibold">
                        Standard
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="col_premium" tag="th"
                        default-classes="px-6 py-4 text-center text-zinc-900 dark:text-white font-semibold">
                        Premium
                    </x-dl.wrapper>
                </tr>
            </x-dl.wrapper>
            <x-dl.grid slug="__SLUG__" prefix="rows"
                default-grid-classes=""
                default-items='[{"feature":"Material","basic":"Plastic","standard":"Aluminum","premium":"Carbon Fiber"},{"feature":"Weight","basic":"500g","standard":"350g","premium":"220g"},{"feature":"Warranty","basic":"1 year","standard":"2 years","premium":"Lifetime"},{"feature":"Colors","basic":"3","standard":"8","premium":"Custom"},{"feature":"Free Shipping","basic":"","standard":"1","premium":"1"},{"feature":"Priority Support","basic":"","standard":"","premium":"1"}]'>
                @dlItems('__SLUG__', 'rows', $rows, '[{"feature":"Material","basic":"Plastic","standard":"Aluminum","premium":"Carbon Fiber"},{"feature":"Weight","basic":"500g","standard":"350g","premium":"220g"},{"feature":"Warranty","basic":"1 year","standard":"2 years","premium":"Lifetime"},{"feature":"Colors","basic":"3","standard":"8","premium":"Custom"},{"feature":"Free Shipping","basic":"","standard":"1","premium":"1"},{"feature":"Priority Support","basic":"","standard":"","premium":"1"}]')
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
                            <x-dl.wrapper slug="__SLUG__" prefix="row_basic" tag="td"
                                default-classes="px-6 py-4 text-center text-zinc-500 dark:text-zinc-400">
                                @if ($row['basic'] === '1')
                                    <x-dl.icon slug="__SLUG__" prefix="check_basic" name="check-circle:solid" default-classes="size-5 text-primary mx-auto" />
                                @elseif (empty($row['basic']))
                                    <x-dl.icon slug="__SLUG__" prefix="x_basic" name="x-mark" default-classes="size-4 text-zinc-300 mx-auto" />
                                @else
                                    {{ $row['basic'] }}
                                @endif
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="__SLUG__" prefix="row_standard" tag="td"
                                default-classes="px-6 py-4 text-center bg-primary/5 dark:bg-primary/10 text-zinc-900 dark:text-white">
                                @if ($row['standard'] === '1')
                                    <x-dl.icon slug="__SLUG__" prefix="check_std" name="check-circle:solid" default-classes="size-5 text-primary mx-auto" />
                                @elseif (empty($row['standard']))
                                    <x-dl.icon slug="__SLUG__" prefix="x_std" name="x-mark" default-classes="size-4 text-zinc-300 mx-auto" />
                                @else
                                    {{ $row['standard'] }}
                                @endif
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="__SLUG__" prefix="row_premium" tag="td"
                                default-classes="px-6 py-4 text-center text-zinc-500 dark:text-zinc-400">
                                @if ($row['premium'] === '1')
                                    <x-dl.icon slug="__SLUG__" prefix="check_prem" name="check-circle:solid" default-classes="size-5 text-primary mx-auto" />
                                @elseif (empty($row['premium']))
                                    <x-dl.icon slug="__SLUG__" prefix="x_prem" name="x-mark" default-classes="size-4 text-zinc-300 mx-auto" />
                                @else
                                    {{ $row['premium'] }}
                                @endif
                            </x-dl.wrapper>
                        </x-dl.card>
                    @endforeach
                </x-dl.wrapper>
            </x-dl.grid>
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
