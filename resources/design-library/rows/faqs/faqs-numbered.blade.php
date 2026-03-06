{{--
@name FAQs - Numbered
@description FAQ list with large numbered items for a bold visual style.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-4xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Questions & Answers"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-12" />
    <x-dl.grid slug="__SLUG__" prefix="faqs"
        default-grid-classes="space-y-10"
        default-items='[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required to start."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings. No questions asked."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans."},{"question":"What happens to my data if I cancel?","answer":"Your data is retained for 30 days after cancellation. You can export everything before that window closes."}]'>
        @dlItems('__SLUG__', 'faqs', $faqs, '[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required to start."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings. No questions asked."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans."},{"question":"What happens to my data if I cancel?","answer":"Your data is retained for 30 days after cancellation. You can export everything before that window closes."}]')
        @foreach ($faqs as $i => $faq)
            <x-dl.card slug="__SLUG__" prefix="faq_item"
                default-classes="flex gap-6">
                <x-dl.wrapper slug="__SLUG__" prefix="faq_number" tag="span"
                    default-classes="text-5xl font-black text-zinc-200 dark:text-zinc-700 shrink-0 leading-none">
                    {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                </x-dl.wrapper>
                <x-dl.group slug="__SLUG__" prefix="faq_content"
                    default-classes="flex-1">
                    <x-dl.wrapper slug="__SLUG__" prefix="faq_question" tag="h3"
                        default-classes="text-lg font-semibold text-zinc-900 dark:text-white mb-2">
                        {{ $faq['question'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="faq_answer" tag="p"
                        default-classes="text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ $faq['answer'] }}
                    </x-dl.wrapper>
                </x-dl.group>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
