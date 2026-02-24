{{--
@name FAQs - Accordion
@description Alpine.js-powered accordion FAQ section with expand/collapse.
@sort 10
--}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white">Frequently Asked Questions</h2>
            <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">Can't find what you're looking for? <a href="/contact" class="text-primary underline">Contact us</a>.</p>
        </div>
        <div class="divide-y divide-zinc-200 dark:divide-zinc-700" x-data="{ open: null }">
            @foreach ([['q' => 'How do I get started?', 'a' => 'Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes.'], ['q' => 'Is there a free trial?', 'a' => 'Yes! All plans come with a 14-day free trial. No credit card required.'], ['q' => 'Can I cancel at any time?', 'a' => 'Absolutely. You can cancel your subscription at any time from your account settings. No questions asked.'], ['q' => 'Do you offer customer support?', 'a' => 'We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans.']] as $i => $faq)
                <div x-data class="py-5">
                    <button
                        @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                        class="w-full flex items-center justify-between text-left"
                    >
                        <span class="text-base font-semibold text-zinc-900 dark:text-white">{{ $faq['q'] }}</span>
                        <svg
                            :class="open === {{ $i }} ? 'rotate-180' : ''"
                            class="size-5 text-zinc-400 shrink-0 transition-transform duration-200"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse class="mt-3 text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
