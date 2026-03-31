{{--
@name Features - List
@description Vertical list of features with icon, title, and description.
@sort 20
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Built for Modern Teams"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Everything you need to collaborate, ship, and grow — all in one place."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-8 flex flex-wrap gap-4"
            default-primary-label="Get Started"
            default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="Learn More"
            default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
    </div>
    <x-dl.grid slug="__SLUG__" prefix="features"
        default-grid-classes="space-y-6"
        default-items='[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack so your team never waits."},{"icon":"shield-check","title":"Enterprise Security","desc":"SOC 2 certified with end-to-end encryption and fine-grained permissions."},{"icon":"chart-bar","title":"Rich Analytics","desc":"Dashboards and reports that give you insight into every corner of your business."},{"icon":"cog-6-tooth","title":"Highly Configurable","desc":"Adapt the platform to your exact workflow with powerful customization options."}]'>
        @dlItems('__SLUG__', 'features', $features, '[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack so your team never waits."},{"icon":"shield-check","title":"Enterprise Security","desc":"SOC 2 certified with end-to-end encryption and fine-grained permissions."},{"icon":"chart-bar","title":"Rich Analytics","desc":"Dashboards and reports that give you insight into every corner of your business."},{"icon":"cog-6-tooth","title":"Highly Configurable","desc":"Adapt the platform to your exact workflow with powerful customization options."}]')
        @foreach ($features as $feature)
            <x-dl.card slug="__SLUG__" prefix="feature_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="flex items-start gap-4">
                <x-dl.icon slug="__SLUG__" prefix="icon" name="{{ $feature['icon'] }}"
                    default-wrapper-classes="mt-0.5 shrink-0 size-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary"
                    default-classes="size-5" />
                <div>
                    <x-dl.wrapper slug="__SLUG__" prefix="feature_title" tag="h3"
                        default-classes="font-semibold text-zinc-900 dark:text-white">
                        {{ $feature['title'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="feature_desc" tag="p"
                        default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ $feature['desc'] }}
                    </x-dl.wrapper>
                </div>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
