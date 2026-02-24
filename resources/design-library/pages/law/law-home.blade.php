{{--
@name Law Firm Home Page
@description Law firm homepage with hero, practice areas, and consultation CTA.
@sort 10
--}}
<div>
    <section class="py-24 px-6 bg-zinc-900 text-white">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl font-bold leading-tight">Experienced Legal Representation You Can Count On</h1>
            <p class="mt-6 text-zinc-400 text-lg">Over 30 years of experience fighting for our clients' rights across all practice areas.</p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="/contact" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">Free Consultation</a>
                <a href="/practice-areas" class="px-6 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors">Our Practice Areas</a>
            </div>
        </div>
    </section>
    <section class="py-20 px-6 bg-white dark:bg-zinc-900">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-zinc-900 dark:text-white text-center mb-12">Practice Areas</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach (['Personal Injury', 'Family Law', 'Criminal Defense', 'Estate Planning', 'Business Law', 'Real Estate'] as $area)
                    <a href="/practice-areas" class="p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 group transition-colors">
                        <div class="text-2xl mb-3">⚖️</div>
                        <h3 class="font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">{{ $area }}</h3>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Brief description of this practice area.</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>
