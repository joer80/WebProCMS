{{--
@name Content - Two Column
@description Two-column content section with text and image placeholder.
@sort 10
--}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            @php $badgeClasses = content('__SLUG__', 'badge_classes', 'text-sm font-semibold text-primary uppercase tracking-wider'); @endphp
            <span class="{{ $badgeClasses }}">{{ content('__SLUG__', 'badge', 'Our Story') }}</span>
            <x-dl-heading slug="__SLUG__" prefix="headline" default="We Are Building the Future of Work"
                default-tag="h2"
                default-classes="font-heading mt-3 text-4xl font-bold text-zinc-900 dark:text-white leading-tight" />
            @php $bodyClasses = content('__SLUG__', 'body_classes', 'mt-6 text-zinc-500 dark:text-zinc-400 leading-relaxed'); @endphp
            <p class="{{ $bodyClasses }}">
                {{ content('__SLUG__', 'body', 'Founded in 2020, we have been on a mission to help teams collaborate more effectively. Our platform combines the best of communication, project management, and automation into one seamless experience.') }}
            </p>
            @php $bodySecondaryClasses = content('__SLUG__', 'body_secondary_classes', 'mt-4 text-zinc-500 dark:text-zinc-400 leading-relaxed'); @endphp
            <p class="{{ $bodySecondaryClasses }}">
                {{ content('__SLUG__', 'body_secondary', 'Today, we are trusted by over 10,000 companies worldwide, from startups to Fortune 500 enterprises.') }}
            </p>
            @php $toggleCta = content('__SLUG__', 'toggle_cta', '1'); @endphp
            @php $ctaLabel = content('__SLUG__', 'cta_label', 'Learn more about us'); @endphp
            @php $ctaClasses = content('__SLUG__', 'cta_classes', 'mt-8 inline-flex items-center text-primary font-semibold hover:text-primary/80 transition-colors'); @endphp
            @if($toggleCta)
            <a
                href="{{ content('__SLUG__', 'cta_url', '#') }}"
                @if(content('__SLUG__', 'cta_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $ctaClasses }}"
            >
                {{ $ctaLabel }} →
            </a>
            @endif
        </div>
        <x-dl-media slug="__SLUG__"
            default-wrapper-classes="rounded-card overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-square flex items-center justify-center"
            default-image-classes="w-full h-full object-cover" />
</x-dl-section>
