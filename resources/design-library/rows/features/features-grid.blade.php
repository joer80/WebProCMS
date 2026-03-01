{{--
@name Features - Grid
@description Three-column feature grid with icons, headings, and descriptions.
@sort 10
--}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            @php $showHeadline = content('__SLUG__', 'show_headline', '1', 'toggle', 'headline'); @endphp
            @if($showHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Everything You Need', 'text', 'headline'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'text-4xl font-bold text-zinc-900 dark:text-white', 'classes', 'headline'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $showSubheadline = content('__SLUG__', 'show_subheadline', '1', 'toggle', 'subheadline'); @endphp
            @if($showSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'Powerful features designed to help you succeed.', 'text', 'subheadline'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400', 'classes', 'subheadline'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
            @endif
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach ([['icon' => '⚡', 'title' => 'Lightning Fast', 'desc' => 'Optimized for speed at every level of the stack.'], ['icon' => '🔒', 'title' => 'Secure by Default', 'desc' => 'Enterprise-grade security built into every feature.'], ['icon' => '📊', 'title' => 'Detailed Analytics', 'desc' => 'Gain insight into every aspect of your business.'], ['icon' => '🔧', 'title' => 'Easy to Customize', 'desc' => 'Tailor the platform to your exact requirements.'], ['icon' => '🌍', 'title' => 'Global Scale', 'desc' => 'Built to handle millions of users worldwide.'], ['icon' => '💬', 'title' => '24/7 Support', 'desc' => 'Our team is always here when you need us.']] as $feature)
                <div class="p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors">
                    <div class="text-3xl mb-4">{{ $feature['icon'] }}</div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
