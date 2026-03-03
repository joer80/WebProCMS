@php $sectionClasses = content('cta-banner:S3575K', 'section_classes', 'bg-primary py-section px-6 text-center'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('cta-banner:S3575K', 'section_container_classes', 'max-w-3xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $toggleHeadline = content('cta-banner:S3575K', 'toggle_headline', '1'); @endphp
        @if($toggleHeadline)
        @php $headlineText = content('cta-banner:S3575K', 'headline', 'Ready to Get Started?'); @endphp
        @php $headlineClasses = content('cta-banner:S3575K', 'headline_classes', 'font-heading text-4xl font-bold text-white'); @endphp
        <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
        @endif
        @php $toggleSubheadline = content('cta-banner:S3575K', 'toggle_subheadline', '1'); @endphp
        @if($toggleSubheadline)
        @php $subheadlineText = content('cta-banner:S3575K', 'subheadline', 'Join thousands of satisfied customers today.'); @endphp
        @php $subheadlineClasses = content('cta-banner:S3575K', 'subheadline_classes', 'mt-4 text-lg text-white/80'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        @php $buttonsWrapperClasses = content('cta-banner:S3575K', 'buttons_wrapper_classes', 'mt-8 flex flex-wrap items-center justify-center gap-4'); @endphp
        <div class="{{ $buttonsWrapperClasses }}">
            @php $togglePrimaryCta = content('cta-banner:S3575K', 'toggle_primary_cta', '1'); @endphp
            @php $primaryCtaLabel = content('cta-banner:S3575K', 'primary_cta', 'Start Free Trial'); @endphp
            @php $primaryCtaClasses = content('cta-banner:S3575K', 'primary_cta_classes', 'px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors'); @endphp
            @if($togglePrimaryCta)
            <a
                href="{{ content('cta-banner:S3575K', 'primary_cta_url', '#') }}"
                @if(content('cta-banner:S3575K', 'primary_cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $primaryCtaClasses }}"
            >{{ $primaryCtaLabel }}</a>
            @endif
            @if(content('cta-banner:S3575K', 'toggle_secondary_cta', '1'))
            @php $secondaryCtaLabel = content('cta-banner:S3575K', 'secondary_cta', 'Talk to Sales'); @endphp
            @php $secondaryCtaClasses = content('cta-banner:S3575K', 'secondary_cta_classes', 'px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors'); @endphp
            <a
                href="{{ content('cta-banner:S3575K', 'secondary_cta_url', '#') }}"
                @if(content('cta-banner:S3575K', 'secondary_cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $secondaryCtaClasses }}"
            >{{ $secondaryCtaLabel }}</a>
            @endif
        </div>
    </div>
</section>