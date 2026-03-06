{{--
@name FAQs - With Contact
@description Accordion FAQ with a contact support card below.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Frequently Asked Questions"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Find answers to common questions below."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.accordion slug="__SLUG__" prefix="faqs"
        default-wrapper-classes="divide-y divide-zinc-200 dark:divide-zinc-700"
        default-items='[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings. No questions asked."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans."},{"question":"What integrations are available?","answer":"We integrate with hundreds of tools including Slack, Zapier, Stripe, and more."}]'>
        @dlItems('__SLUG__', 'faqs', $faqs, '[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings. No questions asked."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans."},{"question":"What integrations are available?","answer":"We integrate with hundreds of tools including Slack, Zapier, Stripe, and more."}]')
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
    <x-dl.wrapper slug="__SLUG__" prefix="contact_card"
        default-classes="mt-12 rounded-card bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 p-8 text-center">
        <x-dl.icon slug="__SLUG__" prefix="contact_icon" name="chat-bubble-left-right"
            default-wrapper-classes="mb-4 text-primary"
            default-classes="size-8 mx-auto" />
        <x-dl.wrapper slug="__SLUG__" prefix="contact_title" tag="h3"
            default-classes="text-lg font-semibold text-zinc-900 dark:text-white mb-2">
            Still have questions?
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="contact_desc" tag="p"
            default-classes="text-zinc-500 dark:text-zinc-400 mb-6">
            Our support team is ready to help. Reach out anytime.
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="contact_cta" tag="a"
            href="/contact"
            default-classes="inline-block px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
            Contact Support
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
