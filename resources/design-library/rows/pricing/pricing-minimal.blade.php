{{--
@name Pricing - Minimal
@description Clean, text-focused pricing with minimal borders.
@sort 50
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Simple Pricing"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="One plan. Everything included."
        default-classes="text-center text-lg text-zinc-500 dark:text-zinc-400 mb-16" />
    <x-dl.grid slug="__SLUG__" prefix="plans"
        default-grid-classes="divide-y divide-zinc-200 dark:divide-zinc-700"
        default-items='[{"name":"Monthly","price":"$29","desc":"Billed every month","features":"All features|Unlimited projects|Priority support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Yearly","price":"$19","desc":"Billed annually — save 34%","features":"All features|Unlimited projects|Priority support|2 months free","cta":"Get Started","cta_url":"#","toggle_featured":"1"}]'>
        @dlItems('__SLUG__', 'plans', $plans, '[{"name":"Monthly","price":"$29","desc":"Billed every month","features":"All features|Unlimited projects|Priority support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Yearly","price":"$19","desc":"Billed annually — save 34%","features":"All features|Unlimited projects|Priority support|2 months free","cta":"Get Started","cta_url":"#","toggle_featured":"1"}]')
        @foreach ($plans as $plan)
            @php $isFeatured = !empty($plan['toggle_featured']); @endphp
            <x-dl.card slug="__SLUG__" prefix="plan_row" :featured="$isFeatured"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="py-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6"
                default-featured-classes="py-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 bg-primary/5 dark:bg-primary/10 -mx-6 px-6 rounded-card">
                <x-dl.group slug="__SLUG__" prefix="plan_info"
                    default-classes="flex-1">
                    <x-dl.wrapper slug="__SLUG__" prefix="plan_name" tag="h3" :featured="$isFeatured"
                        default-classes="text-xl font-semibold text-zinc-900 dark:text-white"
                        default-featured-classes="text-xl font-semibold text-zinc-900 dark:text-white">
                        {{ $plan['name'] }}
                        @if ($isFeatured)
                            <x-dl.wrapper slug="__SLUG__" prefix="plan_badge" tag="span"
                                default-classes="ml-2 text-xs font-medium bg-primary text-white px-2 py-0.5 rounded-full">
                                Best Value
                            </x-dl.wrapper>
                        @endif
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="plan_desc" tag="p"
                        default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $plan['desc'] }}
                    </x-dl.wrapper>
                    <x-dl.group slug="__SLUG__" prefix="plan_features_list" tag="ul"
                        default-classes="mt-3 flex flex-wrap gap-x-4 gap-y-1">
                        @foreach (array_filter(array_map('trim', explode('|', $plan['features'] ?? ''))) as $feature)
                            <x-dl.wrapper slug="__SLUG__" prefix="plan_feature_item" tag="li"
                                default-classes="flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400">
                                <x-dl.icon slug="__SLUG__" prefix="plan_feature_icon" name="check"
                                    default-classes="size-3 text-primary shrink-0" />
                                {{ $feature }}
                            </x-dl.wrapper>
                        @endforeach
                    </x-dl.group>
                </x-dl.group>
                <x-dl.group slug="__SLUG__" prefix="plan_right"
                    default-classes="flex items-center gap-6 shrink-0">
                    <x-dl.wrapper slug="__SLUG__" prefix="plan_price" tag="span"
                        default-classes="text-3xl font-black text-zinc-900 dark:text-white">
                        {{ $plan['price'] }}<x-dl.wrapper slug="__SLUG__" prefix="plan_period" tag="span"
                            default-classes="text-sm font-normal text-zinc-400">/mo</x-dl.wrapper>
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="plan_cta" tag="a" :featured="$isFeatured"
                        href="{{ $plan['cta_url'] ?? '#' }}"
                        default-classes="px-6 py-2.5 rounded-lg text-sm font-semibold border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                        default-featured-classes="px-6 py-2.5 rounded-lg text-sm font-semibold bg-primary text-white hover:bg-primary/90 transition-colors">
                        {{ $plan['cta'] ?? 'Get Started' }}
                    </x-dl.wrapper>
                </x-dl.group>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
