{{--
@name Pricing - Cards
@description Three-tier pricing cards with features list and CTA buttons.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-zinc-50 dark:bg-zinc-950'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-5xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'text-center mb-16'); @endphp
        @php $pricingGridClasses = content('__SLUG__', 'pricing_grid_classes', 'grid md:grid-cols-3 gap-8'); @endphp
        @php $cardClasses = content('__SLUG__', 'card_classes', 'rounded-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700'); @endphp
        @php $cardFeaturedClasses = content('__SLUG__', 'card_featured_classes', 'rounded-card p-8 bg-primary text-white ring-2 ring-primary'); @endphp
        @php $cardNameClasses = content('__SLUG__', 'card_name_classes', 'text-lg font-semibold text-zinc-900 dark:text-white'); @endphp
        @php $cardNameFeaturedClasses = content('__SLUG__', 'card_name_featured_classes', 'text-lg font-semibold text-white'); @endphp
        @php $cardDescClasses = content('__SLUG__', 'card_desc_classes', 'mt-1 text-sm text-zinc-500 dark:text-zinc-400'); @endphp
        @php $cardDescFeaturedClasses = content('__SLUG__', 'card_desc_featured_classes', 'mt-1 text-sm text-white/70'); @endphp
        @php $cardPriceClasses = content('__SLUG__', 'card_price_classes', 'mt-6 text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
        @php $cardPriceFeaturedClasses = content('__SLUG__', 'card_price_featured_classes', 'mt-6 text-4xl font-bold text-white'); @endphp
        @php $cardPricePeriodClasses = content('__SLUG__', 'card_price_period_classes', 'text-base font-normal text-zinc-400'); @endphp
        @php $cardPricePeriodFeaturedClasses = content('__SLUG__', 'card_price_period_featured_classes', 'text-base font-normal text-white/70'); @endphp
        @php $cardFeaturesListClasses = content('__SLUG__', 'card_features_list_classes', 'mt-6 space-y-3'); @endphp
        @php $cardFeatureItemClasses = content('__SLUG__', 'card_feature_item_classes', 'flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300'); @endphp
        @php $cardFeatureItemFeaturedClasses = content('__SLUG__', 'card_feature_item_featured_classes', 'flex items-center gap-2 text-sm text-white/90'); @endphp
        @php $cardFeatureIconClasses = content('__SLUG__', 'card_feature_icon_classes', 'size-4 shrink-0 text-primary'); @endphp
        @php $cardFeatureIconFeaturedClasses = content('__SLUG__', 'card_feature_icon_featured_classes', 'size-4 shrink-0 text-white'); @endphp
        @php $cardCtaClasses = content('__SLUG__', 'card_cta_classes', 'mt-8 block text-center px-4 py-3 rounded-lg font-semibold text-sm transition-colors bg-primary text-white hover:bg-primary/90'); @endphp
        @php $cardCtaFeaturedClasses = content('__SLUG__', 'card_cta_featured_classes', 'mt-8 block text-center px-4 py-3 rounded-lg font-semibold text-sm transition-colors bg-white text-primary hover:bg-zinc-100'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            @php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
            @if($toggleHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Simple, Transparent Pricing'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
            @if($toggleSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'No hidden fees. Cancel anytime.'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        @php
            $plansJson = content('__SLUG__', 'grid_plans', '[{"name":"Starter","price":"$9","desc":"Perfect for individuals","features":"5 projects|10GB storage|Email support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Pro","price":"$29","desc":"Great for small teams","features":"Unlimited projects|100GB storage|Priority support|Analytics","cta":"Get Started","cta_url":"#","toggle_featured":"1"},{"name":"Enterprise","price":"$99","desc":"For large organizations","features":"Unlimited everything|Dedicated support|Custom integrations|SLA guarantee","cta":"Get Started","cta_url":"#","toggle_featured":""}]');
            $plans = json_decode($plansJson, true) ?: [];
        @endphp
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
