{{--
@name Features - Grid
@description Three-column feature grid with icons, headings, and descriptions.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-6xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'text-center mb-16'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            @php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
            @if($toggleHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Everything You Need'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
            @if($toggleSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'Powerful features designed to help you succeed.'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        @php $toggleFeatures = content('__SLUG__', 'toggle_features', '1'); @endphp
        @php
            $featuresJson = content('__SLUG__', 'grid_features', '[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Detailed Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Easy to Customize","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]');
            $features = json_decode($featuresJson, true) ?: [];
        @endphp
        @php $featuresGridClasses = content('__SLUG__', 'features_grid_classes', 'grid md:grid-cols-3 gap-8'); @endphp
        @php $featureCardClasses = content('__SLUG__', 'feature_card_classes', 'p-6 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors'); @endphp
        @php $iconWrapperClasses = content('__SLUG__', 'icon_wrapper_classes', 'mb-4 text-primary'); @endphp
        @php $iconSizeClasses = content('__SLUG__', 'icon_size_classes', 'size-8'); @endphp
        @php $featureTitleClasses = content('__SLUG__', 'feature_title_classes', 'text-lg font-semibold text-zinc-900 dark:text-white mb-2'); @endphp
        @php $featureDescClasses = content('__SLUG__', 'feature_desc_classes', 'text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed'); @endphp
        @if($toggleFeatures)
        <div class="{{ $featuresGridClasses }}">
            @foreach ($features as $feature)
                <div class="{{ $featureCardClasses }}">
                    @php [$iconName, $iconVariant] = array_pad(explode(':', $feature['icon'] ?? 'bolt', 2), 2, 'outline'); @endphp
                    <div class="{{ $iconWrapperClasses }}">
                        <x-heroicon name="{{ $iconName }}" variant="{{ $iconVariant }}" class="{{ $iconSizeClasses }}" />
                    </div>
                    <h3 class="{{ $featureTitleClasses }}">{{ $feature['title'] }}</h3>
                    <p class="{{ $featureDescClasses }}">{{ $feature['desc'] }}</p>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
