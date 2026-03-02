{{--
@name Content - Two Column
@description Two-column content section with text and image placeholder.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900', 'classes', 'section'); @endphp
<section class="{{ $sectionClasses }}">
    @php $containerClasses = content('__SLUG__', 'container_classes', 'max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center', 'classes', 'section'); @endphp
    <div class="{{ $containerClasses }}">
        <div>
            @php $badgeClasses = content('__SLUG__', 'badge_classes', 'text-sm font-semibold text-primary uppercase tracking-wider', 'classes', 'content'); @endphp
            @php $imageWrapperClasses = content('__SLUG__', 'image_wrapper_classes', 'rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square flex items-center justify-center', 'classes', 'media'); @endphp
            @php $imageClasses = content('__SLUG__', 'image_classes', 'w-full h-full object-cover', 'classes', 'media'); @endphp
            <span class="{{ $badgeClasses }}">{{ content('__SLUG__', 'badge', 'Our Story', 'text', 'content') }}</span>
            @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'We Are Building the Future of Work', 'text', 'headline'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading mt-3 text-4xl font-bold text-zinc-900 dark:text-white leading-tight', 'classes', 'headline'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $bodyClasses = content('__SLUG__', 'body_classes', 'mt-6 text-zinc-500 dark:text-zinc-400 leading-relaxed', 'classes', 'content'); @endphp
            <p class="{{ $bodyClasses }}">
                {{ content('__SLUG__', 'body', 'Founded in 2020, we have been on a mission to help teams collaborate more effectively. Our platform combines the best of communication, project management, and automation into one seamless experience.', 'text', 'content') }}
            </p>
            @php $bodySecondaryClasses = content('__SLUG__', 'body_secondary_classes', 'mt-4 text-zinc-500 dark:text-zinc-400 leading-relaxed', 'classes', 'content'); @endphp
            <p class="{{ $bodySecondaryClasses }}">
                {{ content('__SLUG__', 'body_secondary', 'Today, we are trusted by over 10,000 companies worldwide, from startups to Fortune 500 enterprises.', 'text', 'content') }}
            </p>
            @php $showCta = content('__SLUG__', 'show_cta', '1', 'toggle', 'primary button'); @endphp
            @php $ctaLabel = content('__SLUG__', 'cta_label', 'Learn more about us', 'text', 'primary button'); @endphp
            @php $ctaClasses = content('__SLUG__', 'cta_classes', 'mt-8 inline-flex items-center text-primary font-semibold hover:text-primary/80 transition-colors', 'classes', 'primary button'); @endphp
            @if($showCta)
            <a
                href="{{ content('__SLUG__', 'cta_url', '#', 'text', 'primary button') }}"
                @if(content('__SLUG__', 'cta_new_tab', '', 'toggle', 'primary button')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $ctaClasses }}"
            >
                {{ $ctaLabel }} →
            </a>
            @endif
        </div>
        @if(content('__SLUG__', 'show_image', '1', 'toggle', 'media'))
        <div class="{{ $imageWrapperClasses }}">
            @php $sectionImage = content('__SLUG__', 'image', '', 'image', 'media'); @endphp
            @if ($sectionImage)
                <img src="{{ $sectionImage }}" alt="{{ content('__SLUG__', 'image_alt', '', 'text', 'media') }}" class="{{ $imageClasses }}">
            @else
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image Placeholder</span>
            @endif
        </div>
        @endif
    </div>
</section>
