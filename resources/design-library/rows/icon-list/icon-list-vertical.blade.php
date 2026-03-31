{{--
@name Icon List - Vertical
@description Vertical stack of icon + title + description items.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.grid slug="__SLUG__" prefix="items"
        default-grid-classes="space-y-6"
        default-items='[{"icon":"check-circle","title":"Easy to Use","desc":"Intuitive interface that your team can master in minutes, not months."},{"icon":"bolt","title":"Fast Performance","desc":"Engineered for speed so you never wait on your tools."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security baked into every layer of the platform."}]'>
        @dlItems('__SLUG__', 'items', $items, '[{"icon":"check-circle","title":"Easy to Use","desc":"Intuitive interface that your team can master in minutes, not months."},{"icon":"bolt","title":"Fast Performance","desc":"Engineered for speed so you never wait on your tools."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security baked into every layer of the platform."}]')
        @foreach ($items as $item)
            <x-dl.card slug="__SLUG__" prefix="list_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="flex items-start gap-4">
                <x-dl.icon slug="__SLUG__" prefix="item_icon" name="{{ $item['icon'] }}"
                    default-wrapper-classes="mt-0.5 shrink-0 size-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary"
                    default-classes="size-5" />
                <div>
                    <x-dl.wrapper slug="__SLUG__" prefix="item_title" tag="h3"
                        default-classes="font-semibold text-zinc-900 dark:text-white">
                        {{ $item['title'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="item_desc" tag="p"
                        default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ $item['desc'] }}
                    </x-dl.wrapper>
                </div>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
