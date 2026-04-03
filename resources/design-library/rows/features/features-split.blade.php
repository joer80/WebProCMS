{{--
@name Features - Split
@description Two-column layout with large icon on the left and feature text on the right.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-container mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Designed to Scale With You"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="From startup to enterprise — our platform grows as you do."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="features"
        default-grid-classes="grid md:grid-cols-2 gap-6"
        default-items='[{"icon":"bolt","title":"Speed Without Compromise","desc":"Our global CDN and edge computing infrastructure deliver sub-100ms load times everywhere."},{"icon":"shield-check","title":"Security at Every Layer","desc":"End-to-end encryption, 2FA, SSO, and role-based access control right out of the box."},{"icon":"chart-bar","title":"Actionable Insights","desc":"Beautifully designed dashboards that surface the metrics that matter most to your business."},{"icon":"cog-6-tooth","title":"Seamless Integrations","desc":"Connect to 200+ tools your team already uses with one-click native integrations."}]'>
        @dlItems('__SLUG__', 'features', $features, '[{"icon":"bolt","title":"Speed Without Compromise","desc":"Our global CDN and edge computing infrastructure deliver sub-100ms load times everywhere."},{"icon":"shield-check","title":"Security at Every Layer","desc":"End-to-end encryption, 2FA, SSO, and role-based access control right out of the box."},{"icon":"chart-bar","title":"Actionable Insights","desc":"Beautifully designed dashboards that surface the metrics that matter most to your business."},{"icon":"cog-6-tooth","title":"Seamless Integrations","desc":"Connect to 200+ tools your team already uses with one-click native integrations."}]')
        @foreach ($features as $feature)
            <x-dl.card slug="__SLUG__" prefix="feature_card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="p-6 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors flex items-start gap-5">
                <x-dl.icon slug="__SLUG__" prefix="icon" name="{{ $feature['icon'] }}"
                    default-wrapper-classes="shrink-0 size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary"
                    default-classes="size-6" />
                <div>
                    <x-dl.wrapper slug="__SLUG__" prefix="feature_title" tag="h3"
                        default-classes="font-semibold text-zinc-900 dark:text-white mb-2">
                        {{ $feature['title'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="feature_desc" tag="p"
                        default-classes="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ $feature['desc'] }}
                    </x-dl.wrapper>
                </div>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
