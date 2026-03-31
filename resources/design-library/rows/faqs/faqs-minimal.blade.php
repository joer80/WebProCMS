{{--
@name FAQs - Minimal
@description Ultra-clean FAQ with thin border separators and no decoration.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-2xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="FAQ"
        default-tag="h2"
        default-classes="font-heading text-3xl font-bold text-zinc-900 dark:text-white mb-8" />
    <x-dl.grid slug="__SLUG__" prefix="faqs"
        default-grid-classes="divide-y divide-zinc-100 dark:divide-zinc-800"
        default-items='[{"question":"How do I get started?","answer":"Sign up for a free account and follow the onboarding wizard."},{"question":"Is there a free trial?","answer":"Yes, 14 days free. No credit card required."},{"question":"Can I cancel at any time?","answer":"Yes, cancel anytime from account settings."},{"question":"Do you offer customer support?","answer":"Email support on all plans. Live chat on Pro+."},{"question":"Is my data secure?","answer":"Yes. AES-256 encryption, SOC 2 Type II certified."},{"question":"What payment methods?","answer":"All major credit cards and PayPal accepted."}]'>
        @dlItems('__SLUG__', 'faqs', $faqs, '[{"question":"How do I get started?","answer":"Sign up for a free account and follow the onboarding wizard."},{"question":"Is there a free trial?","answer":"Yes, 14 days free. No credit card required."},{"question":"Can I cancel at any time?","answer":"Yes, cancel anytime from account settings."},{"question":"Do you offer customer support?","answer":"Email support on all plans. Live chat on Pro+."},{"question":"Is my data secure?","answer":"Yes. AES-256 encryption, SOC 2 Type II certified."},{"question":"What payment methods?","answer":"All major credit cards and PayPal accepted."}]')
        @foreach ($faqs as $faq)
            <x-dl.card slug="__SLUG__" prefix="faq_item"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="py-6">
                <x-dl.wrapper slug="__SLUG__" prefix="faq_question" tag="h3"
                    default-classes="text-sm font-semibold text-zinc-900 dark:text-white mb-1.5 uppercase tracking-wide">
                    {{ $faq['question'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="faq_answer" tag="p"
                    default-classes="text-zinc-500 dark:text-zinc-400">
                    {{ $faq['answer'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
