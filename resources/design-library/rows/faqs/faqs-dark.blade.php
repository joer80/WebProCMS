{{--
@name FAQs - Dark
@description Dark background accordion FAQ section.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Got Questions?"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="We have answers."
        default-classes="text-center text-lg text-zinc-400 mb-12" />
    <x-dl.accordion slug="__SLUG__" prefix="faqs"
        default-wrapper-classes="divide-y divide-zinc-700"
        default-items='[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans."}]'>
        @dlItems('__SLUG__', 'faqs', $faqs, '[{"question":"How do I get started?","answer":"Simply sign up for a free account and follow the onboarding wizard. You can be up and running in under 5 minutes."},{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required."},{"question":"Can I cancel at any time?","answer":"Absolutely. You can cancel your subscription at any time from your account settings."},{"question":"Do you offer customer support?","answer":"We offer email support on all plans, with priority support and live chat available on Pro and Enterprise plans."}]')
        @foreach ($faqs as $i => $faq)
            <x-dl.accordion-item slug="__SLUG__" prefix="faq_item" :index="$i"
                question="{{ $faq['question'] }}"
                default-classes="py-5"
                default-button-classes="w-full flex items-center justify-between text-left"
                default-question-classes="text-base font-semibold text-white"
                default-chevron-classes="size-5 text-zinc-500 shrink-0 transition-transform duration-200"
                default-answer-classes="mt-3 text-zinc-400 text-sm leading-relaxed">
                {{ $faq['answer'] }}
            </x-dl.accordion-item>
        @endforeach
    </x-dl.accordion>
</x-dl.section>
