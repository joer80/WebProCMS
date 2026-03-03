{{--
@name CTA - Banner
@description Full-width call-to-action banner with headline and button.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'bg-primary py-section px-6 text-center'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-3xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
        @if($toggleHeadline)
        @php $headlineTag = content('__SLUG__', 'headline_htag', 'h2'); @endphp
        @php $headlineText = content('__SLUG__', 'headline', 'Ready to Get Started?'); @endphp
        @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-white'); @endphp
        {!! "<{$headlineTag} class=\"" . e($headlineClasses) . "\">" . e($headlineText) . "</{$headlineTag}>" !!}
        @endif
        @php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
        @if($toggleSubheadline)
        @php $subheadlineText = content('__SLUG__', 'subheadline', 'Join thousands of satisfied customers today.'); @endphp
        @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-white/80'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        @php $buttonsWrapperClasses = content('__SLUG__', 'buttons_wrapper_classes', 'mt-8 flex flex-wrap items-center justify-center gap-4'); @endphp
        <div class="{{ $buttonsWrapperClasses }}">
            @php $togglePrimaryButton = content('__SLUG__', 'toggle_primary_button', '1'); @endphp
            @php $primaryButtonLabel = content('__SLUG__', 'primary_button', 'Start Free Trial'); @endphp
            @php $primaryButtonClasses = content('__SLUG__', 'primary_button_classes', 'px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors'); @endphp
            @if($togglePrimaryButton)
            <a
                href="{{ content('__SLUG__', 'primary_button_url', '#') }}"
                @if(content('__SLUG__', 'primary_button_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $primaryButtonClasses }}"
            >{{ $primaryButtonLabel }}</a>
            @endif
            @if(content('__SLUG__', 'toggle_secondary_button', '1'))
            @php $secondaryButtonLabel = content('__SLUG__', 'secondary_button', 'Talk to Sales'); @endphp
            @php $secondaryButtonClasses = content('__SLUG__', 'secondary_button_classes', 'px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors'); @endphp
            <a
                href="{{ content('__SLUG__', 'secondary_button_url', '#') }}"
                @if(content('__SLUG__', 'secondary_button_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $secondaryButtonClasses }}"
            >{{ $secondaryButtonLabel }}</a>
            @endif
        </div>
    </div>
</section>
