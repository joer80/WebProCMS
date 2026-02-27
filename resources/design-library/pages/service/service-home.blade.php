{{--
@name Service Business Home Page
@description Home page for local service businesses with hero, services, and contact CTA.
@sort 10
--}}
<div>
    {{-- Hero --}}
    <section class="py-24 px-6 bg-white dark:bg-zinc-900">
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-5xl font-bold text-zinc-900 dark:text-white leading-tight">Professional Services You Can Trust</h1>
                <p class="mt-6 text-lg text-zinc-500 dark:text-zinc-400">Serving the community for over 20 years with reliable, high-quality service at competitive prices.</p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="/contact" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">Get a Free Quote</a>
                    <a href="tel:5551234567" class="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">Call Us Now</a>
                </div>
            </div>
            <div class="rounded-2xl bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center">
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Hero Image</span>
            </div>
        </div>
    </section>

    {{-- Services --}}
    <section class="py-20 px-6 bg-zinc-50 dark:bg-zinc-950">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-zinc-900 dark:text-white">Our Services</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach (['Service One', 'Service Two', 'Service Three'] as $service)
                    <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 text-center">
                        <div class="size-12 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <span class="text-primary text-xl">🔧</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $service }}</h3>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Description of this service and what's included.</p>
                        <a href="/services" class="mt-4 inline-block text-sm text-primary font-medium hover:text-primary/80">Learn more →</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
