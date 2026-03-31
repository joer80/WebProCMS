{{--
@name Icon List - Checklist
@description Simple two-column checklist with check icons and labels.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-12 px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-4xl mx-auto">
    <x-dl.grid slug="__SLUG__" prefix="items"
        default-grid-classes="grid sm:grid-cols-2 gap-4"
        default-items='[{"label":"No credit card required"},{"label":"14-day free trial"},{"label":"Cancel anytime"},{"label":"SOC 2 certified"},{"label":"99.9% uptime SLA"},{"label":"24/7 live support"},{"label":"Free onboarding"},{"label":"GDPR compliant"}]'>
        @dlItems('__SLUG__', 'items', $items, '[{"label":"No credit card required"},{"label":"14-day free trial"},{"label":"Cancel anytime"},{"label":"SOC 2 certified"},{"label":"99.9% uptime SLA"},{"label":"24/7 live support"},{"label":"Free onboarding"},{"label":"GDPR compliant"}]')
        @foreach ($items as $item)
            <x-dl.card slug="__SLUG__" prefix="check_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="flex items-center gap-3">
                <x-dl.icon slug="__SLUG__" prefix="check_icon" name="check-circle"
                    default-classes="size-5 shrink-0 text-primary" />
                <x-dl.wrapper slug="__SLUG__" prefix="check_label" tag="span"
                    default-classes="text-zinc-700 dark:text-zinc-300 font-medium">
                    {{ $item['label'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
