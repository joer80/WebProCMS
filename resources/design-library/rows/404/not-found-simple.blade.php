{{--
@name 404 - Simple
@description Clean 404 not found page with large error code and navigation links.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center px-6'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'text-center'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $errorCodeClasses = content('__SLUG__', 'error_code_classes', 'text-8xl font-black text-zinc-200 dark:text-zinc-700'); @endphp
        @php $buttonsWrapperClasses = content('__SLUG__', 'buttons_wrapper_classes', 'mt-8 flex flex-wrap items-center justify-center gap-4'); @endphp
        @php $primaryButtonClasses = content('__SLUG__', 'primary_button_classes', 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'); @endphp
        @php $toggleSecondaryButton = content('__SLUG__', 'toggle_secondary_button', '1'); @endphp
        @php $secondaryButtonLabel = content('__SLUG__', 'secondary_button', 'Contact Support'); @endphp
        @php $secondaryButtonClasses = content('__SLUG__', 'secondary_button_classes', 'px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors'); @endphp
        <div class="{{ $errorCodeClasses }}">404</div>
        @php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
        @if($toggleHeadline)
        @php $headlineTag = content('__SLUG__', 'headline_htag', 'h1'); @endphp
        @php $headlineText = content('__SLUG__', 'headline', 'Page Not Found'); @endphp
        @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading mt-4 text-3xl font-bold text-zinc-900 dark:text-white'); @endphp
        {!! "<{$headlineTag} class=\"" . e($headlineClasses) . "\">" . e($headlineText) . "</{$headlineTag}>" !!}
        @endif
        @php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
        @if($toggleSubheadline)
        @php $subheadlineText = content('__SLUG__', 'subheadline', 'Sorry, we couldn\'t find the page you\'re looking for. It may have been moved or deleted.'); @endphp
        @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        <div class="{{ $buttonsWrapperClasses }}">
            @php $togglePrimaryButton = content('__SLUG__', 'toggle_primary_button', '1'); @endphp
            @php $primaryButtonLabel = content('__SLUG__', 'primary_button', 'Go Home'); @endphp
            @if($togglePrimaryButton)
            <a href="{{ content('__SLUG__', 'primary_button_url', '/') }}" class="{{ $primaryButtonClasses }}">
                {{ $primaryButtonLabel }}
            </a>
            @endif
            @if($toggleSecondaryButton)
            <a href="{{ content('__SLUG__', 'secondary_button_url', '/contact') }}" class="{{ $secondaryButtonClasses }}">
                {{ $secondaryButtonLabel }}
            </a>
            @endif
        </div>
    </div>
</section>
