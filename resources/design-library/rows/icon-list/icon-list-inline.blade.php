{{--
@name Icon List - Inline
@description Single horizontal row of small icon + label pairs.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-10 px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.grid slug="__SLUG__" prefix="items"
        default-grid-classes="flex flex-wrap items-center justify-center gap-8"
        default-items='[{"icon":"check","label":"Fast"},{"icon":"shield-check","label":"Secure"},{"icon":"star","label":"Rated 4.9"},{"icon":"users","label":"10K+ Users"},{"icon":"globe-alt","label":"Global"},{"icon":"bolt","label":"99.9% Uptime"}]'>
        @dlItems('__SLUG__', 'items', $items, '[{"icon":"check","label":"Fast"},{"icon":"shield-check","label":"Secure"},{"icon":"star","label":"Rated 4.9"},{"icon":"users","label":"10K+ Users"},{"icon":"globe-alt","label":"Global"},{"icon":"bolt","label":"99.9% Uptime"}]')
        @foreach ($items as $item)
            <x-dl.card slug="__SLUG__" prefix="inline_item"
                default-classes="flex items-center gap-2">
                <x-dl.icon slug="__SLUG__" prefix="item_icon" name="{{ $item['icon'] }}"
                    default-classes="size-4 text-primary shrink-0" />
                <x-dl.wrapper slug="__SLUG__" prefix="item_label" tag="span"
                    default-classes="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    {{ $item['label'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
