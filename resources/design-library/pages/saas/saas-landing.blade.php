{{--
@name SaaS Landing Page
@description Complete SaaS landing page with hero, features, pricing, testimonials, and CTA.
@sort 10
--}}
<div>
    {{-- Hero --}}
    <section class="py-24 px-6 bg-white dark:bg-zinc-900 text-center">
        <div class="max-w-3xl mx-auto">
            <span class="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6">Now in Beta</span>
            <h1 class="text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight">The Smarter Way to Manage Your Business</h1>
            <p class="mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed">Streamline operations, boost productivity, and grow your business with our all-in-one platform.</p>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="#" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">Start Free Trial</a>
                <a href="#" class="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">Watch Demo</a>
            </div>
            <p class="mt-4 text-sm text-zinc-400">No credit card required · 14-day free trial</p>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-20 px-6 bg-zinc-50 dark:bg-zinc-950">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-zinc-900 dark:text-white">Everything You Need</h2>
                <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">Powerful features, thoughtfully designed.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach ([['icon' => '⚡', 'title' => 'Blazing Fast', 'desc' => 'Built for speed from the ground up.'], ['icon' => '🔒', 'title' => 'Enterprise Security', 'desc' => 'SOC 2 Type II certified with end-to-end encryption.'], ['icon' => '📊', 'title' => 'Powerful Analytics', 'desc' => 'Real-time insights to make smarter decisions.']] as $f)
                    <div class="p-6 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
                        <div class="text-3xl mb-4">{{ $f['icon'] }}</div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">{{ $f['title'] }}</h3>
                        <p class="text-zinc-500 dark:text-zinc-400 text-sm">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-primary py-16 px-6 text-center">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-4xl font-bold text-white">Ready to Get Started?</h2>
            <p class="mt-4 text-white/80">Join 10,000+ businesses already using our platform.</p>
            <a href="#" class="mt-8 inline-block px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors">Start Free Trial</a>
        </div>
    </section>
</div>
