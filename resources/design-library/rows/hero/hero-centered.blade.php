{{--
@name Hero - Centered
@description Full-width centered hero with headline, subheadline, and dual CTA buttons.
@sort 10
--}}
<section class="py-section px-6 bg-white dark:bg-zinc-900 text-center">
    <div class="max-w-3xl mx-auto">
        @if(content('__SLUG__', 'show_badge', '1', 'toggle', 'content'))
        <span class="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6">{{ content('__SLUG__', 'badge', 'Welcome', 'text', 'content') }}</span>
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
        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            @php $showPrimaryCta = content('__SLUG__', 'show_primary_cta', '1', 'toggle', 'primary button'); @endphp
            @php $primaryCtaLabel = content('__SLUG__', 'primary_cta', 'Get Started', 'text', 'primary button'); @endphp
            @if($showPrimaryCta)
            <a
                href="{{ content('__SLUG__', 'primary_cta_url', '#', 'text', 'primary button') }}"
                @if(content('__SLUG__', 'primary_cta_new_tab', '', 'toggle', 'primary button')) target="_blank" rel="noopener noreferrer" @endif
                class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            >{{ $primaryCtaLabel }}</a>
            @endif
            @if(content('__SLUG__', 'show_secondary_cta', '1', 'toggle', 'secondary button'))
            @php $secondaryCtaLabel = content('__SLUG__', 'secondary_cta', 'Learn More', 'text', 'secondary button'); @endphp
            <a
                href="{{ content('__SLUG__', 'secondary_cta_url', '#', 'text', 'secondary button') }}"
                @if(content('__SLUG__', 'secondary_cta_new_tab', '', 'toggle', 'secondary button')) target="_blank" rel="noopener noreferrer" @endif
                class="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
            >{{ $secondaryCtaLabel }}</a>
            @endif
        </div>
    </div>
</section>
