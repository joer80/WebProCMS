{{--
@name CTA - With Features
@description Two-column call-to-action with text and buttons on the left, feature highlights on the right.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
    <div>
        <x-dl.heading slug="__SLUG__" prefix="headline" default="Everything You Need to Succeed"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Our platform gives you the tools to grow your business faster."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-8 flex flex-wrap items-center gap-4"
            default-primary-label="Start Free Trial"
            default-primary-classes="px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="See Pricing"
            default-secondary-classes="px-8 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
    </div>
    <x-dl.grid slug="__SLUG__" prefix="features"
        default-grid-classes="space-y-4"
        default-items='[{"icon":"check-circle","title":"No credit card required"},{"icon":"check-circle","title":"14-day free trial"},{"icon":"check-circle","title":"Cancel anytime"},{"icon":"check-circle","title":"Free onboarding support"}]'>
        @dlItems('__SLUG__', 'features', $features, '[{"icon":"check-circle","title":"No credit card required"},{"icon":"check-circle","title":"14-day free trial"},{"icon":"check-circle","title":"Cancel anytime"},{"icon":"check-circle","title":"Free onboarding support"}]')
        @foreach ($features as $feature)
            <x-dl.card slug="__SLUG__" prefix="feature_item"
            data-editor-item-index="{{ $loop->index }}"
                default-classes="flex items-center gap-3">
                <x-dl.icon slug="__SLUG__" prefix="feature_icon" name="{{ $feature['icon'] }}"
                    default-classes="size-5 text-primary shrink-0" />
                <x-dl.wrapper slug="__SLUG__" prefix="feature_title" tag="span"
                    default-classes="text-zinc-700 dark:text-zinc-300 font-medium">
                    {{ $feature['title'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
