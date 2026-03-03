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

{{-- ROW:start:pricing-cards:zV9Lnb --}}
@php $sectionClasses = content('pricing-cards:zV9Lnb', 'section_classes', 'py-section px-6 bg-zinc-50 dark:bg-zinc-950'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('pricing-cards:zV9Lnb', 'section_container_classes', 'max-w-5xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $headerWrapperClasses = content('pricing-cards:zV9Lnb', 'header_wrapper_classes', 'text-center mb-16'); @endphp
        @php $pricingGridClasses = content('pricing-cards:zV9Lnb', 'pricing_grid_classes', 'grid md:grid-cols-3 gap-8'); @endphp
        @php $cardClasses = content('pricing-cards:zV9Lnb', 'card_classes', 'rounded-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700'); @endphp
        @php $cardFeaturedClasses = content('pricing-cards:zV9Lnb', 'card_featured_classes', 'rounded-card p-8 bg-primary text-white ring-2 ring-primary'); @endphp
        @php $cardNameClasses = content('pricing-cards:zV9Lnb', 'card_name_classes', 'text-lg font-semibold text-zinc-900 dark:text-white'); @endphp
        @php $cardNameFeaturedClasses = content('pricing-cards:zV9Lnb', 'card_name_featured_classes', 'text-lg font-semibold text-white'); @endphp
        @php $cardDescClasses = content('pricing-cards:zV9Lnb', 'card_desc_classes', 'mt-1 text-sm text-zinc-500 dark:text-zinc-400'); @endphp
        @php $cardDescFeaturedClasses = content('pricing-cards:zV9Lnb', 'card_desc_featured_classes', 'mt-1 text-sm text-white/70'); @endphp
        @php $cardPriceClasses = content('pricing-cards:zV9Lnb', 'card_price_classes', 'mt-6 text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
        @php $cardPriceFeaturedClasses = content('pricing-cards:zV9Lnb', 'card_price_featured_classes', 'mt-6 text-4xl font-bold text-white'); @endphp
        @php $cardPricePeriodClasses = content('pricing-cards:zV9Lnb', 'card_price_period_classes', 'text-base font-normal text-zinc-400'); @endphp
        @php $cardPricePeriodFeaturedClasses = content('pricing-cards:zV9Lnb', 'card_price_period_featured_classes', 'text-base font-normal text-white/70'); @endphp
        @php $cardFeaturesListClasses = content('pricing-cards:zV9Lnb', 'card_features_list_classes', 'mt-6 space-y-3'); @endphp
        @php $cardFeatureItemClasses = content('pricing-cards:zV9Lnb', 'card_feature_item_classes', 'flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300'); @endphp
        @php $cardFeatureItemFeaturedClasses = content('pricing-cards:zV9Lnb', 'card_feature_item_featured_classes', 'flex items-center gap-2 text-sm text-white/90'); @endphp
        @php $cardFeatureIconClasses = content('pricing-cards:zV9Lnb', 'card_feature_icon_classes', 'size-4 shrink-0 text-primary'); @endphp
        @php $cardFeatureIconFeaturedClasses = content('pricing-cards:zV9Lnb', 'card_feature_icon_featured_classes', 'size-4 shrink-0 text-white'); @endphp
        @php $cardCtaClasses = content('pricing-cards:zV9Lnb', 'card_cta_classes', 'mt-8 block text-center px-4 py-3 rounded-lg font-semibold text-sm transition-colors bg-primary text-white hover:bg-primary/90'); @endphp
        @php $cardCtaFeaturedClasses = content('pricing-cards:zV9Lnb', 'card_cta_featured_classes', 'mt-8 block text-center px-4 py-3 rounded-lg font-semibold text-sm transition-colors bg-white text-primary hover:bg-zinc-100'); @endphp
        @php
            $plansJson = content('pricing-cards:zV9Lnb', 'grid_plans', '[{"name":"Starter","price":"$9","desc":"Perfect for individuals","features":"5 projects|10GB storage|Email support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Pro","price":"$29","desc":"Great for small teams","features":"Unlimited projects|100GB storage|Priority support|Analytics","cta":"Get Started","cta_url":"#","toggle_featured":"1"},{"name":"Enterprise","price":"$99","desc":"For large organizations","features":"Unlimited everything|Dedicated support|Custom integrations|SLA guarantee","cta":"Get Started","cta_url":"#","toggle_featured":""}]');
            $plans = json_decode($plansJson, true) ?: [];
        @endphp
        <div class="{{ $headerWrapperClasses }}">
            @php $toggleHeadline = content('pricing-cards:zV9Lnb', 'toggle_headline', '1'); @endphp
            @if($toggleHeadline)
            @php $headlineText = content('pricing-cards:zV9Lnb', 'headline', 'Simple, Transparent Pricing'); @endphp
            @php $headlineClasses = content('pricing-cards:zV9Lnb', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $toggleSubheadline = content('pricing-cards:zV9Lnb', 'toggle_subheadline', '1'); @endphp
            @if($toggleSubheadline)
            @php $subheadlineText = content('pricing-cards:zV9Lnb', 'subheadline', 'No hidden fees. Cancel anytime.'); @endphp
            @php $subheadlineClasses = content('pricing-cards:zV9Lnb', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        <div class="{{ $pricingGridClasses }}">
            @foreach ($plans as $plan)
                @php $isFeatured = !empty($plan['toggle_featured']); @endphp
                <div class="{{ $isFeatured ? $cardFeaturedClasses : $cardClasses }}">
                    <h3 class="{{ $isFeatured ? $cardNameFeaturedClasses : $cardNameClasses }}">{{ $plan['name'] }}</h3>
                    <p class="{{ $isFeatured ? $cardDescFeaturedClasses : $cardDescClasses }}">{{ $plan['desc'] }}</p>
                    <div class="{{ $isFeatured ? $cardPriceFeaturedClasses : $cardPriceClasses }}">
                        {{ $plan['price'] }}<span class="{{ $isFeatured ? $cardPricePeriodFeaturedClasses : $cardPricePeriodClasses }}">/mo</span>
                    </div>
                    <ul class="{{ $cardFeaturesListClasses }}">
                        @foreach (array_filter(array_map('trim', explode('|', $plan['features'] ?? ''))) as $feature)
                            <li class="{{ $isFeatured ? $cardFeatureItemFeaturedClasses : $cardFeatureItemClasses }}">
                                <svg class="{{ $isFeatured ? $cardFeatureIconFeaturedClasses : $cardFeatureIconClasses }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ $plan['cta_url'] ?? '#' }}" class="{{ $isFeatured ? $cardCtaFeaturedClasses : $cardCtaClasses }}">
                        {{ $plan['cta'] ?? 'Get Started' }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
{{-- ROW:end:pricing-cards:zV9Lnb --}}

{{-- ROW:start:cta-banner:S3575K:shared=1 --}}
@include('shared-rows.cta-banner-S3575K')
{{-- ROW:end:cta-banner:S3575K --}}
</div>
