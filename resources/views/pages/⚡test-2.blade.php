<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test 2')] class extends Component {
}; ?>
<div>{{-- ROW:start:hero-centered:gGzAZj --}}
@php $sectionClasses = content('hero-centered:gGzAZj', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900 text-center'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('hero-centered:gGzAZj', 'section_container_classes', 'max-w-3xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $toggleBadge = content('hero-centered:gGzAZj', 'toggle_badge', '1'); @endphp
        @php $badgeClasses = content('hero-centered:gGzAZj', 'badge_classes', 'inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6'); @endphp
        @if($toggleBadge)
        <span class="{{ $badgeClasses }}">{{ content('hero-centered:gGzAZj', 'badge', 'Welcome') }}</span>
        @endif
        @php $toggleHeadline = content('hero-centered:gGzAZj', 'toggle_headline', '1'); @endphp
        @if($toggleHeadline)
        @php $headlineText = content('hero-centered:gGzAZj', 'headline', 'Your Headline Goes Here'); @endphp
        @php $headlineClasses = content('hero-centered:gGzAZj', 'headline_classes', 'font-heading text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight'); @endphp
        <h1 class="{{ $headlineClasses }}">{{ $headlineText }}</h1>
        @endif
        @php $toggleSubheadline = content('hero-centered:gGzAZj', 'toggle_subheadline', '1'); @endphp
        @if($toggleSubheadline)
        @php $subheadlineText = content('hero-centered:gGzAZj', 'subheadline', 'A compelling subheadline that explains what you do and why it matters to your audience.'); @endphp
        @php $subheadlineClasses = content('hero-centered:gGzAZj', 'subheadline_classes', 'mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        @php $buttonsWrapperClasses = content('hero-centered:gGzAZj', 'buttons_wrapper_classes', 'mt-10 flex flex-wrap items-center justify-center gap-4'); @endphp
        <div class="{{ $buttonsWrapperClasses }}">
            @php $togglePrimaryCta = content('hero-centered:gGzAZj', 'toggle_primary_cta', '1'); @endphp
            @php $primaryCtaLabel = content('hero-centered:gGzAZj', 'primary_cta', 'Get Started'); @endphp
            @php $primaryCtaClasses = content('hero-centered:gGzAZj', 'primary_cta_classes', 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'); @endphp
            @if($togglePrimaryCta)
            <a
                href="{{ content('hero-centered:gGzAZj', 'primary_cta_url', '#') }}"
                @if(content('hero-centered:gGzAZj', 'primary_cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $primaryCtaClasses }}"
            >{{ $primaryCtaLabel }}</a>
            @endif
            @if(content('hero-centered:gGzAZj', 'toggle_secondary_cta', '1'))
            @php $secondaryCtaLabel = content('hero-centered:gGzAZj', 'secondary_cta', 'Learn More'); @endphp
            @php $secondaryCtaClasses = content('hero-centered:gGzAZj', 'secondary_cta_classes', 'px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors'); @endphp
            <a
                href="{{ content('hero-centered:gGzAZj', 'secondary_cta_url', '#') }}"
                @if(content('hero-centered:gGzAZj', 'secondary_cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $secondaryCtaClasses }}"
            >{{ $secondaryCtaLabel }}</a>
            @endif
        </div>
    </div>
</section>
{{-- ROW:end:hero-centered:gGzAZj --}}

{{-- ROW:start:content-two-column:o1x1EC --}}
@php $sectionClasses = content('content-two-column:o1x1EC', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('content-two-column:o1x1EC', 'section_container_classes', 'max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        <div>
            @php $badgeClasses = content('content-two-column:o1x1EC', 'badge_classes', 'text-sm font-semibold text-primary uppercase tracking-wider'); @endphp
            @php $imageWrapperClasses = content('content-two-column:o1x1EC', 'image_wrapper_classes', 'rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square flex items-center justify-center'); @endphp
            @php $imageClasses = content('content-two-column:o1x1EC', 'image_classes', 'w-full h-full object-cover'); @endphp
            <span class="{{ $badgeClasses }}">{{ content('content-two-column:o1x1EC', 'badge', 'Our Story') }}</span>
            @php $toggleHeadline = content('content-two-column:o1x1EC', 'toggle_headline', '1'); @endphp
            @if($toggleHeadline)
            @php $headlineText = content('content-two-column:o1x1EC', 'headline', 'We Are Building the Future of Work'); @endphp
            @php $headlineClasses = content('content-two-column:o1x1EC', 'headline_classes', 'font-heading mt-3 text-4xl font-bold text-zinc-900 dark:text-white leading-tight'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $bodyClasses = content('content-two-column:o1x1EC', 'body_classes', 'mt-6 text-zinc-500 dark:text-zinc-400 leading-relaxed'); @endphp
            <p class="{{ $bodyClasses }}">
                {{ content('content-two-column:o1x1EC', 'body', 'Founded in 2020, we have been on a mission to help teams collaborate more effectively. Our platform combines the best of communication, project management, and automation into one seamless experience.') }}
            </p>
            @php $bodySecondaryClasses = content('content-two-column:o1x1EC', 'body_secondary_classes', 'mt-4 text-zinc-500 dark:text-zinc-400 leading-relaxed'); @endphp
            <p class="{{ $bodySecondaryClasses }}">
                {{ content('content-two-column:o1x1EC', 'body_secondary', 'Today, we are trusted by over 10,000 companies worldwide, from startups to Fortune 500 enterprises.') }}
            </p>
            @php $toggleCta = content('content-two-column:o1x1EC', 'toggle_cta', '1'); @endphp
            @php $ctaLabel = content('content-two-column:o1x1EC', 'cta_label', 'Learn more about us'); @endphp
            @php $ctaClasses = content('content-two-column:o1x1EC', 'cta_classes', 'mt-8 inline-flex items-center text-primary font-semibold hover:text-primary/80 transition-colors'); @endphp
            @if($toggleCta)
            <a
                href="{{ content('content-two-column:o1x1EC', 'cta_url', '#') }}"
                @if(content('content-two-column:o1x1EC', 'cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $ctaClasses }}"
            >
                {{ $ctaLabel }} →
            </a>
            @endif
        </div>
        @if(content('content-two-column:o1x1EC', 'toggle_image', '1'))
        <div class="{{ $imageWrapperClasses }}">
            @php $sectionImage = content('content-two-column:o1x1EC', 'image', ''); @endphp
            @if ($sectionImage)
                <img src="{{ $sectionImage }}" alt="{{ content('content-two-column:o1x1EC', 'image_alt', '') }}" class="{{ $imageClasses }}">
            @else
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image Placeholder</span>
            @endif
        </div>
        @endif
    </div>
</section>
{{-- ROW:end:content-two-column:o1x1EC --}}

{{-- ROW:start:cta-banner:S3575K:shared=1 --}}
@include('shared-rows.cta-banner-S3575K')
{{-- ROW:end:cta-banner:S3575K --}}
</div>
