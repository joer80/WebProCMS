{{--
@name Custom Home Page
@description General-purpose homepage template suitable for any business type.
@sort 10
--}}
<div>
    <section class="py-24 px-6 bg-white dark:bg-zinc-900 text-center">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-5xl font-bold text-zinc-900 dark:text-white leading-tight">Welcome to Our Website</h1>
            <p class="mt-6 text-xl text-zinc-500 dark:text-zinc-400">Replace this with your compelling headline that clearly explains what you do and why it matters.</p>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="#" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">Primary Action</a>
                <a href="#" class="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">Secondary Action</a>
            </div>
        </div>
    </section>
    <section class="py-20 px-6 bg-zinc-50 dark:bg-zinc-950">
        <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-8">
            @foreach (['First Feature', 'Second Feature', 'Third Feature'] as $f)
                <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 text-center">
                    <div class="size-12 bg-primary/10 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-primary font-bold">✓</span>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $f }}</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Description of this feature and how it benefits your customers.</p>
                </div>
            @endforeach
        </div>
    </section>
</div>
