{{--
@name Header - Simple
@description Minimal sticky header with logo and navigation links.
@sort 10
--}}
<header class="sticky top-0 z-50 bg-white/80 dark:bg-zinc-900/80 backdrop-blur border-b border-zinc-200 dark:border-zinc-800">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="/" class="font-heading text-xl font-bold text-zinc-900 dark:text-white">{{ content('__SLUG__', 'brand_name', 'Brand', 'text', 'content') }}</a>
        <nav class="hidden md:flex items-center gap-8">
            <a href="#" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Features</a>
            <a href="#" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Pricing</a>
            <a href="#" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Blog</a>
            <a href="#" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Contact</a>
        </nav>
        @php $primaryCtaLabel = content('__SLUG__', 'primary_cta', 'Get Started', 'text', 'primary button'); @endphp
        <a href="{{ content('__SLUG__', 'primary_cta_url', '#', 'text', 'primary button') }}" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            {{ $primaryCtaLabel }}
        </a>
    </div>
</header>
