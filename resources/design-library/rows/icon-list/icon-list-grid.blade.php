{{--
@name Icon List - Grid
@description Three-column grid of icon items with labels.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Key Features"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="items"
        default-grid-classes="grid grid-cols-2 md:grid-cols-3 gap-8 text-center"
        default-items='[{"icon":"bolt","title":"Fast"},{"icon":"shield-check","title":"Secure"},{"icon":"chart-bar","title":"Analytics"},{"icon":"adjustments-horizontal","title":"Customizable"},{"icon":"globe-alt","title":"Global"},{"icon":"heart","title":"Loved"}]'>
        @dlItems('__SLUG__', 'items', $items, '[{"icon":"bolt","title":"Fast"},{"icon":"shield-check","title":"Secure"},{"icon":"chart-bar","title":"Analytics"},{"icon":"adjustments-horizontal","title":"Customizable"},{"icon":"globe-alt","title":"Global"},{"icon":"heart","title":"Loved"}]')
        @foreach ($items as $item)
            <x-dl.card slug="__SLUG__" prefix="grid_item"
                default-classes="flex flex-col items-center gap-3 p-4">
                <x-dl.icon slug="__SLUG__" prefix="item_icon" name="{{ $item['icon'] }}"
                    default-wrapper-classes="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary"
                    default-classes="size-6" />
                <x-dl.wrapper slug="__SLUG__" prefix="item_title" tag="span"
                    default-classes="font-semibold text-zinc-900 dark:text-white text-sm">
                    {{ $item['title'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
