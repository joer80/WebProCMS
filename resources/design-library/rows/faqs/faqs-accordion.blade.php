{{--
@name FAQs - Accordion
@description Alpine.js-powered accordion FAQ section with expand/collapse.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
        <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
            default-classes="text-center mb-16">
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Frequently Asked Questions"
                default-tag="h2"
                default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Can't find what you're looking for?"
                default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
            <x-dl.link slug="__SLUG__" prefix="contact"
                default-label="Contact us"
                default-url="/contact"
                default-classes="text-primary underline" />
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="accordion"
            default-classes="divide-y divide-zinc-200 dark:divide-zinc-700"
            x-data="{ open: null }">
            @foreach ([['q' => 'How do I get started?', 'a' => 'Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes.'], ['q' => 'Is there a free trial?', 'a' => 'Yes! All plans come with a 14-day free trial. No credit card required.'], ['q' => 'Can I cancel at any time?', 'a' => 'Absolutely. You can cancel your subscription at any time from your account settings. No questions asked.'], ['q' => 'Do you offer customer support?', 'a' => 'We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans.']] as $i => $faq)
                <x-dl.wrapper slug="__SLUG__" prefix="item"
                    default-classes="py-5"
                    x-data>
                    <x-dl.wrapper slug="__SLUG__" prefix="question_button" tag="button"
                        default-classes="w-full flex items-center justify-between text-left"
                        @click="open === {{ $i }} ? open = null : open = {{ $i }}">
                        <x-dl.wrapper slug="__SLUG__" prefix="question_text" tag="span"
                            default-classes="text-base font-semibold text-zinc-900 dark:text-white">
                            {{ $faq['q'] }}
                        </x-dl.wrapper>
                        <x-dl.wrapper slug="__SLUG__" prefix="chevron" tag="svg"
                            default-classes="size-5 text-zinc-400 shrink-0 transition-transform duration-200"
                            :class="open === {{ $i }} ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </x-dl.wrapper>
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="answer"
                        default-classes="mt-3 text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed"
                        x-show="open === {{ $i }}" x-collapse>
                        {{ $faq['a'] }}
                    </x-dl.wrapper>
                </x-dl.wrapper>
            @endforeach
        </x-dl.wrapper>
</x-dl.section>
