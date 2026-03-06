{{--
@name Pricing - With FAQ
@description Pricing card alongside common pricing questions.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Pricing & Questions"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Everything you need to know before you sign up."
        default-classes="text-center text-lg text-zinc-500 dark:text-zinc-400 mb-12" />
    <x-dl.wrapper slug="__SLUG__" prefix="columns_wrapper"
        default-classes="grid md:grid-cols-2 gap-12 items-start">
        <x-dl.wrapper slug="__SLUG__" prefix="price_panel"
            default-classes="rounded-card p-8 bg-primary text-white">
            <x-dl.wrapper slug="__SLUG__" prefix="plan_name" tag="h3"
                default-classes="text-xl font-bold text-white">
                Pro Plan
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="plan_price"
                default-classes="mt-4 text-5xl font-black text-white">
                $29<x-dl.wrapper slug="__SLUG__" prefix="plan_period" tag="span"
                    default-classes="text-base font-normal text-white/70">/month</x-dl.wrapper>
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="plan_desc" tag="p"
                default-classes="mt-3 text-white/80">
                Everything you need to grow your business. Cancel anytime.
            </x-dl.wrapper>
            <x-dl.grid slug="__SLUG__" prefix="plan_features"
                default-grid-classes="mt-6 space-y-3"
                default-items='[{"feature":"Unlimited projects"},{"feature":"100GB storage"},{"feature":"Priority email support"},{"feature":"Advanced analytics"},{"feature":"API access"},{"feature":"Team collaboration"}]'>
                @dlItems('__SLUG__', 'plan_features', $planFeatures, '[{"feature":"Unlimited projects"},{"feature":"100GB storage"},{"feature":"Priority email support"},{"feature":"Advanced analytics"},{"feature":"API access"},{"feature":"Team collaboration"}]')
                @foreach ($planFeatures as $item)
                    <x-dl.card slug="__SLUG__" prefix="plan_feature_item"
                        default-classes="flex items-center gap-2 text-sm text-white/90">
                        <x-dl.icon slug="__SLUG__" prefix="plan_feature_icon" name="check"
                            default-classes="size-4 shrink-0 text-white" />
                        {{ $item['feature'] }}
                    </x-dl.card>
                @endforeach
            </x-dl.grid>
            <x-dl.wrapper slug="__SLUG__" prefix="plan_cta" tag="a"
                href="#"
                default-classes="mt-8 block text-center px-4 py-3 rounded-lg font-semibold text-sm bg-white text-primary hover:bg-zinc-100 transition-colors">
                Start Free Trial
            </x-dl.wrapper>
        </x-dl.wrapper>
        <x-dl.accordion slug="__SLUG__" prefix="faqs"
            default-wrapper-classes="divide-y divide-zinc-200 dark:divide-zinc-700"
            default-items='[{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required to start."},{"question":"Can I change plans later?","answer":"Absolutely. You can upgrade or downgrade your plan at any time. Changes take effect immediately."},{"question":"What payment methods do you accept?","answer":"We accept all major credit cards (Visa, Mastercard, Amex) and PayPal."},{"question":"Do you offer refunds?","answer":"Yes, we offer a 30-day money-back guarantee. If you are not satisfied, contact us for a full refund."},{"question":"Is my data secure?","answer":"We use industry-standard encryption and are SOC 2 Type II certified. Your data is always safe."}]'>
            @dlItems('__SLUG__', 'faqs', $faqs, '[{"question":"Is there a free trial?","answer":"Yes! All plans come with a 14-day free trial. No credit card required to start."},{"question":"Can I change plans later?","answer":"Absolutely. You can upgrade or downgrade your plan at any time. Changes take effect immediately."},{"question":"What payment methods do you accept?","answer":"We accept all major credit cards (Visa, Mastercard, Amex) and PayPal."},{"question":"Do you offer refunds?","answer":"Yes, we offer a 30-day money-back guarantee. If you are not satisfied, contact us for a full refund."},{"question":"Is my data secure?","answer":"We use industry-standard encryption and are SOC 2 Type II certified. Your data is always safe."}]')
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
