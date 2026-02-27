{{--
@name Nonprofit Home Page
@description Nonprofit homepage with mission statement, impact stats, and donation CTA.
@sort 10
--}}
<div>
    <section class="py-24 px-6 bg-white dark:bg-zinc-900 text-center">
        <div class="max-w-3xl mx-auto">
            <span class="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6">Est. 2005</span>
            <h1 class="text-5xl font-bold text-zinc-900 dark:text-white leading-tight">Together We Can Make a Difference</h1>
            <p class="mt-6 text-lg text-zinc-500 dark:text-zinc-400">We're dedicated to creating a better world through community action, education, and advocacy.</p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="/donate" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">Donate Now</a>
                <a href="/volunteer" class="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">Volunteer</a>
            </div>
        </div>
    </section>
    <section class="py-12 px-6 bg-primary">
        <div class="max-w-4xl mx-auto grid grid-cols-3 gap-8 text-center text-white">
            @foreach ([['num' => '50K+', 'label' => 'Lives Impacted'], ['num' => '$2M+', 'label' => 'Funds Raised'], ['num' => '200+', 'label' => 'Volunteers']] as $stat)
                <div>
                    <div class="text-4xl font-black">{{ $stat['num'] }}</div>
                    <div class="mt-1 text-white/80 text-sm">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </section>
</div>
