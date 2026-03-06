{{--
@name FAQs - Cards
@description FAQ items displayed as standalone bordered cards in a grid.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.wrapper slug="__SLUG__" prefix="header_wrapper"
        default-classes="text-center mb-12">
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Frequently Asked Questions"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Find answers to the most common questions."
            default-classes="text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="faqs"
        default-grid-classes="grid md:grid-cols-3 gap-6"
        default-items='[{"question":"How do I get started?","answer":"Sign up for a free account and follow the onboarding wizard. Be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel anytime?","answer":"Absolutely. Cancel your subscription at any time from account settings."},{"question":"Is my data secure?","answer":"Yes, we use AES-256 encryption and are SOC 2 Type II certified."},{"question":"Do you offer support?","answer":"Email support on all plans, with priority support on Pro and Enterprise."},{"question":"Can I export my data?","answer":"Export all data as CSV or JSON at any time from your account settings."}]'>
        @dlItems('__SLUG__', 'faqs', $faqs, '[{"question":"How do I get started?","answer":"Sign up for a free account and follow the onboarding wizard. Be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel anytime?","answer":"Absolutely. Cancel your subscription at any time from account settings."},{"question":"Is my data secure?","answer":"Yes, we use AES-256 encryption and are SOC 2 Type II certified."},{"question":"Do you offer support?","answer":"Email support on all plans, with priority support on Pro and Enterprise."},{"question":"Can I export my data?","answer":"Export all data as CSV or JSON at any time from your account settings."}]')
        @foreach ($faqs as $faq)
            <x-dl.card slug="__SLUG__" prefix="faq_card"
                default-classes="bg-white dark:bg-zinc-900 rounded-card p-6 border border-zinc-200 dark:border-zinc-700 shadow-card">
                <x-dl.icon slug="__SLUG__" prefix="faq_icon" name="question-mark-circle"
                    default-wrapper-classes="mb-3 text-primary"
                    default-classes="size-6" />
                <x-dl.wrapper slug="__SLUG__" prefix="faq_question" tag="h3"
                    default-classes="text-base font-semibold text-zinc-900 dark:text-white mb-2">
                    {{ $faq['question'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="faq_answer" tag="p"
                    default-classes="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                    {{ $faq['answer'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
