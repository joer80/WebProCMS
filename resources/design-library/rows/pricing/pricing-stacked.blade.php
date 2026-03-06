{{--
@name Pricing - Stacked
@description Vertically stacked plans with expandable feature details.
@sort 90
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-3xl mx-auto"
    x-data="{ open: null }">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Choose a Plan"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Click any plan to see what's included."
        default-classes="text-center text-lg text-zinc-500 dark:text-zinc-400 mb-12" />
    <x-dl.grid slug="__SLUG__" prefix="plans"
        default-grid-classes="space-y-4"
        default-items='[{"name":"Free","price":"$0","period":"forever","desc":"Get started at no cost","features":"3 projects|1 user|Community support","toggle_featured":""},{"name":"Pro","price":"$29","period":"per month","desc":"Everything you need to grow","features":"Unlimited projects|10 users|Priority support|Analytics|API access","toggle_featured":"1"},{"name":"Enterprise","price":"Custom","period":"contact us","desc":"For teams that need more","features":"Unlimited everything|Unlimited users|Dedicated support|Custom integrations|SLA","toggle_featured":""}]'>
        @dlItems('__SLUG__', 'plans', $plans, '[{"name":"Free","price":"$0","period":"forever","desc":"Get started at no cost","features":"3 projects|1 user|Community support","toggle_featured":""},{"name":"Pro","price":"$29","period":"per month","desc":"Everything you need to grow","features":"Unlimited projects|10 users|Priority support|Analytics|API access","toggle_featured":"1"},{"name":"Enterprise","price":"Custom","period":"contact us","desc":"For teams that need more","features":"Unlimited everything|Unlimited users|Dedicated support|Custom integrations|SLA","toggle_featured":""}]')
        @foreach ($plans as $i => $plan)
            @php $isFeatured = !empty($plan['toggle_featured']); @endphp
            <x-dl.card slug="__SLUG__" prefix="plan_card" :featured="$isFeatured"
                default-classes="rounded-card border border-zinc-200 dark:border-zinc-700 overflow-hidden"
                default-featured-classes="rounded-card border-2 border-primary overflow-hidden"
                @click="open = open === {{ $i }} ? null : {{ $i }}">
                <x-dl.group slug="__SLUG__" prefix="plan_header"
                    default-classes="flex items-center justify-between p-6 cursor-pointer">
                    <x-dl.group slug="__SLUG__" prefix="plan_info"
                        default-classes="flex items-center gap-4">
                        <x-dl.group slug="__SLUG__" prefix="plan_text"
                            default-classes="">
                            <x-dl.wrapper slug="__SLUG__" prefix="plan_name" tag="h3" :featured="$isFeatured"
                                default-classes="font-semibold text-zinc-900 dark:text-white"
                                default-featured-classes="font-semibold text-primary">
                                {{ $plan['name'] }}
                            </x-dl.wrapper>
                            <x-dl.wrapper slug="__SLUG__" prefix="plan_desc" tag="p"
                                default-classes="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
                                {{ $plan['desc'] }}
                            </x-dl.wrapper>
                        </x-dl.group>
                    </x-dl.group>
                    <x-dl.group slug="__SLUG__" prefix="plan_price_group"
                        default-classes="flex items-center gap-4">
                        <x-dl.wrapper slug="__SLUG__" prefix="plan_price" tag="span"
                            default-classes="text-2xl font-black text-zinc-900 dark:text-white">
                            {{ $plan['price'] }}<x-dl.wrapper slug="__SLUG__" prefix="plan_period" tag="span"
                                default-classes="text-xs font-normal text-zinc-400 ml-1">{{ $plan['period'] }}</x-dl.wrapper>
                        </x-dl.wrapper>
                        <x-dl.icon slug="__SLUG__" prefix="plan_chevron" name="chevron-down"
                            default-classes="size-5 text-zinc-400 transition-transform duration-200"
                            x-bind:class="open === {{ $i }} ? 'rotate-180' : ''" />
                    </x-dl.group>
                </x-dl.group>
                <div x-show="open === {{ $i }}"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    @click.stop>
                    <x-dl.wrapper slug="__SLUG__" prefix="plan_body"
                        default-classes="border-t border-zinc-100 dark:border-zinc-800 p-6 grid grid-cols-2 gap-3">
                        @foreach (array_filter(array_map('trim', explode('|', $plan['features'] ?? ''))) as $feature)
                            <x-dl.wrapper slug="__SLUG__" prefix="plan_feature_item"
                                default-classes="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                                <x-dl.icon slug="__SLUG__" prefix="feature_check" name="check-circle:solid"
                                    default-classes="size-4 shrink-0 text-primary" />
                                {{ $feature }}
                            </x-dl.wrapper>
                        @endforeach
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="plan_cta_wrapper"
                        default-classes="px-6 pb-6">
                        <x-dl.wrapper slug="__SLUG__" prefix="plan_cta" tag="a" :featured="$isFeatured"
                            href="#"
                            default-classes="block text-center px-4 py-3 rounded-lg font-semibold text-sm border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                            default-featured-classes="block text-center px-4 py-3 rounded-lg font-semibold text-sm bg-primary text-white hover:bg-primary/90 transition-colors">
                            Get Started with {{ $plan['name'] }}
                        </x-dl.wrapper>
                    </x-dl.wrapper>
                </div>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
