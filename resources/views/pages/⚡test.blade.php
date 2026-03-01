<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>
<div>{{-- ROW:start:hero-0PcmrC --}}
@php $sectionClasses = content('hero-0PcmrC', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('hero-0PcmrC', 'container_classes', 'max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        <div>
            @php $showHeadline = content('hero-0PcmrC', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('hero-0PcmrC', 'headline', 'Build Something Amazing', 'text', 'headline'); @endphp
            @php $headlineClasses = content('hero-0PcmrC', 'headline_classes', 'font-heading text-5xl font-bold text-zinc-900 dark:text-white leading-tight', 'classes', 'headline'); @endphp
            <h1 class="{{ $headlineClasses }}">{{ $headlineText }}</h1>
            @endif
            @php $showSubheadline = content('hero-0PcmrC', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
            @if($showSubheadline)
            @php $subheadlineText = content('hero-0PcmrC', 'subheadline', 'Describe your product or service here. Keep it concise and focused on the value you deliver to customers.', 'text', 'subheadline'); @endphp
            @php $subheadlineClasses = content('hero-0PcmrC', 'subheadline_classes', 'mt-6 text-lg text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
            <div class="mt-8 flex flex-wrap gap-4">
                @php $showPrimaryCta = content('hero-0PcmrC', 'show_primary_cta', '1', 'toggle', 'primary button'); @endphp
                @php $primaryCtaLabel = content('hero-0PcmrC', 'primary_cta', 'Start Free Trial', 'text', 'primary button'); @endphp
                @php $primaryCtaClasses = content('hero-0PcmrC', 'primary_cta_classes', 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors', 'classes', 'primary button'); @endphp
                @if($showPrimaryCta)
                <a
                    href="{{ content('hero-0PcmrC', 'primary_cta_url', '#', 'text', 'primary button') }}"
                    @if(content('hero-0PcmrC', 'primary_cta_new_tab', '', 'toggle', 'primary button')) target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $primaryCtaClasses }}"
                >{{ $primaryCtaLabel }}</a>
                @endif
                @if(content('hero-0PcmrC', 'show_secondary_cta', '1', 'toggle', 'secondary button'))
                @php $secondaryCtaLabel = content('hero-0PcmrC', 'secondary_cta', 'Watch Demo →', 'text', 'secondary button'); @endphp
                @php $secondaryCtaClasses = content('hero-0PcmrC', 'secondary_cta_classes', 'px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors', 'classes', 'secondary button'); @endphp
                <a
                    href="{{ content('hero-0PcmrC', 'secondary_cta_url', '#', 'text', 'secondary button') }}"
                    @if(content('hero-0PcmrC', 'secondary_cta_new_tab', '', 'toggle', 'secondary button')) target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $secondaryCtaClasses }}"
                >{{ $secondaryCtaLabel }}</a>
                @endif
            </div>
        </div>
        @if(content('hero-0PcmrC', 'show_image', '1', 'toggle', 'media'))
        <div class="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center">
            @php $heroImage = content('hero-0PcmrC', 'image', '', 'image', 'media'); @endphp
            @if ($heroImage)
                <img src="{{ $heroImage }}" alt="{{ content('hero-0PcmrC', 'image_alt', '', 'text', 'media') }}" class="w-full h-full object-cover">
            @else
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
            @endif
        </div>
        @endif
    </div>
</section>
{{-- ROW:end:hero-0PcmrC --}}

{{-- ROW:start:features-P15otx --}}
@php $sectionClasses = content('features-P15otx', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('features-P15otx', 'container_classes', 'max-w-6xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        <div class="text-center mb-16">
            @php $showHeadline = content('features-P15otx', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('features-P15otx', 'headline', 'Everything You Need', 'text', 'headline'); @endphp
            @php $headlineClasses = content('features-P15otx', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white', 'classes', 'headline'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $showSubheadline = content('features-P15otx', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
            @if($showSubheadline)
            @php $subheadlineText = content('features-P15otx', 'subheadline', 'Powerful features designed to help you succeed.', 'text', 'subheadline'); @endphp
            @php $subheadlineClasses = content('features-P15otx', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        @php $showFeatures = content('features-P15otx', 'show_features', '1', 'toggle', 'grid'); @endphp
        @php
            $featuresJson = content('features-P15otx', 'features', '[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Detailed Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Easy to Customize","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]', 'grid', 'grid');
            $features = json_decode($featuresJson, true) ?: [];
        @endphp
        @php $featuresGridClasses = content('features-P15otx', 'features_grid_classes', 'grid md:grid-cols-3 gap-8', 'classes', 'grid'); @endphp
        @if($showFeatures)
        <div class="{{ $featuresGridClasses }}">
            @foreach ($features as $feature)
                <div class="p-6 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors">
                    @php [$iconName, $iconVariant] = array_pad(explode(':', $feature['icon'] ?? 'bolt', 2), 2, 'outline'); @endphp
                    <div class="mb-4 text-primary">
                        <x-heroicon name="{{ $iconName }}" variant="{{ $iconVariant }}" class="size-8" />
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
{{-- ROW:end:features-P15otx --}}

{{-- ROW:start:cta-8pPODC --}}
@php $sectionClasses = content('cta-8pPODC', 'section_classes', 'bg-primary py-section px-6 text-center', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('cta-8pPODC', 'container_classes', 'max-w-3xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        @php $showHeadline = content('cta-8pPODC', 'show_headline', '1', 'toggle', 'headline'); @endphp
        @if($showHeadline)
        @php $headlineText = content('cta-8pPODC', 'headline', 'Ready to Get Started?', 'text', 'headline'); @endphp
        @php $headlineClasses = content('cta-8pPODC', 'headline_classes', 'font-heading text-4xl font-bold text-white', 'classes', 'headline'); @endphp
        <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
        @endif
        @php $showSubheadline = content('cta-8pPODC', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
        @if($showSubheadline)
        @php $subheadlineText = content('cta-8pPODC', 'subheadline', 'Join thousands of satisfied customers today.', 'text', 'subheadline'); @endphp
        @php $subheadlineClasses = content('cta-8pPODC', 'subheadline_classes', 'mt-4 text-lg text-white/80', 'classes', 'subheadline'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            @php $showPrimaryCta = content('cta-8pPODC', 'show_primary_cta', '1', 'toggle', 'primary button'); @endphp
            @php $primaryCtaLabel = content('cta-8pPODC', 'primary_cta', 'Start Free Trial', 'text', 'primary button'); @endphp
            @php $primaryCtaClasses = content('cta-8pPODC', 'primary_cta_classes', 'px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors', 'classes', 'primary button'); @endphp
            @if($showPrimaryCta)
            <a
                href="{{ content('cta-8pPODC', 'primary_cta_url', '#', 'text', 'primary button') }}"
                @if(content('cta-8pPODC', 'primary_cta_new_tab', '', 'toggle', 'primary button')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $primaryCtaClasses }}"
            >{{ $primaryCtaLabel }}</a>
            @endif
            @if(content('cta-8pPODC', 'show_secondary_cta', '1', 'toggle', 'secondary button'))
            @php $secondaryCtaLabel = content('cta-8pPODC', 'secondary_cta', 'Talk to Sales', 'text', 'secondary button'); @endphp
            @php $secondaryCtaClasses = content('cta-8pPODC', 'secondary_cta_classes', 'px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors', 'classes', 'secondary button'); @endphp
            <a
                href="{{ content('cta-8pPODC', 'secondary_cta_url', '#', 'text', 'secondary button') }}"
                @if(content('cta-8pPODC', 'secondary_cta_new_tab', '', 'toggle', 'secondary button')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $secondaryCtaClasses }}"
            >{{ $secondaryCtaLabel }}</a>
            @endif
        </div>
    </div>
</section>
{{-- ROW:end:cta-8pPODC --}}
</div>
