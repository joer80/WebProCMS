{{--
@name Icon List - Horizontal
@description Horizontal row of icon + label pairs, great for trust signals or features.
@sort 10
--}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-12 px-6 bg-zinc-50 dark:bg-zinc-800/50 border-y border-zinc-200 dark:border-zinc-700"
    default-container-classes="max-w-5xl mx-auto">
        @php $itemClasses = content('__SLUG__', 'item_classes', 'flex items-center gap-3'); @endphp
        @php $iconClasses = content('__SLUG__', 'icon_classes', 'size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0'); @endphp
        @php $labelClasses = content('__SLUG__', 'label_classes', 'text-sm font-medium text-zinc-700 dark:text-zinc-300'); @endphp
        <x-dl-grid slug="__SLUG__" prefix="items"
            default-grid-classes="grid grid-cols-2 md:grid-cols-4 gap-8"
            default-items='[{"icon":"✓","label":"No credit card required"},{"icon":"✓","label":"14-day free trial"},{"icon":"✓","label":"Cancel anytime"},{"icon":"✓","label":"SOC 2 compliant"}]'>
            @php $items = json_decode(content('__SLUG__', 'grid_items', ''), true) ?: []; @endphp
            @foreach ($items as $item)
                <div class="{{ $itemClasses }}">
                    <div class="{{ $iconClasses }}">{{ $item['icon'] }}</div>
                    <span class="{{ $labelClasses }}">{{ $item['label'] }}</span>
                </div>
            @endforeach
        </x-dl-grid>
</x-dl-section>
