{{--
@name Features - Centered Cards
@description Centered three-column feature cards with icon above and centered text.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Why Choose Us"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="We've built a platform that puts your success first."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="features"
        default-grid-classes="grid md:grid-cols-3 gap-8"
        default-items='[{"icon":"bolt","title":"Blazing Fast","desc":"Industry-leading performance so you never miss a beat."},{"icon":"shield-check","title":"Fort Knox Security","desc":"Your data is always protected with military-grade encryption."},{"icon":"chart-bar","title":"Smart Analytics","desc":"Turn raw data into clear, actionable business insights."},{"icon":"users","title":"Team-First Design","desc":"Built with collaboration at its core from the ground up."},{"icon":"globe-alt","title":"Works Anywhere","desc":"Access from any device, any browser, anywhere in the world."},{"icon":"heart","title":"Award-Winning Support","desc":"Our customers rate us 4.9/5 for support and satisfaction."}]'>
        @dlItems('__SLUG__', 'features', $features, '[{"icon":"bolt","title":"Blazing Fast","desc":"Industry-leading performance so you never miss a beat."},{"icon":"shield-check","title":"Fort Knox Security","desc":"Your data is always protected with military-grade encryption."},{"icon":"chart-bar","title":"Smart Analytics","desc":"Turn raw data into clear, actionable business insights."},{"icon":"users","title":"Team-First Design","desc":"Built with collaboration at its core from the ground up."},{"icon":"globe-alt","title":"Works Anywhere","desc":"Access from any device, any browser, anywhere in the world."},{"icon":"heart","title":"Award-Winning Support","desc":"Our customers rate us 4.9/5 for support and satisfaction."}]')
        @foreach ($features as $feature)
            <x-dl.card slug="__SLUG__" prefix="feature_card"
                default-classes="p-8 rounded-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-card text-center">
                <x-dl.icon slug="__SLUG__" prefix="icon" name="{{ $feature['icon'] }}"
                    default-wrapper-classes="mx-auto mb-5 size-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary"
                    default-classes="size-7" />
                <x-dl.wrapper slug="__SLUG__" prefix="feature_title" tag="h3"
                    default-classes="text-lg font-semibold text-zinc-900 dark:text-white mb-2">
                    {{ $feature['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="feature_desc" tag="p"
                    default-classes="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">
                    {{ $feature['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
