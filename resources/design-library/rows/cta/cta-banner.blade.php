{{--
@name CTA - Banner
@description Full-width call-to-action banner with headline and button.
@sort 10
--}}
<section class="bg-primary py-16 px-6 text-center">
    <div class="max-w-3xl mx-auto">
        @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
        @if($showHeadline)
        @php $headlineText = content('__SLUG__', 'headline', 'Ready to Get Started?', 'text', 'headline'); @endphp
        @php $headlineClasses = content('__SLUG__', 'headline_classes', 'text-4xl font-bold text-white', 'classes', 'headline'); @endphp
        <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
        @endif
        @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
        @if($showSubheadline)
        @php $subheadlineText = content('__SLUG__', 'subheadline', 'Join thousands of satisfied customers today.', 'text', 'subheadline'); @endphp
        @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-white/80', 'classes', 'subheadline'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            @php $showPrimaryCta = content('__SLUG__', 'show_primary_cta', '1', 'toggle', 'primary button'); @endphp
            @php $primaryCtaLabel = content('__SLUG__', 'primary_cta', 'Start Free Trial', 'text', 'primary button'); @endphp
            @if($showPrimaryCta)
            <a
                href="{{ content('__SLUG__', 'primary_cta_url', '#', 'text', 'primary button') }}"
                @if(content('__SLUG__', 'primary_cta_new_tab', '', 'toggle', 'primary button')) target="_blank" rel="noopener noreferrer" @endif
                class="px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors"
            >{{ $primaryCtaLabel }}</a>
            @endif
            @if(content('__SLUG__', 'show_secondary_cta', '1', 'toggle', 'secondary button'))
            @php $secondaryCtaLabel = content('__SLUG__', 'secondary_cta', 'Talk to Sales', 'text', 'secondary button'); @endphp
            <a
                href="{{ content('__SLUG__', 'secondary_cta_url', '#', 'text', 'secondary button') }}"
                @if(content('__SLUG__', 'secondary_cta_new_tab', '', 'toggle', 'secondary button')) target="_blank" rel="noopener noreferrer" @endif
                class="px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors"
            >{{ $secondaryCtaLabel }}</a>
            @endif
        </div>
    </div>
</section>
