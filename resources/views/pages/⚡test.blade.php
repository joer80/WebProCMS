<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>
<div>{{-- ROW:start:hero-dd317W --}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            @php $showHeadline = content('hero-dd317W', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('hero-dd317W', 'headline', 'Build Something Amazing', 'text', 'headline'); @endphp
            @php $headlineClasses = content('hero-dd317W', 'headline_classes', 'text-5xl font-bold text-zinc-900 dark:text-white leading-tight', 'classes', 'headline'); @endphp
            <h1 class="{{ $headlineClasses }}">{{ $headlineText }}</h1>
            @endif
            @php $showSubheadline = content('hero-dd317W', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
            @if($showSubheadline)
            @php $subheadlineText = content('hero-dd317W', 'subheadline', 'Describe your product or service here. Keep it concise and focused on the value you deliver to customers.', 'text', 'subheadline'); @endphp
            @php $subheadlineClasses = content('hero-dd317W', 'subheadline_classes', 'mt-6 text-lg text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
            <div class="mt-8 flex flex-wrap gap-4">
                @php $showPrimaryCta = content('hero-dd317W', 'show_primary_cta', '1', 'toggle', 'primary button'); @endphp
                @php $primaryCtaLabel = content('hero-dd317W', 'primary_cta', 'Start Free Trial', 'text', 'primary button'); @endphp
                @php $primaryCtaClasses = content('hero-dd317W', 'primary_cta_classes', 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors', 'classes', 'primary button'); @endphp
                @if($showPrimaryCta)
                <a
                    href="{{ content('hero-dd317W', 'primary_cta_url', '#', 'text', 'primary button') }}"
                    @if(content('hero-dd317W', 'primary_cta_new_tab', '', 'toggle', 'primary button')) target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $primaryCtaClasses }}"
                >{{ $primaryCtaLabel }}</a>
                @endif
                @if(content('hero-dd317W', 'show_secondary_cta', '1', 'toggle', 'secondary button'))
                @php $secondaryCtaLabel = content('hero-dd317W', 'secondary_cta', 'Watch Demo →', 'text', 'secondary button'); @endphp
                @php $secondaryCtaClasses = content('hero-dd317W', 'secondary_cta_classes', 'px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors', 'classes', 'secondary button'); @endphp
                <a
                    href="{{ content('hero-dd317W', 'secondary_cta_url', '#', 'text', 'secondary button') }}"
                    @if(content('hero-dd317W', 'secondary_cta_new_tab', '', 'toggle', 'secondary button')) target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $secondaryCtaClasses }}"
                >{{ $secondaryCtaLabel }}</a>
                @endif
            </div>
        </div>
        @if(content('hero-dd317W', 'show_image', '1', 'toggle', 'media'))
        <div class="rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center">
            @php $heroImage = content('hero-dd317W', 'image', '', 'image', 'media'); @endphp
            @if ($heroImage)
                <img src="{{ $heroImage }}" alt="{{ content('hero-dd317W', 'image_alt', '', 'text', 'media') }}" class="w-full h-full object-cover">
            @else
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
            @endif
        </div>
        @endif
    </div>
</section>
{{-- ROW:end:hero-dd317W --}}

{{-- ROW:start:cta-0khbdJ --}}
<section class="bg-primary py-16 px-6 text-center">
    <div class="max-w-3xl mx-auto">
        @php $showHeadline = content('cta-0khbdJ', 'show_headline', '1', 'toggle', 'headline'); @endphp
        @if($showHeadline)
        @php $headlineText = content('cta-0khbdJ', 'headline', 'Ready to Get Started?', 'text', 'headline'); @endphp
        @php $headlineClasses = content('cta-0khbdJ', 'headline_classes', 'text-4xl font-bold text-white', 'classes', 'headline'); @endphp
        <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
        @endif
        @php $showSubheadline = content('cta-0khbdJ', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
        @if($showSubheadline)
        @php $subheadlineText = content('cta-0khbdJ', 'subheadline', 'Join thousands of satisfied customers today.', 'text', 'subheadline'); @endphp
        @php $subheadlineClasses = content('cta-0khbdJ', 'subheadline_classes', 'mt-4 text-lg text-white/80', 'classes', 'subheadline'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            @php $showPrimaryCta = content('cta-0khbdJ', 'show_primary_cta', '1', 'toggle', 'primary button'); @endphp
            @php $primaryCtaLabel = content('cta-0khbdJ', 'primary_cta', 'Start Free Trial', 'text', 'primary button'); @endphp
            @php $primaryCtaClasses = content('cta-0khbdJ', 'primary_cta_classes', 'px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors', 'classes', 'primary button'); @endphp
            @if($showPrimaryCta)
            <a
                href="{{ content('cta-0khbdJ', 'primary_cta_url', '#', 'text', 'primary button') }}"
                @if(content('cta-0khbdJ', 'primary_cta_new_tab', '', 'toggle', 'primary button')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $primaryCtaClasses }}"
            >{{ $primaryCtaLabel }}</a>
            @endif
            @if(content('cta-0khbdJ', 'show_secondary_cta', '1', 'toggle', 'secondary button'))
            @php $secondaryCtaLabel = content('cta-0khbdJ', 'secondary_cta', 'Talk to Sales', 'text', 'secondary button'); @endphp
            @php $secondaryCtaClasses = content('cta-0khbdJ', 'secondary_cta_classes', 'px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors', 'classes', 'secondary button'); @endphp
            <a
                href="{{ content('cta-0khbdJ', 'secondary_cta_url', '#', 'text', 'secondary button') }}"
                @if(content('cta-0khbdJ', 'secondary_cta_new_tab', '', 'toggle', 'secondary button')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $secondaryCtaClasses }}"
            >{{ $secondaryCtaLabel }}</a>
            @endif
        </div>
    </div>
</section>
{{-- ROW:end:cta-0khbdJ --}}
</div>
