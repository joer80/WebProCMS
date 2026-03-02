{{--
@name Hero - Split
@description Two-column hero with text on the left and image placeholder on the right.
@sort 20
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        <div>
            @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Build Something Amazing', 'text', 'headline'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-5xl font-bold text-zinc-900 dark:text-white leading-tight', 'classes', 'headline'); @endphp
            <h1 class="{{ $headlineClasses }}">{{ $headlineText }}</h1>
            @endif
            @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
            @if($showSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'Describe your product or service here. Keep it concise and focused on the value you deliver to customers.', 'text', 'subheadline'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-6 text-lg text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
            @php $buttonsWrapperClasses = content('__SLUG__', 'buttons_wrapper_classes', 'mt-8 flex flex-wrap gap-4', 'classes', 'primary button'); @endphp
            <div class="{{ $buttonsWrapperClasses }}">
                @php $showPrimaryCta = content('__SLUG__', 'show_primary_cta', '1', 'toggle', 'primary button'); @endphp
                @php $primaryCtaLabel = content('__SLUG__', 'primary_cta', 'Start Free Trial', 'text', 'primary button'); @endphp
                @php $primaryCtaClasses = content('__SLUG__', 'primary_cta_classes', 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors', 'classes', 'primary button'); @endphp
                @if($showPrimaryCta)
                <a
                    href="{{ content('__SLUG__', 'primary_cta_url', '#', 'text', 'primary button') }}"
                    @if(content('__SLUG__', 'primary_cta_new_tab', '', 'toggle', 'primary button')) target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $primaryCtaClasses }}"
                >{{ $primaryCtaLabel }}</a>
                @endif
                @if(content('__SLUG__', 'show_secondary_cta', '1', 'toggle', 'secondary button'))
                @php $secondaryCtaLabel = content('__SLUG__', 'secondary_cta', 'Watch Demo →', 'text', 'secondary button'); @endphp
                @php $secondaryCtaClasses = content('__SLUG__', 'secondary_cta_classes', 'px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors', 'classes', 'secondary button'); @endphp
                <a
                    href="{{ content('__SLUG__', 'secondary_cta_url', '#', 'text', 'secondary button') }}"
                    @if(content('__SLUG__', 'secondary_cta_new_tab', '', 'toggle', 'secondary button')) target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $secondaryCtaClasses }}"
                >{{ $secondaryCtaLabel }}</a>
                @endif
            </div>
        </div>
        @php $imageWrapperClasses = content('__SLUG__', 'image_wrapper_classes', 'rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center', 'classes', 'media'); @endphp
        @php $imageClasses = content('__SLUG__', 'image_classes', 'w-full h-full object-cover', 'classes', 'media'); @endphp
        @if(content('__SLUG__', 'show_image', '1', 'toggle', 'media'))
        <div class="{{ $imageWrapperClasses }}">
            @php $heroImage = content('__SLUG__', 'image', '', 'image', 'media'); @endphp
            @if ($heroImage)
                <img src="{{ $heroImage }}" alt="{{ content('__SLUG__', 'image_alt', '', 'text', 'media') }}" class="{{ $imageClasses }}">
            @else
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
            @endif
        </div>
        @endif
    </div>
</section>
