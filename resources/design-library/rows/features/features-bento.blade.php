{{--
@name Features - Bento Grid
@description Modern bento-style feature grid with varying card emphasis.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="One Platform, Endless Possibilities"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Every tool you need, beautifully integrated."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="items"
        default-grid-classes="grid md:grid-cols-3 gap-4"
        default-items='[{"icon":"bolt","title":"Instant Performance","desc":"Optimized from the ground up for speed."},{"icon":"shield-check","title":"Built-in Security","desc":"Zero-trust architecture by default."},{"icon":"chart-bar","title":"Advanced Analytics","desc":"Real-time insights across your entire stack."},{"icon":"users","title":"Collaboration Tools","desc":"Work together, in real time, from anywhere."},{"icon":"globe-alt","title":"Global Delivery","desc":"Edge-cached assets in 200+ locations."},{"icon":"cog-6-tooth","title":"Automation Engine","desc":"Automate any workflow without code."}]'>
        @dlItems('__SLUG__', 'items', $items, '[{"icon":"bolt","title":"Instant Performance","desc":"Optimized from the ground up for speed."},{"icon":"shield-check","title":"Built-in Security","desc":"Zero-trust architecture by default."},{"icon":"chart-bar","title":"Advanced Analytics","desc":"Real-time insights across your entire stack."},{"icon":"users","title":"Collaboration Tools","desc":"Work together, in real time, from anywhere."},{"icon":"globe-alt","title":"Global Delivery","desc":"Edge-cached assets in 200+ locations."},{"icon":"cog-6-tooth","title":"Automation Engine","desc":"Automate any workflow without code."}]')
        @foreach ($items as $item)
            <x-dl.card slug="__SLUG__" prefix="item_card"
                default-classes="p-6 rounded-card bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 hover:bg-white dark:hover:bg-zinc-700 transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="icon" name="{{ $item['icon'] }}"
                    default-wrapper-classes="mb-4 text-primary"
                    default-classes="size-7" />
                <x-dl.wrapper slug="__SLUG__" prefix="item_title" tag="h3"
                    default-classes="font-semibold text-zinc-900 dark:text-white mb-1">
                    {{ $item['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="item_desc" tag="p"
                    default-classes="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $item['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
