@php $sectionClasses = content('cta-banner:uuRPa3', 'section_classes', 'bg-primary py-section px-6 text-center'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('cta-banner:uuRPa3', 'section_container_classes', 'max-w-3xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $toggleHeadline = content('cta-banner:uuRPa3', 'toggle_headline', '1'); @endphp
        @if($toggleHeadline)
        @php $headlineTag = content('cta-banner:uuRPa3', 'headline_htag', 'h2'); @endphp
        @php $headlineText = content('cta-banner:uuRPa3', 'headline', 'Ready to Get Started?'); @endphp
        @php $headlineClasses = content('cta-banner:uuRPa3', 'headline_classes', 'font-heading text-4xl font-bold text-white'); @endphp
        {!! "<{$headlineTag} class=\"" . e($headlineClasses) . "\">" . e($headlineText) . "</{$headlineTag}>" !!}
        @endif
        @php $toggleSubheadline = content('cta-banner:uuRPa3', 'toggle_subheadline', '1'); @endphp
        @if($toggleSubheadline)
        @php $subheadlineText = content('cta-banner:uuRPa3', 'subheadline', 'Join thousands of satisfied customers today.'); @endphp
        @php $subheadlineClasses = content('cta-banner:uuRPa3', 'subheadline_classes', 'mt-4 text-lg text-white/80'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        @php $buttonsWrapperClasses = content('cta-banner:uuRPa3', 'buttons_wrapper_classes', 'mt-8 flex flex-wrap items-center justify-center gap-4'); @endphp
        <div class="{{ $buttonsWrapperClasses }}">
            @php $togglePrimaryCta = content('cta-banner:uuRPa3', 'toggle_primary_cta', '1'); @endphp
            @php $primaryCtaLabel = content('cta-banner:uuRPa3', 'primary_cta', 'Start Free Trial'); @endphp
            @php $primaryCtaClasses = content('cta-banner:uuRPa3', 'primary_cta_classes', 'px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors'); @endphp
            @if($togglePrimaryCta)
            <a
                href="{{ content('cta-banner:uuRPa3', 'primary_cta_url', '#') }}"
                @if(content('cta-banner:uuRPa3', 'primary_cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $primaryCtaClasses }}"
            >{{ $primaryCtaLabel }}</a>
            @endif
            @if(content('cta-banner:uuRPa3', 'toggle_secondary_cta', '1'))
            @php $secondaryCtaLabel = content('cta-banner:uuRPa3', 'secondary_cta', 'Talk to Sales'); @endphp
            @php $secondaryCtaClasses = content('cta-banner:uuRPa3', 'secondary_cta_classes', 'px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors'); @endphp
            <a
                href="{{ content('cta-banner:uuRPa3', 'secondary_cta_url', '#') }}"
                @if(content('cta-banner:uuRPa3', 'secondary_cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $secondaryCtaClasses }}"
            >{{ $secondaryCtaLabel }}</a>
            @endif
        </div>
    </div>
</section>