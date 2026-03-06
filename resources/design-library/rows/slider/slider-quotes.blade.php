{{--
@name Slider - Quote Rotator
@description Simple rotating quote display with fade transition.
@sort 100
--}}
@dlItems('__SLUG__', 'quotes', $quotes, '[{"quote":"The best tool our team has ever used. Period.","author":"CEO at Acme"},{"quote":"Transformed how we collaborate. Wish we found it sooner.","author":"CTO at TechCorp"},{"quote":"Worth every penny. The ROI was clear within the first week.","author":"Director at BuildIt"}]')
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto text-center"
    x-data="{
        current: 0,
        total: {{ count($quotes) }},
        autoPlay() {
            setInterval(() => { this.current = (this.current + 1) % this.total; }, 5000);
        }
    }"
    x-init="autoPlay()">
    <x-dl.wrapper slug="__SLUG__" prefix="quotes_wrapper"
        default-classes="relative min-h-[160px]">
        @foreach ($quotes as $i => $quote)
            <div x-show="current === {{ $i }}"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <x-dl.wrapper slug="__SLUG__" prefix="quote_text" tag="blockquote"
                    default-classes="text-2xl text-zinc-700 dark:text-zinc-200 italic font-medium leading-relaxed">
                    "{{ $quote['quote'] }}"
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="quote_author" tag="cite"
                    default-classes="block mt-6 text-sm font-semibold text-zinc-500 dark:text-zinc-400 not-italic">
                    — {{ $quote['author'] }}
                </x-dl.wrapper>
            </div>
        @endforeach
    </x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="dots_wrapper"
        default-classes="flex items-center justify-center gap-2 mt-8">
        @foreach ($quotes as $i => $quote)
            <x-dl.wrapper slug="__SLUG__" prefix="dot" tag="button"
                default-classes="h-2 rounded-full transition-all duration-300"
                @click="current = {{ $i }}"
                :class="current === {{ $i }} ? 'bg-primary w-6' : 'bg-zinc-300 dark:bg-zinc-600 w-2'">
            </x-dl.wrapper>
        @endforeach
    </x-dl.wrapper>
</x-dl.section>
