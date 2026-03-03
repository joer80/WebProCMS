{{--
@name Hero - Centered
@description Full-width centered hero with headline, subheadline, and dual CTA buttons.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900 text-center'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-3xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $toggleBadge = content('__SLUG__', 'toggle_badge', '1'); @endphp
        @php $badgeClasses = content('__SLUG__', 'badge_classes', 'inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6'); @endphp
        @if($toggleBadge)
        <span class="{{ $badgeClasses }}">{{ content('__SLUG__', 'badge', 'Welcome') }}</span>
        @endif
        @php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
        @if($toggleHeadline)
        @php $headlineTag = content('__SLUG__', 'headline_htag', 'h1'); @endphp
        @php $headlineText = content('__SLUG__', 'headline', 'Your Headline Goes Here'); @endphp
        @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight'); @endphp
        {!! "<{$headlineTag} class=\"" . e($headlineClasses) . "\">" . e($headlineText) . "</{$headlineTag}>" !!}
        @endif
        @php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
        @if($toggleSubheadline)
        @php $subheadlineText = content('__SLUG__', 'subheadline', 'A compelling subheadline that explains what you do and why it matters to your audience.'); @endphp
        @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        @php $buttonsWrapperClasses = content('__SLUG__', 'buttons_wrapper_classes', 'mt-10 flex flex-wrap items-center justify-center gap-4'); @endphp
        <div class="{{ $buttonsWrapperClasses }}">
            @php $togglePrimaryButton = content('__SLUG__', 'toggle_primary_button', '1'); @endphp
            @php $primaryButtonLabel = content('__SLUG__', 'primary_button', 'Get Started'); @endphp
            @php $primaryButtonClasses = content('__SLUG__', 'primary_button_classes', 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'); @endphp
            @if($togglePrimaryButton)
            <a
                href="{{ content('__SLUG__', 'primary_button_url', '#') }}"
                @if(content('__SLUG__', 'primary_button_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $primaryButtonClasses }}"
            >{{ $primaryButtonLabel }}</a>
            @endif
            @if(content('__SLUG__', 'toggle_secondary_button', '1'))
            @php $secondaryButtonLabel = content('__SLUG__', 'secondary_button', 'Learn More'); @endphp
            @php $secondaryButtonClasses = content('__SLUG__', 'secondary_button_classes', 'px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors'); @endphp
            <a
                href="{{ content('__SLUG__', 'secondary_button_url', '#') }}"
                @if(content('__SLUG__', 'secondary_button_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $secondaryButtonClasses }}"
            >{{ $secondaryButtonLabel }}</a>
            @endif
        </div>
    </div>
</section>
