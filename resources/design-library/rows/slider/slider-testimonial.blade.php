{{--
@name Slider - Testimonial
@description Alpine.js testimonial slider with navigation dots and auto-advance.
@sort 10
--}}
{{-- TODO: Convert hardcoded testimonial array to grid_testimonials JSON field for editability. Cannot use x-dl-grid (this is an Alpine slider, not a CSS grid layout). --}}
<x-dl-section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950 overflow-hidden"
    default-container-classes="max-w-3xl mx-auto text-center"
    x-data="{
        current: 0,
        total: 3,
        autoPlay() {
            setInterval(() => { this.current = (this.current + 1) % this.total; }, 4000);
        }
    }"
    x-init="autoPlay()">
        <x-dl-heading slug="__SLUG__" prefix="headline" default="What Our Customers Say"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-12" />

        <x-dl-wrapper slug="__SLUG__" prefix="slides_wrapper"
            default-classes="relative">
            @foreach ([['quote' => 'This is honestly the best tool I\'ve used in years. It transformed how we work.', 'name' => 'Alex Thompson', 'role' => 'Founder at StartupXYZ'], ['quote' => 'The onboarding was seamless and the results were immediate. I highly recommend it.', 'name' => 'Jamie Rivera', 'role' => 'VP of Engineering'], ['quote' => 'Customer support is incredible. They went above and beyond to help our team.', 'name' => 'Morgan Lee', 'role' => 'Director of Operations']] as $i => $t)
                <div x-show="current === {{ $i }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <x-dl-wrapper slug="__SLUG__" prefix="quote" tag="p"
                        default-classes="text-xl text-zinc-700 dark:text-zinc-300 italic leading-relaxed">
                        "{{ $t['quote'] }}"
                    </x-dl-wrapper>
                    <x-dl-wrapper slug="__SLUG__" prefix="author_wrapper"
                        default-classes="mt-6">
                        <x-dl-wrapper slug="__SLUG__" prefix="author_name"
                            default-classes="font-semibold text-zinc-900 dark:text-white">
                            {{ $t['name'] }}
                        </x-dl-wrapper>
                        <x-dl-wrapper slug="__SLUG__" prefix="author_role"
                            default-classes="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $t['role'] }}
                        </x-dl-wrapper>
                    </x-dl-wrapper>
                </div>
            @endforeach
        </x-dl-wrapper>

        <x-dl-wrapper slug="__SLUG__" prefix="dots_wrapper"
            default-classes="flex items-center justify-center gap-2 mt-8">
            @for ($i = 0; $i < 3; $i++)
                <x-dl-wrapper slug="__SLUG__" prefix="dot_base" tag="button"
                    default-classes="h-2 rounded-full transition-all duration-300"
                    @click="current = {{ $i }}"
                    :class="current === {{ $i }} ? 'bg-primary w-6' : 'bg-zinc-300 dark:bg-zinc-600 w-2'">
                </x-dl-wrapper>
            @endfor
        </x-dl-wrapper>
</x-dl-section>
