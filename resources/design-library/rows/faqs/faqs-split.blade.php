{{--
@name FAQs - Split
@description Heading and contact CTA on the left, accordion list on the right.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="columns_wrapper"
        default-classes="grid md:grid-cols-2 gap-12 items-start">
        <x-dl.wrapper slug="__SLUG__" prefix="left_panel"
            default-classes="">
            <x-dl.heading slug="__SLUG__" prefix="headline" default="Frequently Asked Questions"
                default-tag="h2"
                default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
            <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Can't find what you're looking for? Our support team is happy to help."
                default-classes="text-lg text-zinc-500 dark:text-zinc-400 mb-6" />
            <x-dl.wrapper slug="__SLUG__" prefix="contact_link" tag="a"
                href="/contact"
                default-classes="inline-flex items-center gap-2 text-primary font-semibold hover:text-primary/80 transition-colors">
                <x-dl.icon slug="__SLUG__" prefix="contact_icon" name="chat-bubble-left-right"
                    default-classes="size-5" />
                Contact Support
            </x-dl.wrapper>
        </x-dl.wrapper>
        <x-dl.accordion slug="__SLUG__" prefix="faqs"
            default-wrapper-classes="divide-y divide-zinc-200 dark:divide-zinc-700"
            default-items='[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support on Pro and Enterprise plans."},{"question":"What happens to my data if I cancel?","answer":"Your data is retained for 30 days after cancellation. Export everything before that window closes."}]'>
            @dlItems('__SLUG__', 'faqs', $faqs, '[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support on Pro and Enterprise plans."},{"question":"What happens to my data if I cancel?","answer":"Your data is retained for 30 days after cancellation. Export everything before that window closes."}]')
            @foreach ($faqs as $i => $faq)
                <x-dl.accordion-item slug="__SLUG__" prefix="faq_item" :index="$i"
                    question="{{ $faq['question'] }}"
                    default-classes="py-5"
                    default-button-classes="w-full flex items-center justify-between text-left"
                    default-question-classes="text-base font-semibold text-zinc-900 dark:text-white"
                    default-chevron-classes="size-5 text-zinc-400 shrink-0 transition-transform duration-200"
                    default-answer-classes="mt-3 text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">
                    {{ $faq['answer'] }}
                </x-dl.accordion-item>
            @endforeach
        </x-dl.accordion>
    </x-dl.wrapper>
</x-dl.section>
