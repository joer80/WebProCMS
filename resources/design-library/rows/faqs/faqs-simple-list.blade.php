{{--
@name FAQs - Simple List
@description Plain list of questions and answers with no accordion.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Common Questions"
        default-tag="h2"
        default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-10" />
    <x-dl.grid slug="__SLUG__" prefix="faqs"
        default-grid-classes="space-y-8"
        default-items='[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings. No questions asked."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans."},{"question":"What integrations are available?","answer":"We integrate with hundreds of tools including Slack, Zapier, Stripe, and more. See our integrations page for the full list."}]'>
        @dlItems('__SLUG__', 'faqs', $faqs, '[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings. No questions asked."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans."},{"question":"What integrations are available?","answer":"We integrate with hundreds of tools including Slack, Zapier, Stripe, and more. See our integrations page for the full list."}]')
        @foreach ($faqs as $faq)
            <x-dl.card slug="__SLUG__" prefix="faq_item"
                default-classes="">
                <x-dl.wrapper slug="__SLUG__" prefix="faq_question" tag="h3"
                    default-classes="text-base font-semibold text-zinc-900 dark:text-white mb-2">
                    {{ $faq['question'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="faq_answer" tag="p"
                    default-classes="text-zinc-500 dark:text-zinc-400 leading-relaxed">
                    {{ $faq['answer'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
