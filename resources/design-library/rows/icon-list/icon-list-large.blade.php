{{--
@name Icon List - Large Icons
@description Two-column feature grid with extra-large centered icons.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Core Capabilities"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="items"
        default-grid-classes="grid md:grid-cols-2 gap-10"
        default-items='[{"icon":"bolt","title":"Blazing Performance","desc":"Our infrastructure is optimized from the ground up for sub-100ms global response times."},{"icon":"shield-check","title":"Security First","desc":"Zero-trust architecture, end-to-end encryption, and SOC 2 Type II certification."},{"icon":"chart-bar","title":"Deep Analytics","desc":"360-degree visibility into your operations with real-time dashboards and custom reports."},{"icon":"cog-6-tooth","title":"Flexible Automation","desc":"Build powerful workflows without writing code using our drag-and-drop automation builder."}]'>
        @dlItems('__SLUG__', 'items', $items, '[{"icon":"bolt","title":"Blazing Performance","desc":"Our infrastructure is optimized from the ground up for sub-100ms global response times."},{"icon":"shield-check","title":"Security First","desc":"Zero-trust architecture, end-to-end encryption, and SOC 2 Type II certification."},{"icon":"chart-bar","title":"Deep Analytics","desc":"360-degree visibility into your operations with real-time dashboards and custom reports."},{"icon":"cog-6-tooth","title":"Flexible Automation","desc":"Build powerful workflows without writing code using our drag-and-drop automation builder."}]')
        @foreach ($items as $item)
            <x-dl.card slug="__SLUG__" prefix="large_item"
                default-classes="text-center px-6">
                <x-dl.icon slug="__SLUG__" prefix="large_icon" name="{{ $item['icon'] }}"
                    default-wrapper-classes="mx-auto mb-5 size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary"
                    default-classes="size-8" />
                <x-dl.wrapper slug="__SLUG__" prefix="item_title" tag="h3"
                    default-classes="text-xl font-bold text-zinc-900 dark:text-white mb-2">
                    {{ $item['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="item_desc" tag="p"
                    default-classes="text-zinc-500 dark:text-zinc-400 leading-relaxed">
                    {{ $item['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
