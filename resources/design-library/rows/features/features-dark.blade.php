{{--
@name Features - Dark Grid
@description Dark background three-column feature grid with glowing icon accents.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Powerful by Default"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Everything you need, right out of the box."
            default-classes="mt-4 text-lg text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="features"
        default-grid-classes="grid md:grid-cols-3 gap-8"
        default-items='[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Deep Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Fully Customizable","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]'>
        @dlItems('__SLUG__', 'features', $features, '[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Deep Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Fully Customizable","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]')
        @foreach ($features as $feature)
            <x-dl.card slug="__SLUG__" prefix="feature_card"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="p-6 rounded-card bg-zinc-800 border border-zinc-700 hover:border-primary/40 transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="icon" name="{{ $feature['icon'] }}"
                    default-wrapper-classes="mb-4 size-10 rounded-lg bg-primary/20 flex items-center justify-center text-primary"
                    default-classes="size-5" />
                <x-dl.wrapper slug="__SLUG__" prefix="feature_title" tag="h3"
                    default-classes="text-lg font-semibold text-white mb-2">
                    {{ $feature['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="feature_desc" tag="p"
                    default-classes="text-zinc-400 text-sm leading-relaxed">
                    {{ $feature['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
