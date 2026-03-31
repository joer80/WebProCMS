{{--
@name Pricing - Simple
@description Single centered pricing card with full feature list.
@sort 70
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-lg mx-auto text-center">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="One Plan, Everything Included"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="No hidden fees. No complicated tiers. Just everything you need."
        default-classes="text-lg text-zinc-500 dark:text-zinc-400 mb-10" />
    <x-dl.wrapper slug="__SLUG__" prefix="price_card"
        default-classes="rounded-card border border-zinc-200 dark:border-zinc-700 p-10 bg-zinc-50 dark:bg-zinc-800">
        <x-dl.wrapper slug="__SLUG__" prefix="price_display"
            default-classes="text-6xl font-black text-zinc-900 dark:text-white">
            $29<x-dl.wrapper slug="__SLUG__" prefix="price_period" tag="span"
                default-classes="text-xl font-normal text-zinc-400">/month</x-dl.wrapper>
        </x-dl.wrapper>
        <x-dl.wrapper slug="__SLUG__" prefix="price_note" tag="p"
            default-classes="mt-2 text-sm text-zinc-400">
            Billed monthly. Cancel anytime.
        </x-dl.wrapper>
        <x-dl.grid slug="__SLUG__" prefix="features"
            default-grid-classes="mt-8 grid grid-cols-2 gap-3 text-left"
            default-items='[{"feature":"Unlimited projects"},{"feature":"100GB storage"},{"feature":"Priority support"},{"feature":"Team collaboration"},{"feature":"Advanced analytics"},{"feature":"Custom domain"},{"feature":"API access"},{"feature":"White-label exports"}]'>
            @dlItems('__SLUG__', 'features', $features, '[{"feature":"Unlimited projects"},{"feature":"100GB storage"},{"feature":"Priority support"},{"feature":"Team collaboration"},{"feature":"Advanced analytics"},{"feature":"Custom domain"},{"feature":"API access"},{"feature":"White-label exports"}]')
            @foreach ($features as $item)
                <x-dl.card slug="__SLUG__" prefix="feature_item"
                    data-editor-item-index="{{ $loop->index }}"
                    default-classes="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                    <x-dl.icon slug="__SLUG__" prefix="feature_icon" name="check-circle:solid"
                        default-classes="size-4 shrink-0 text-primary" />
                    {{ $item['feature'] }}
                </x-dl.card>
            @endforeach
        </x-dl.grid>
        <x-dl.buttons slug="__SLUG__"
            default-wrapper-classes="mt-10 flex flex-col gap-3"
            default-primary-label="Get Started"
            default-primary-classes="w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="Start free trial"
            default-secondary-classes="w-full px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors" />
    </x-dl.wrapper>
</x-dl.section>
