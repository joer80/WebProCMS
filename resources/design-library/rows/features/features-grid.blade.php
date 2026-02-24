{{--
@name Features - Grid
@description Three-column feature grid with icons, headings, and descriptions.
@sort 10
--}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white">Everything You Need</h2>
            <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">Powerful features designed to help you succeed.</p>
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
