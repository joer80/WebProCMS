{{--
@name Slider - Testimonial
@description Alpine.js testimonial slider with navigation dots and auto-advance.
@sort 10
--}}
<section class="py-section px-6 bg-zinc-50 dark:bg-zinc-950 overflow-hidden"
    x-data="{
        current: 0,
        total: 3,
        autoPlay() {
            setInterval(() => { this.current = (this.current + 1) % this.total; }, 4000);
        }
    }"
    x-init="autoPlay()"
>
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-12">{{ content('__SLUG__', 'headline', 'What Our Customers Say', 'text', 'content') }}</h2>

        <div class="relative">
            @foreach ([['quote' => 'This is honestly the best tool I\'ve used in years. It transformed how we work.', 'name' => 'Alex Thompson', 'role' => 'Founder at StartupXYZ'], ['quote' => 'The onboarding was seamless and the results were immediate. I highly recommend it.', 'name' => 'Jamie Rivera', 'role' => 'VP of Engineering'], ['quote' => 'Customer support is incredible. They went above and beyond to help our team.', 'name' => 'Morgan Lee', 'role' => 'Director of Operations']] as $i => $t)
                <div x-show="current === {{ $i }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <p class="text-xl text-zinc-700 dark:text-zinc-300 italic leading-relaxed">"{{ $t['quote'] }}"</p>
                    <div class="mt-6">
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ $t['name'] }}</div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $t['role'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-center gap-2 mt-8">
            @for ($i = 0; $i < 3; $i++)
                <button
                    @click="current = {{ $i }}"
                    :class="current === {{ $i }} ? 'bg-primary w-6' : 'bg-zinc-300 dark:bg-zinc-600 w-2'"
                    class="h-2 rounded-full transition-all duration-300"
                ></button>
            @endfor
        </div>
    </div>
</section>
