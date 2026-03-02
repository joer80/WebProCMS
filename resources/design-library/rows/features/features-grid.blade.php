{{--
@name Features - Grid
@description Three-column feature grid with icons, headings, and descriptions.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-6xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        <div class="text-center mb-16">
            @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Everything You Need', 'text', 'headline'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white', 'classes', 'headline'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
            @if($showSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'Powerful features designed to help you succeed.', 'text', 'subheadline'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        @php $showFeatures = content('__SLUG__', 'show_features', '1', 'toggle', 'grid'); @endphp
        @php
            $featuresJson = content('__SLUG__', 'features', '[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Detailed Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Easy to Customize","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]', 'grid', 'grid');
            $features = json_decode($featuresJson, true) ?: [];
        @endphp
        @php $featuresGridClasses = content('__SLUG__', 'features_grid_classes', 'grid md:grid-cols-3 gap-8', 'classes', 'grid'); @endphp
        @php $featureCardClasses = content('__SLUG__', 'feature_card_classes', 'p-6 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors', 'classes', 'grid'); @endphp
        @php $iconWrapperClasses = content('__SLUG__', 'icon_wrapper_classes', 'mb-4 text-primary', 'classes', 'grid'); @endphp
        @php $iconSizeClasses = content('__SLUG__', 'icon_size_classes', 'size-8', 'classes', 'grid'); @endphp
        @php $featureTitleClasses = content('__SLUG__', 'feature_title_classes', 'text-lg font-semibold text-zinc-900 dark:text-white mb-2', 'classes', 'grid'); @endphp
        @php $featureDescClasses = content('__SLUG__', 'feature_desc_classes', 'text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed', 'classes', 'grid'); @endphp
        @if($showFeatures)
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
