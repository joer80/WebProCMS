<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>
<div>{{-- ROW:start:hero-split:oE5Mc1 --}}
@php $sectionClasses = content('hero-split:oE5Mc1', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('hero-split:oE5Mc1', 'section_container_classes', 'max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        <div>
            @php $toggleHeadline = content('hero-split:oE5Mc1', 'toggle_headline', '1'); @endphp
            @if($toggleHeadline)
            @php $headlineText = content('hero-split:oE5Mc1', 'headline', 'Build Something Amazing'); @endphp
            @php $headlineClasses = content('hero-split:oE5Mc1', 'headline_classes', 'font-heading text-5xl font-bold text-zinc-900 dark:text-white leading-tight'); @endphp
            <h1 class="{{ $headlineClasses }}">{{ $headlineText }}</h1>
            @endif
            @php $toggleSubheadline = content('hero-split:oE5Mc1', 'toggle_subheadline', '1'); @endphp
            @if($toggleSubheadline)
            @php $subheadlineText = content('hero-split:oE5Mc1', 'subheadline', 'Describe your product or service here. Keep it concise and focused on the value you deliver to customers.'); @endphp
            @php $subheadlineClasses = content('hero-split:oE5Mc1', 'subheadline_classes', 'mt-6 text-lg text-zinc-500 dark:text-zinc-400'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
            @php $buttonsWrapperClasses = content('hero-split:oE5Mc1', 'buttons_wrapper_classes', 'mt-8 flex flex-wrap gap-4'); @endphp
            <div class="{{ $buttonsWrapperClasses }}">
                @php $togglePrimaryCta = content('hero-split:oE5Mc1', 'toggle_primary_cta', '1'); @endphp
                @php $primaryCtaLabel = content('hero-split:oE5Mc1', 'primary_cta', 'Start Free Trial'); @endphp
                @php $primaryCtaClasses = content('hero-split:oE5Mc1', 'primary_cta_classes', 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'); @endphp
                @if($togglePrimaryCta)
                <a
                    href="{{ content('hero-split:oE5Mc1', 'primary_cta_url', '#') }}"
                    @if(content('hero-split:oE5Mc1', 'primary_cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $primaryCtaClasses }}"
                >{{ $primaryCtaLabel }}</a>
                @endif
                @if(content('hero-split:oE5Mc1', 'toggle_secondary_cta', '1'))
                @php $secondaryCtaLabel = content('hero-split:oE5Mc1', 'secondary_cta', 'Watch Demo →'); @endphp
                @php $secondaryCtaClasses = content('hero-split:oE5Mc1', 'secondary_cta_classes', 'px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors'); @endphp
                <a
                    href="{{ content('hero-split:oE5Mc1', 'secondary_cta_url', '#') }}"
                    @if(content('hero-split:oE5Mc1', 'secondary_cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $secondaryCtaClasses }}"
                >{{ $secondaryCtaLabel }}</a>
                @endif
            </div>
        </div>
        @php $imageWrapperClasses = content('hero-split:oE5Mc1', 'image_wrapper_classes', 'rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center'); @endphp
        @php $imageClasses = content('hero-split:oE5Mc1', 'image_classes', 'w-full h-full object-cover'); @endphp
        @if(content('hero-split:oE5Mc1', 'toggle_image', '1'))
        <div class="{{ $imageWrapperClasses }}">
            @php $heroImage = content('hero-split:oE5Mc1', 'image', ''); @endphp
            @if ($heroImage)
                <img src="{{ $heroImage }}" alt="{{ content('hero-split:oE5Mc1', 'image_alt', '') }}" class="{{ $imageClasses }}">
            @else
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
            @endif
        </div>
        @endif
    </div>
</section>
{{-- ROW:end:hero-split:oE5Mc1 --}}

{{-- ROW:start:features-grid:yBNWwy --}}
@php $sectionClasses = content('features-grid:yBNWwy', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('features-grid:yBNWwy', 'section_container_classes', 'max-w-6xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        <div class="text-center mb-16">
            @php $toggleHeadline = content('features-grid:yBNWwy', 'toggle_headline', '1'); @endphp
            @if($toggleHeadline)
            @php $headlineText = content('features-grid:yBNWwy', 'headline', 'Everything You Need'); @endphp
            @php $headlineClasses = content('features-grid:yBNWwy', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $toggleSubheadline = content('features-grid:yBNWwy', 'toggle_subheadline', '1'); @endphp
            @if($toggleSubheadline)
            @php $subheadlineText = content('features-grid:yBNWwy', 'subheadline', 'Powerful features designed to help you succeed.'); @endphp
            @php $subheadlineClasses = content('features-grid:yBNWwy', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        @php $toggleFeatures = content('features-grid:yBNWwy', 'toggle_features', '1'); @endphp
        @php
            $featuresJson = content('features-grid:yBNWwy', 'grid_features', '[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Detailed Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Easy to Customize","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]');
            $features = json_decode($featuresJson, true) ?: [];
        @endphp
        @php $featuresGridClasses = content('features-grid:yBNWwy', 'features_grid_classes', 'grid md:grid-cols-3 gap-8'); @endphp
        @php $featureCardClasses = content('features-grid:yBNWwy', 'feature_card_classes', 'p-6 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors'); @endphp
        @php $iconWrapperClasses = content('features-grid:yBNWwy', 'icon_wrapper_classes', 'mb-4 text-primary'); @endphp
        @php $iconSizeClasses = content('features-grid:yBNWwy', 'icon_size_classes', 'size-8'); @endphp
        @php $featureTitleClasses = content('features-grid:yBNWwy', 'feature_title_classes', 'text-lg font-semibold text-zinc-900 dark:text-white mb-2'); @endphp
        @php $featureDescClasses = content('features-grid:yBNWwy', 'feature_desc_classes', 'text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed'); @endphp
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
{{-- ROW:end:features-grid:yBNWwy --}}
</div>
