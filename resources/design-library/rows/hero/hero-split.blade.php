{{--
@name Hero - Split
@description Two-column hero with text on the left and image placeholder on the right.
@sort 20
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        <div>
            @php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
            @if($toggleHeadline)
            @php $headlineTag = content('__SLUG__', 'headline_htag', 'h1'); @endphp
            @php $headlineText = content('__SLUG__', 'headline', 'Build Something Amazing'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-5xl font-bold text-zinc-900 dark:text-white leading-tight'); @endphp
            {!! "<{$headlineTag} class=\"" . e($headlineClasses) . "\">" . e($headlineText) . "</{$headlineTag}>" !!}
            @endif
            @php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
            @if($toggleSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'Describe your product or service here. Keep it concise and focused on the value you deliver to customers.'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-6 text-lg text-zinc-500 dark:text-zinc-400'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
            @php $buttonsWrapperClasses = content('__SLUG__', 'buttons_wrapper_classes', 'mt-8 flex flex-wrap gap-4'); @endphp
            <div class="{{ $buttonsWrapperClasses }}">
                @php $togglePrimaryCta = content('__SLUG__', 'toggle_primary_cta', '1'); @endphp
                @php $primaryCtaLabel = content('__SLUG__', 'primary_cta', 'Start Free Trial'); @endphp
                @php $primaryCtaClasses = content('__SLUG__', 'primary_cta_classes', 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'); @endphp
                @if($togglePrimaryCta)
                <a
                    href="{{ content('__SLUG__', 'primary_cta_url', '#') }}"
                    @if(content('__SLUG__', 'primary_cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $primaryCtaClasses }}"
                >{{ $primaryCtaLabel }}</a>
                @endif
                @if(content('__SLUG__', 'toggle_secondary_cta', '1'))
                @php $secondaryCtaLabel = content('__SLUG__', 'secondary_cta', 'Watch Demo →'); @endphp
                @php $secondaryCtaClasses = content('__SLUG__', 'secondary_cta_classes', 'px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors'); @endphp
                <a
                    href="{{ content('__SLUG__', 'secondary_cta_url', '#') }}"
                    @if(content('__SLUG__', 'secondary_cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $secondaryCtaClasses }}"
                >{{ $secondaryCtaLabel }}</a>
                @endif
            </div>
        </div>
        @php $imageWrapperClasses = content('__SLUG__', 'image_wrapper_classes', 'rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center'); @endphp
        @php $imageClasses = content('__SLUG__', 'image_classes', 'w-full h-full object-cover'); @endphp
        @if(content('__SLUG__', 'toggle_image', '1'))
        <div class="{{ $imageWrapperClasses }}">
            @php $heroImage = content('__SLUG__', 'image', ''); @endphp
            @if ($heroImage)
                <img src="{{ $heroImage }}" alt="{{ content('__SLUG__', 'image_alt', '') }}" class="{{ $imageClasses }}">
            @else
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
            @endif
        </div>
        @endif
    </div>
</section>
