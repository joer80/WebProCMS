{{--
@name Slider - Testimonial
@description Alpine.js testimonial slider with navigation dots and auto-advance.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-zinc-50 dark:bg-zinc-950 overflow-hidden'); @endphp
<section class="{{ $sectionClasses }}"
    x-data="{
        current: 0,
        total: 3,
        autoPlay() {
            setInterval(() => { this.current = (this.current + 1) % this.total; }, 4000);
        }
    }"
    x-init="autoPlay()"
>
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-3xl mx-auto text-center'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        <x-dl-heading slug="__SLUG__" prefix="headline" default="What Our Customers Say"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-12" />

        @php $quoteClasses = content('__SLUG__', 'quote_classes', 'text-xl text-zinc-700 dark:text-zinc-300 italic leading-relaxed'); @endphp
        @php $authorWrapperClasses = content('__SLUG__', 'author_wrapper_classes', 'mt-6'); @endphp
        @php $authorNameClasses = content('__SLUG__', 'author_name_classes', 'font-semibold text-zinc-900 dark:text-white'); @endphp
        @php $authorRoleClasses = content('__SLUG__', 'author_role_classes', 'text-sm text-zinc-500 dark:text-zinc-400'); @endphp
        @php $dotsWrapperClasses = content('__SLUG__', 'dots_wrapper_classes', 'flex items-center justify-center gap-2 mt-8'); @endphp
        @php $dotBaseClasses = content('__SLUG__', 'dot_base_classes', 'h-2 rounded-full transition-all duration-300'); @endphp
        @php $slidesWrapperClasses = content('__SLUG__', 'slides_wrapper_classes', 'relative'); @endphp

        <div class="{{ $slidesWrapperClasses }}">
            @foreach ([['quote' => 'This is honestly the best tool I\'ve used in years. It transformed how we work.', 'name' => 'Alex Thompson', 'role' => 'Founder at StartupXYZ'], ['quote' => 'The onboarding was seamless and the results were immediate. I highly recommend it.', 'name' => 'Jamie Rivera', 'role' => 'VP of Engineering'], ['quote' => 'Customer support is incredible. They went above and beyond to help our team.', 'name' => 'Morgan Lee', 'role' => 'Director of Operations']] as $i => $t)
                <div x-show="current === {{ $i }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <p class="{{ $quoteClasses }}">"{{ $t['quote'] }}"</p>
                    <div class="{{ $authorWrapperClasses }}">
                        <div class="{{ $authorNameClasses }}">{{ $t['name'] }}</div>
                        <div class="{{ $authorRoleClasses }}">{{ $t['role'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="{{ $dotsWrapperClasses }}">
            @for ($i = 0; $i < 3; $i++)
                <button
                    @click="current = {{ $i }}"
                    :class="current === {{ $i }} ? 'bg-primary w-6' : 'bg-zinc-300 dark:bg-zinc-600 w-2'"
                    class="{{ $dotBaseClasses }}"
                ></button>
            @endfor
        </div>
    </div>
</section>
