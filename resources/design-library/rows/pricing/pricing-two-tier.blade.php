{{--
@name Pricing - Two Tier
@description Two-option pricing side by side, ideal for free vs paid plans.
@sort 30
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-4xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Choose Your Plan"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Start free, upgrade when you need more."
        default-classes="text-center text-lg text-zinc-500 dark:text-zinc-400 mb-12" />
    <x-dl.grid slug="__SLUG__" prefix="plans"
        default-grid-classes="grid md:grid-cols-2 gap-8"
        default-items='[{"name":"Free","price":"$0","period":"forever","desc":"Everything you need to get started.","features":"3 projects|1GB storage|Community support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Pro","price":"$19","period":"per month","desc":"For professionals who need more power.","features":"Unlimited projects|50GB storage|Priority support|Advanced analytics|Custom domain","cta":"Start Free Trial","cta_url":"#","toggle_featured":"1"}]'>
        @dlItems('__SLUG__', 'plans', $plans, '[{"name":"Free","price":"$0","period":"forever","desc":"Everything you need to get started.","features":"3 projects|1GB storage|Community support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Pro","price":"$19","period":"per month","desc":"For professionals who need more power.","features":"Unlimited projects|50GB storage|Priority support|Advanced analytics|Custom domain","cta":"Start Free Trial","cta_url":"#","toggle_featured":"1"}]')
        @foreach ($plans as $plan)
            @php $isFeatured = !empty($plan['toggle_featured']); @endphp
            <x-dl.card slug="__SLUG__" prefix="card" :featured="$isFeatured"
                default-classes="rounded-card p-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700"
                default-featured-classes="rounded-card p-10 bg-primary text-white ring-2 ring-primary">
                <x-dl.wrapper slug="__SLUG__" prefix="card_name" tag="h3" :featured="$isFeatured"
                    default-classes="text-xl font-bold text-zinc-900 dark:text-white"
                    default-featured-classes="text-xl font-bold text-white">
                    {{ $plan['name'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="card_price_block" default-classes="mt-4 flex items-end gap-1">
                    <x-dl.wrapper slug="__SLUG__" prefix="card_price" tag="span" :featured="$isFeatured"
                        default-classes="text-5xl font-black text-zinc-900 dark:text-white"
                        default-featured-classes="text-5xl font-black text-white">
                        {{ $plan['price'] }}
                    </x-dl.wrapper>
                    <x-dl.wrapper slug="__SLUG__" prefix="card_period" tag="span" :featured="$isFeatured"
                        default-classes="text-sm text-zinc-400 mb-2"
                        default-featured-classes="text-sm text-white/70 mb-2">
                        {{ $plan['period'] }}
                    </x-dl.wrapper>
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="card_desc" tag="p" :featured="$isFeatured"
                    default-classes="mt-3 text-sm text-zinc-500 dark:text-zinc-400"
                    default-featured-classes="mt-3 text-sm text-white/80">
                    {{ $plan['desc'] }}
                </x-dl.wrapper>
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
