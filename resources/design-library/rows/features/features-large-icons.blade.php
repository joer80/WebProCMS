{{--
@name Features - Large Icons
@description Two-column feature grid with extra-large icons and bold descriptions.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-800/50"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Feature-Rich by Design"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Every feature built with purpose to help you achieve more."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="features"
        default-grid-classes="grid md:grid-cols-2 gap-10"
        default-items='[{"icon":"bolt","title":"Performance First","desc":"Sub-100ms response times across the globe with our edge network."},{"icon":"shield-check","title":"Zero-Trust Security","desc":"Every request verified, every action audited, every data point encrypted."},{"icon":"chart-bar","title":"Business Intelligence","desc":"360-degree visibility into operations, revenue, and customer health."},{"icon":"cog-6-tooth","title":"Infinite Flexibility","desc":"Hundreds of integrations and an open API for custom workflows."}]'>
        @dlItems('__SLUG__', 'features', $features, '[{"icon":"bolt","title":"Performance First","desc":"Sub-100ms response times across the globe with our edge network."},{"icon":"shield-check","title":"Zero-Trust Security","desc":"Every request verified, every action audited, every data point encrypted."},{"icon":"chart-bar","title":"Business Intelligence","desc":"360-degree visibility into operations, revenue, and customer health."},{"icon":"cog-6-tooth","title":"Infinite Flexibility","desc":"Hundreds of integrations and an open API for custom workflows."}]')
        @foreach ($features as $feature)
            <x-dl.card slug="__SLUG__" prefix="feature_card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="flex items-start gap-6">
                <x-dl.icon slug="__SLUG__" prefix="icon" name="{{ $feature['icon'] }}"
                    default-wrapper-classes="shrink-0 size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary"
                    default-classes="size-8" />
                <div>
                    <x-dl.wrapper slug="__SLUG__" prefix="feature_title" tag="h3"
                        default-classes="text-xl font-bold text-zinc-900 dark:text-white mb-2">
                        {{ $feature['title'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="feature_desc" tag="p"
                        default-classes="text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ $feature['desc'] }}
                    </x-dl.wrapper>
                </div>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
