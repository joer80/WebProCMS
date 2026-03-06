{{--
@name Social Proof - Testimonial Slider
@description Auto-playing testimonial slider on a dark background.
@sort 60
--}}
@dlItems('__SLUG__', 'testimonials', $testimonials, '[{"quote":"This platform completely transformed how our team operates. I cannot imagine going back.","name":"Sarah Johnson","role":"CEO at Acme Co"},{"quote":"The best investment we made this year. Onboarding was seamless and support is world-class.","name":"Mark Davis","role":"CTO at BuildIt"},{"quote":"Our productivity doubled in the first month. Highly recommend to any growing team.","name":"Lisa Chen","role":"Product Manager at TechCorp"}]')
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900 overflow-hidden"
    default-container-classes="max-w-3xl mx-auto text-center"
    x-data="{
        current: 0,
        total: {{ count($testimonials) }},
        autoPlay() {
            setInterval(() => { this.current = (this.current + 1) % this.total; }, 4500);
        }
    }"
    x-init="autoPlay()">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Customer Stories"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-white mb-12" />
    <x-dl.wrapper slug="__SLUG__" prefix="slides_wrapper"
        default-classes="relative min-h-[180px]">
        @foreach ($testimonials as $i => $t)
            <div x-show="current === {{ $i }}"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0">
                <x-dl.wrapper slug="__SLUG__" prefix="stars"
                    default-classes="flex justify-center text-primary text-2xl gap-1 mb-6">
                    ★★★★★
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="quote" tag="p"
                    default-classes="text-xl text-zinc-200 italic leading-relaxed">
                    "{{ $t['quote'] }}"
                </x-dl.wrapper>
                <x-dl.group slug="__SLUG__" prefix="author_wrapper"
                    default-classes="mt-8">
                    <x-dl.wrapper slug="__SLUG__" prefix="author_name"
                        default-classes="font-semibold text-white">
                        {{ $t['name'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="author_role"
                        default-classes="text-sm text-zinc-400 mt-1">
                        {{ $t['role'] }}
                    </x-dl.wrapper>
                </x-dl.group>
            </div>
        @endforeach
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="dots_wrapper"
        default-classes="flex items-center justify-center gap-2 mt-10">
        @foreach ($testimonials as $i => $t)
            <x-dl.wrapper slug="__SLUG__" prefix="dot" tag="button"
                default-classes="h-2 rounded-full transition-all duration-300"
                @click="current = {{ $i }}"
                :class="current === {{ $i }} ? 'bg-primary w-6' : 'bg-zinc-600 w-2'">
            </x-dl.wrapper>
        @endforeach
    </x-dl.wrapper>
</x-dl.section>
