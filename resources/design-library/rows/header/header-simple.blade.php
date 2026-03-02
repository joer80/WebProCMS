{{--
@name Header - Simple
@description Minimal sticky header with logo and navigation links.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'sticky top-0 z-50 bg-white/80 dark:bg-zinc-900/80 backdrop-blur border-b border-zinc-200 dark:border-zinc-800'); @endphp
<header class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-6xl mx-auto px-6 h-16 flex items-center justify-between'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $brandClasses = content('__SLUG__', 'brand_classes', 'font-heading text-xl font-bold text-zinc-900 dark:text-white'); @endphp
        @php $navClasses = content('__SLUG__', 'nav_classes', 'hidden md:flex items-center gap-8'); @endphp
        @php $navLinkClasses = content('__SLUG__', 'nav_link_classes', 'text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors'); @endphp
        <a href="/" class="{{ $brandClasses }}">{{ content('__SLUG__', 'brand_name', 'Brand') }}</a>
        <nav class="{{ $navClasses }}">
            <a href="#" class="{{ $navLinkClasses }}">Features</a>
            <a href="#" class="{{ $navLinkClasses }}">Pricing</a>
            <a href="#" class="{{ $navLinkClasses }}">Blog</a>
            <a href="#" class="{{ $navLinkClasses }}">Contact</a>
        </nav>
        @php $primaryCtaLabel = content('__SLUG__', 'primary_cta', 'Get Started'); @endphp
        @php $primaryCtaClasses = content('__SLUG__', 'primary_cta_classes', 'px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors'); @endphp
        <a href="{{ content('__SLUG__', 'primary_cta_url', '#') }}" class="{{ $primaryCtaClasses }}">
            {{ $primaryCtaLabel }}
        </a>
    </div>
</header>
