{{--
@name 404 - Simple
@description Clean 404 not found page with large error code and navigation links.
@sort 10
--}}
<section class="min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center px-6">
    <div class="text-center">
        <div class="text-8xl font-black text-zinc-200 dark:text-zinc-700">404</div>
        @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
        @if($showHeadline)
        @php $headlineText = content('__SLUG__', 'headline', 'Page Not Found', 'text', 'headline'); @endphp
        @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading mt-4 text-3xl font-bold text-zinc-900 dark:text-white', 'classes', 'headline'); @endphp
        <h1 class="{{ $headlineClasses }}">{{ $headlineText }}</h1>
        @endif
        @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
        @if($showSubheadline)
        @php $subheadlineText = content('__SLUG__', 'subheadline', 'Sorry, we couldn\'t find the page you\'re looking for. It may have been moved or deleted.', 'text', 'subheadline'); @endphp
        @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto', 'classes', 'subheadline'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            @php $showPrimaryCta = content('__SLUG__', 'show_primary_cta', '1', 'toggle', 'primary button'); @endphp
            @php $primaryCtaLabel = content('__SLUG__', 'primary_cta', 'Go Home', 'text', 'primary button'); @endphp
            @if($showPrimaryCta)
            <a href="{{ content('__SLUG__', 'primary_cta_url', '/', 'text', 'primary button') }}" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                {{ $primaryCtaLabel }}
            </a>
            @endif
            @if(content('__SLUG__', 'show_secondary_cta', '1', 'toggle', 'secondary button'))
            @php $secondaryCtaLabel = content('__SLUG__', 'secondary_cta', 'Contact Support', 'text', 'secondary button'); @endphp
            <a href="{{ content('__SLUG__', 'secondary_cta_url', '/contact', 'text', 'secondary button') }}" class="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                {{ $secondaryCtaLabel }}
            </a>
            @endif
        </div>
    </div>
</section>
