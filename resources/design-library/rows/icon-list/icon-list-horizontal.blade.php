{{--
@name Icon List - Horizontal
@description Horizontal row of icon + label pairs, great for trust signals or features.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-12 px-6 bg-zinc-50 dark:bg-zinc-800/50 border-y border-zinc-200 dark:border-zinc-700"
    default-container-classes="max-w-5xl mx-auto">
        <x-dl.grid slug="__SLUG__" prefix="items"
            default-grid-classes="grid grid-cols-2 md:grid-cols-4 gap-8"
            default-items='[{"icon":"✓","label":"No credit card required"},{"icon":"✓","label":"14-day free trial"},{"icon":"✓","label":"Cancel anytime"},{"icon":"✓","label":"SOC 2 compliant"}]'>
            @dlItems('__SLUG__', 'items', $items, '[{"icon":"\u2713","label":"No credit card required"},{"icon":"\u2713","label":"14-day free trial"},{"icon":"\u2713","label":"Cancel anytime"},{"icon":"\u2713","label":"SOC 2 compliant"}]')
            @foreach ($items as $item)
                <x-dl.card slug="__SLUG__" prefix="item"
                    default-classes="flex items-center gap-3">
                    <x-dl.wrapper slug="__SLUG__" prefix="icon"
                        default-classes="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                        {{ $item['icon'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="label" tag="span"
                        default-classes="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        {{ $item['label'] }}
                    </x-dl.wrapper>
                </x-dl.card>
            @endforeach
        </x-dl.grid>
</x-dl.section>
