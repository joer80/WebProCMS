{{--
@name Icon List - Two Column
@description Two-column layout with heading on left and icon list on right.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-container mx-auto grid md:grid-cols-2 gap-16 items-start">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="What's Included"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Everything your team needs, all in one package with no surprise fees."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
        <x-dl.link slug="__SLUG__" prefix="cta"
            default-label="See full feature list →"
            default-url="#"
            default-classes="mt-6 inline-block text-primary font-semibold hover:text-primary/80 transition-colors" />
    </div>
    <x-dl.grid slug="__SLUG__" prefix="items"
        default-grid-classes="grid sm:grid-cols-2 gap-4"
        default-items='[{"icon":"bolt","title":"Performance"},{"icon":"shield-check","title":"Security"},{"icon":"chart-bar","title":"Analytics"},{"icon":"users","title":"Collaboration"},{"icon":"globe-alt","title":"Global CDN"},{"icon":"cog-6-tooth","title":"Automation"}]'>
        @dlItems('__SLUG__', 'items', $items, '[{"icon":"bolt","title":"Performance"},{"icon":"shield-check","title":"Security"},{"icon":"chart-bar","title":"Analytics"},{"icon":"users","title":"Collaboration"},{"icon":"globe-alt","title":"Global CDN"},{"icon":"cog-6-tooth","title":"Automation"}]')
        @foreach ($items as $item)
            <x-dl.card slug="__SLUG__" prefix="list_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="flex items-center gap-3 p-3 rounded-lg hover:bg-white dark:hover:bg-zinc-900 transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="item_icon" name="{{ $item['icon'] }}"
                    default-wrapper-classes="shrink-0 size-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary"
                    default-classes="size-4" />
                <x-dl.wrapper slug="__SLUG__" prefix="item_title" tag="span"
                    default-classes="font-medium text-zinc-700 dark:text-zinc-300 text-sm">
                    {{ $item['title'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
