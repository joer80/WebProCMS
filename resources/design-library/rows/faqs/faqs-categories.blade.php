{{--
@name FAQs - With Categories
@description Tabbed FAQ section with questions organized by category.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-4xl mx-auto"
    x-data="{ activeTab: 0 }">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Frequently Asked Questions"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Browse by topic or search below."
        default-classes="text-center text-lg text-zinc-500 dark:text-zinc-400 mb-10" />
    <x-dl.grid slug="__SLUG__" prefix="categories"
        default-grid-classes="flex flex-wrap gap-2 justify-center mb-10"
        default-items='[{"label":"General"},{"label":"Billing"},{"label":"Technical"},{"label":"Account"}]'>
        @dlItems('__SLUG__', 'categories', $categories, '[{"label":"General"},{"label":"Billing"},{"label":"Technical"},{"label":"Account"}]')
        @foreach ($categories as $i => $cat)
            <x-dl.card slug="__SLUG__" prefix="tab_button" tag="button"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="px-4 py-2 rounded-full text-sm font-medium transition-colors"
                @click="activeTab = {{ $i }}"
                x-bind:class="activeTab === {{ $i }} ? 'bg-primary text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:border-primary hover:text-primary'">
                {{ $cat['label'] }}
            </x-dl.card>
        @endforeach
    </x-dl.grid>
    <x-dl.grid slug="__SLUG__" prefix="faqs"
        default-grid-classes=""
        default-items='[{"question":"How do I get started?","answer":"Sign up for a free account and follow the onboarding wizard.","category":"0"},{"question":"What is your refund policy?","answer":"We offer a 30-day money-back guarantee on all paid plans.","category":"1"},{"question":"How do I reset my API key?","answer":"Go to Settings → API and click Regenerate Key.","category":"2"},{"question":"How do I update my email?","answer":"Go to Settings → Profile to update your email address.","category":"3"},{"question":"Is there a free trial?","answer":"Yes, 14 days free on all plans. No credit card required.","category":"0"},{"question":"How do I cancel my subscription?","answer":"You can cancel anytime from Settings → Billing.","category":"1"}]'>
        @dlItems('__SLUG__', 'faqs', $faqs, '[{"question":"How do I get started?","answer":"Sign up for a free account and follow the onboarding wizard.","category":"0"},{"question":"What is your refund policy?","answer":"We offer a 30-day money-back guarantee on all paid plans.","category":"1"},{"question":"How do I reset my API key?","answer":"Go to Settings → API and click Regenerate Key.","category":"2"},{"question":"How do I update my email?","answer":"Go to Settings → Profile to update your email address.","category":"3"},{"question":"Is there a free trial?","answer":"Yes, 14 days free on all plans. No credit card required.","category":"0"},{"question":"How do I cancel my subscription?","answer":"You can cancel anytime from Settings → Billing.","category":"1"}]')
        @foreach ($categories as $i => $cat)
            <div x-show="activeTab === {{ $i }}">
                <x-dl.wrapper slug="__SLUG__" prefix="faq_list"
                    default-classes="divide-y divide-zinc-200 dark:divide-zinc-700 rounded-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    @foreach ($faqs as $j => $faq)
                        @if (($faq['category'] ?? '0') === (string) $i)
                            <x-dl.card slug="__SLUG__" prefix="faq_item"
                                data-editor-item-index="{{ $loop->index }}"
                                default-classes="p-6">
                                <x-dl.wrapper slug="__SLUG__" prefix="faq_question" tag="h3"
                                    default-classes="text-base font-semibold text-zinc-900 dark:text-white mb-2">
                                    {{ $faq['question'] }}
                                </x-dl.wrapper>
                                <x-dl.wrapper slug="__SLUG__" prefix="faq_answer" tag="p"
                                    default-classes="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    {{ $faq['answer'] }}
                                </x-dl.wrapper>
                            </x-dl.card>
                        @endif
                    @endforeach
                </x-dl.wrapper>
            </div>
        @endforeach
    </x-dl.grid>
</x-dl.section>
