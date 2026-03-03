{{--
@name Features - Grid
@description Three-column feature grid with icons, headings, and descriptions.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Everything You Need"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Powerful features designed to help you succeed."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="features"
        default-grid-classes="grid md:grid-cols-3 gap-8"
        default-items='[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Detailed Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Easy to Customize","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]'>
        @dlItems('__SLUG__', 'features', $features, '[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Detailed Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Easy to Customize","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]')
        @foreach ($features as $feature)
            <x-dl.card slug="__SLUG__" prefix="feature_card"
                default-classes="p-6 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="icon" name="{{ $feature['icon'] }}"
                    default-wrapper-classes="mb-4 text-primary"
                    default-classes="size-8" />
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
