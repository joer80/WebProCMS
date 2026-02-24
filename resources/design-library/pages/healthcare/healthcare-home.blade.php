{{--
@name Healthcare Home Page
@description Healthcare provider homepage with hero, services, and appointment booking CTA.
@sort 10
--}}
<div>
    <section class="py-24 px-6 bg-white dark:bg-zinc-900">
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6">Accepting New Patients</span>
                <h1 class="text-5xl font-bold text-zinc-900 dark:text-white leading-tight">Compassionate Care for You and Your Family</h1>
                <p class="mt-6 text-lg text-zinc-500 dark:text-zinc-400">Our experienced medical team provides personalized, evidence-based care in a welcoming environment.</p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="/contact" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">Book Appointment</a>
                    <a href="/patients" class="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">Patient Portal</a>
                </div>
                <p class="mt-4 text-sm text-zinc-400">📞 (555) 123-4567 · Available 24/7 for emergencies</p>
            </div>
            <div class="rounded-2xl bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center">
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Hero Image</span>
            </div>
        </div>
    </section>
    <section class="py-20 px-6 bg-zinc-50 dark:bg-zinc-950">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-zinc-900 dark:text-white text-center mb-12">Our Services</h2>
            <div class="grid md:grid-cols-4 gap-6">
                @foreach (['Primary Care', 'Urgent Care', 'Pediatrics', 'Specialists'] as $service)
                    <div class="p-5 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 text-center">
                        <div class="text-3xl mb-3">🏥</div>
                        <h3 class="font-semibold text-zinc-900 dark:text-white text-sm">{{ $service }}</h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
