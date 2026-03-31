{{--
@name Icon List - Dark
@description Dark background three-column icon list with accent icons.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Built to Last"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Infrastructure you can trust, features you'll love."
            default-classes="mt-4 text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="items"
        default-grid-classes="grid md:grid-cols-3 gap-6"
        default-items='[{"icon":"bolt","title":"Speed","desc":"Millisecond response times at any scale."},{"icon":"shield-check","title":"Security","desc":"Zero-trust architecture by design."},{"icon":"chart-bar","title":"Analytics","desc":"Visibility into every metric that matters."},{"icon":"users","title":"Teamwork","desc":"Collaborate seamlessly across time zones."},{"icon":"globe-alt","title":"Global","desc":"CDN in 200+ locations worldwide."},{"icon":"cog-6-tooth","title":"Automation","desc":"Eliminate manual work with smart workflows."}]'>
        @dlItems('__SLUG__', 'items', $items, '[{"icon":"bolt","title":"Speed","desc":"Millisecond response times at any scale."},{"icon":"shield-check","title":"Security","desc":"Zero-trust architecture by design."},{"icon":"chart-bar","title":"Analytics","desc":"Visibility into every metric that matters."},{"icon":"users","title":"Teamwork","desc":"Collaborate seamlessly across time zones."},{"icon":"globe-alt","title":"Global","desc":"CDN in 200+ locations worldwide."},{"icon":"cog-6-tooth","title":"Automation","desc":"Eliminate manual work with smart workflows."}]')
        @foreach ($items as $item)
            <x-dl.card slug="__SLUG__" prefix="dark_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="p-6 rounded-card bg-zinc-800 border border-zinc-700 hover:border-primary/40 transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="item_icon" name="{{ $item['icon'] }}"
                    default-wrapper-classes="mb-4 size-10 rounded-lg bg-primary/20 flex items-center justify-center text-primary"
                    default-classes="size-5" />
                <x-dl.wrapper slug="__SLUG__" prefix="item_title" tag="h3"
                    default-classes="font-semibold text-white mb-1">
                    {{ $item['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="item_desc" tag="p"
                    default-classes="text-sm text-zinc-400">
                    {{ $item['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
