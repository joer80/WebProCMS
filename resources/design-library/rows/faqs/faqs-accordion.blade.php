{{--
@name FAQs - Accordion
@description Alpine.js-powered accordion FAQ section with expand/collapse.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-3xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $headerWrapperClasses = content('__SLUG__', 'header_wrapper_classes', 'text-center mb-16'); @endphp
        @php $accordionClasses = content('__SLUG__', 'accordion_classes', 'divide-y divide-zinc-200 dark:divide-zinc-700'); @endphp
        @php $itemClasses = content('__SLUG__', 'item_classes', 'py-5'); @endphp
        @php $questionButtonClasses = content('__SLUG__', 'question_button_classes', 'w-full flex items-center justify-between text-left'); @endphp
        @php $questionTextClasses = content('__SLUG__', 'question_text_classes', 'text-base font-semibold text-zinc-900 dark:text-white'); @endphp
        @php $chevronClasses = content('__SLUG__', 'chevron_classes', 'size-5 text-zinc-400 shrink-0 transition-transform duration-200'); @endphp
        @php $answerClasses = content('__SLUG__', 'answer_classes', 'mt-3 text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed'); @endphp
        @php $contactLinkClasses = content('__SLUG__', 'contact_link_classes', 'text-primary underline'); @endphp
        <div class="{{ $headerWrapperClasses }}">
            @php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
            @if($toggleHeadline)
            @php $headlineText = content('__SLUG__', 'headline', 'Frequently Asked Questions'); @endphp
            @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
            <h2 class="{{ $headlineClasses }}">{{ $headlineText }}</h2>
            @endif
            @php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
            @if($toggleSubheadline)
            @php $subheadlineText = content('__SLUG__', 'subheadline', 'Can\'t find what you\'re looking for?'); @endphp
            @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400'); @endphp
            <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }} <a href="{{ content('__SLUG__', 'contact_url', '/contact') }}" class="{{ $contactLinkClasses }}">{{ content('__SLUG__', 'contact_cta', 'Contact us') }}</a>.</p>
            @endif
        </div>
        <div class="{{ $accordionClasses }}" x-data="{ open: null }">
            @foreach ([['q' => 'How do I get started?', 'a' => 'Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes.'], ['q' => 'Is there a free trial?', 'a' => 'Yes! All plans come with a 14-day free trial. No credit card required.'], ['q' => 'Can I cancel at any time?', 'a' => 'Absolutely. You can cancel your subscription at any time from your account settings. No questions asked.'], ['q' => 'Do you offer customer support?', 'a' => 'We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans.']] as $i => $faq)
                <div x-data class="{{ $itemClasses }}">
                    <button
                        @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                        class="{{ $questionButtonClasses }}"
                    >
                        <span class="{{ $questionTextClasses }}">{{ $faq['q'] }}</span>
                        <svg
                            :class="open === {{ $i }} ? 'rotate-180' : ''"
                            class="{{ $chevronClasses }}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse class="{{ $answerClasses }}">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
