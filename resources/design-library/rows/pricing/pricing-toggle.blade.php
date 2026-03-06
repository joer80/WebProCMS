{{--
@name Pricing - Monthly/Yearly Toggle
@description Pricing cards with an Alpine.js monthly/yearly billing toggle.
@sort 60
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-5xl mx-auto"
    x-data="{ yearly: false }">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Pricing"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Save up to 34% with annual billing."
        default-classes="text-center text-lg text-zinc-500 dark:text-zinc-400 mb-8" />
    <x-dl.wrapper slug="__SLUG__" prefix="toggle_wrapper"
        default-classes="flex items-center justify-center gap-4 mb-12">
        <x-dl.wrapper slug="__SLUG__" prefix="toggle_label_monthly" tag="span"
            default-classes="text-sm font-medium text-zinc-700 dark:text-zinc-300"
            x-bind:class="!yearly ? 'text-primary' : ''">
            Monthly
        </x-dl.wrapper>
        <button @click="yearly = !yearly"
            x-bind:class="yearly ? 'bg-primary' : 'bg-zinc-300 dark:bg-zinc-600'"
            class="relative w-10 h-5 rounded-full transition-colors focus:outline-none">
            <span class="absolute top-0.5 left-0.5 size-4 bg-white rounded-full shadow transition-transform"
                x-bind:class="yearly ? 'translate-x-5' : ''"></span>
        </button>
        <x-dl.wrapper slug="__SLUG__" prefix="toggle_label_yearly" tag="span"
            default-classes="text-sm font-medium text-zinc-700 dark:text-zinc-300"
            x-bind:class="yearly ? 'text-primary' : ''">
            Yearly
            <x-dl.wrapper slug="__SLUG__" prefix="toggle_badge" tag="span"
                default-classes="ml-1.5 text-xs bg-green-100 text-green-700 px-1.5 py-0.5 rounded-full font-semibold">
                Save 34%
            </x-dl.wrapper>
        </x-dl.wrapper>
    </x-dl.wrapper>
    <x-dl.grid slug="__SLUG__" prefix="plans"
        default-grid-classes="grid md:grid-cols-3 gap-8"
        default-items='[{"name":"Starter","monthly":"$9","yearly":"$6","desc":"For individuals","features":"5 projects|10GB storage|Email support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Pro","monthly":"$29","yearly":"$19","desc":"For teams","features":"Unlimited projects|100GB storage|Priority support|Analytics","cta":"Start Free Trial","cta_url":"#","toggle_featured":"1"},{"name":"Enterprise","monthly":"$99","yearly":"$69","desc":"For large orgs","features":"Everything|Dedicated support|Custom integrations|SLA","cta":"Contact Sales","cta_url":"#","toggle_featured":""}]'>
        @dlItems('__SLUG__', 'plans', $plans, '[{"name":"Starter","monthly":"$9","yearly":"$6","desc":"For individuals","features":"5 projects|10GB storage|Email support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Pro","monthly":"$29","yearly":"$19","desc":"For teams","features":"Unlimited projects|100GB storage|Priority support|Analytics","cta":"Start Free Trial","cta_url":"#","toggle_featured":"1"},{"name":"Enterprise","monthly":"$99","yearly":"$69","desc":"For large orgs","features":"Everything|Dedicated support|Custom integrations|SLA","cta":"Contact Sales","cta_url":"#","toggle_featured":""}]')
        @foreach ($plans as $plan)
            @php $isFeatured = !empty($plan['toggle_featured']); @endphp
            <x-dl.card slug="__SLUG__" prefix="card" :featured="$isFeatured"
                default-classes="rounded-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700"
                default-featured-classes="rounded-card p-8 bg-primary text-white ring-2 ring-primary">
                <x-dl.wrapper slug="__SLUG__" prefix="card_name" tag="h3" :featured="$isFeatured"
                    default-classes="text-lg font-semibold text-zinc-900 dark:text-white"
                    default-featured-classes="text-lg font-semibold text-white">
                    {{ $plan['name'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="card_desc" tag="p" :featured="$isFeatured"
                    default-classes="mt-1 text-sm text-zinc-500 dark:text-zinc-400"
                    default-featured-classes="mt-1 text-sm text-white/70">
                    {{ $plan['desc'] }}
                </x-dl.wrapper>
                <x-dl.group slug="__SLUG__" prefix="card_price"
                    default-classes="mt-6 text-4xl font-bold text-zinc-900 dark:text-white">
                    <span x-show="!yearly">{{ $plan['monthly'] }}</span>
                    <span x-show="yearly">{{ $plan['yearly'] }}</span>
                    <x-dl.wrapper slug="__SLUG__" prefix="card_price_period" tag="span" :featured="$isFeatured"
                        default-classes="text-base font-normal text-zinc-400"
                        default-featured-classes="text-base font-normal text-white/70">/mo</x-dl.wrapper>
                </x-dl.group>
                <x-dl.group slug="__SLUG__" prefix="card_features_list" tag="ul"
                    default-classes="mt-6 space-y-3">
                    @foreach (array_filter(array_map('trim', explode('|', $plan['features'] ?? ''))) as $feature)
                        <x-dl.wrapper slug="__SLUG__" prefix="card_feature_item" tag="li" :featured="$isFeatured"
                            default-classes="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300"
                            default-featured-classes="flex items-center gap-2 text-sm text-white/90">
                            <x-dl.icon slug="__SLUG__" prefix="card_feature_icon" name="check"
                                :featured="$isFeatured"
                                default-classes="size-4 shrink-0 text-primary"
                                default-featured-classes="size-4 shrink-0 text-white" />
                            {{ $feature }}
                        </x-dl.wrapper>
                    @endforeach
                </x-dl.group>
                <x-dl.wrapper slug="__SLUG__" prefix="card_cta" tag="a" :featured="$isFeatured"
                    href="{{ $plan['cta_url'] ?? '#' }}"
                    default-classes="mt-8 block text-center px-4 py-3 rounded-lg font-semibold text-sm bg-primary text-white hover:bg-primary/90 transition-colors"
                    default-featured-classes="mt-8 block text-center px-4 py-3 rounded-lg font-semibold text-sm bg-white text-primary hover:bg-zinc-100 transition-colors">
                    {{ $plan['cta'] ?? 'Get Started' }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
