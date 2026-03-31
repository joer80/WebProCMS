{{--
@name CTA - Two Column
@description Two-column CTA with headline on the left and benefits list with button on the right.
@sort 100
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Everything You Need to Succeed"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white leading-tight" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Our platform gives you all the tools, support, and resources to grow faster."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </div>
    <div>
        <x-dl.grid slug="__SLUG__" prefix="benefits"
            default-grid-classes="space-y-4 mb-8"
            default-items='[{"icon":"check-circle","text":"14-day free trial, no credit card required"},{"icon":"check-circle","text":"Onboarding support included with every plan"},{"icon":"check-circle","text":"Scales seamlessly as your business grows"},{"icon":"check-circle","text":"Cancel anytime, no questions asked"}]'>
            @dlItems('__SLUG__', 'benefits', $benefits, '[{"icon":"check-circle","text":"14-day free trial, no credit card required"},{"icon":"check-circle","text":"Onboarding support included with every plan"},{"icon":"check-circle","text":"Scales seamlessly as your business grows"},{"icon":"check-circle","text":"Cancel anytime, no questions asked"}]')
            @foreach ($benefits as $benefit)
                <x-dl.card slug="__SLUG__" prefix="benefit_item"
            data-editor-item-index="{{ $loop->index }}"
                    default-classes="flex items-center gap-3">
                    <x-dl.icon slug="__SLUG__" prefix="benefit_icon" name="{{ $benefit['icon'] }}"
                        default-classes="size-5 shrink-0 text-primary" />
                    <x-dl.wrapper slug="__SLUG__" prefix="benefit_text" tag="span"
                        default-classes="text-zinc-700 dark:text-zinc-300">
                        {{ $benefit['text'] }}
                    </x-dl.wrapper>
                </x-dl.card>
            @endforeach
        </x-dl.grid>
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="flex flex-wrap gap-4"
            default-primary-label="Get Started Free"
            default-primary-classes="px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="Learn More"
            default-secondary-classes="px-8 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
    </div>
</x-dl.section>
