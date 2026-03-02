{{--
@name Hero - Centered
@description Full-width centered hero with headline, subheadline, and dual CTA buttons.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900 text-center', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-3xl mx-auto', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        @php $showBadge = content('__SLUG__', 'show_badge', '1', 'toggle', 'content'); @endphp
        @php $badgeClasses = content('__SLUG__', 'badge_classes', 'inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6', 'classes', 'content'); @endphp
        @if($showBadge)
        <span class="{{ $badgeClasses }}">{{ content('__SLUG__', 'badge', 'Welcome', 'text', 'content') }}</span>
        @endif
        @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
        @if($showHeadline)
        @php $headlineText = content('__SLUG__', 'headline', 'Your Headline Goes Here', 'text', 'headline'); @endphp
        @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight', 'classes', 'headline'); @endphp
        <h1 class="{{ $headlineClasses }}">{{ $headlineText }}</h1>
        @endif
        @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
        @if($showSubheadline)
        @php $subheadlineText = content('__SLUG__', 'subheadline', 'A compelling subheadline that explains what you do and why it matters to your audience.', 'text', 'subheadline'); @endphp
        @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed', 'classes', 'subheadline'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        @php $buttonsWrapperClasses = content('__SLUG__', 'buttons_wrapper_classes', 'mt-10 flex flex-wrap items-center justify-center gap-4', 'classes', 'content'); @endphp
        <div class="{{ $buttonsWrapperClasses }}">
            @php $showPrimaryCta = content('__SLUG__', 'show_primary_cta', '1', 'toggle', 'primary button'); @endphp
            @php $primaryCtaLabel = content('__SLUG__', 'primary_cta', 'Get Started', 'text', 'primary button'); @endphp
            @php $primaryCtaClasses = content('__SLUG__', 'primary_cta_classes', 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors', 'classes', 'primary button'); @endphp
            @if($showPrimaryCta)
            <a
                href="{{ content('__SLUG__', 'primary_cta_url', '#', 'text', 'primary button') }}"
                @if(content('__SLUG__', 'primary_cta_new_tab', '', 'toggle', 'primary button')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $primaryCtaClasses }}"
            >{{ $primaryCtaLabel }}</a>
            @endif
            @if(content('__SLUG__', 'show_secondary_cta', '1', 'toggle', 'secondary button'))
            @php $secondaryCtaLabel = content('__SLUG__', 'secondary_cta', 'Learn More', 'text', 'secondary button'); @endphp
            @php $secondaryCtaClasses = content('__SLUG__', 'secondary_cta_classes', 'px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors', 'classes', 'secondary button'); @endphp
            <a
                href="{{ content('__SLUG__', 'secondary_cta_url', '#', 'text', 'secondary button') }}"
                @if(content('__SLUG__', 'secondary_cta_new_tab', '', 'toggle', 'secondary button')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $secondaryCtaClasses }}"
            >{{ $secondaryCtaLabel }}</a>
            @endif
        </div>
    </div>
</section>
