{{--
@name Pricing - Dark
@description Dark background pricing cards with glowing featured plan.
@sort 40
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-900"
    default-container-classes="max-w-5xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Pricing That Scales With You"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="No contracts. No surprise fees."
        default-classes="text-center text-lg text-zinc-400 mb-12" />
    <x-dl.grid slug="__SLUG__" prefix="plans"
        default-grid-classes="grid md:grid-cols-3 gap-8"
        default-items='[{"name":"Starter","price":"$9","desc":"For solo creators","features":"5 projects|10GB storage|Email support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Growth","price":"$39","desc":"For growing teams","features":"Unlimited projects|100GB storage|Priority support|Team collaboration","cta":"Start Free Trial","cta_url":"#","toggle_featured":"1"},{"name":"Scale","price":"$99","desc":"For enterprises","features":"Unlimited everything|Dedicated support|Custom integrations|99.99% SLA","cta":"Contact Sales","cta_url":"#","toggle_featured":""}]'>
        @dlItems('__SLUG__', 'plans', $plans, '[{"name":"Starter","price":"$9","desc":"For solo creators","features":"5 projects|10GB storage|Email support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Growth","price":"$39","desc":"For growing teams","features":"Unlimited projects|100GB storage|Priority support|Team collaboration","cta":"Start Free Trial","cta_url":"#","toggle_featured":"1"},{"name":"Scale","price":"$99","desc":"For enterprises","features":"Unlimited everything|Dedicated support|Custom integrations|99.99% SLA","cta":"Contact Sales","cta_url":"#","toggle_featured":""}]')
        @foreach ($plans as $plan)
            @php $isFeatured = !empty($plan['toggle_featured']); @endphp
            <x-dl.card slug="__SLUG__" prefix="card" :featured="$isFeatured"
                data-editor-item-index="{{ $loop->index }}"
                default-classes="rounded-card p-8 bg-zinc-800 border border-zinc-700"
                default-featured-classes="rounded-card p-8 bg-primary ring-2 ring-primary shadow-[0_0_40px_rgba(var(--color-primary),0.3)]">
                <x-dl.wrapper slug="__SLUG__" prefix="card_name" tag="h3" :featured="$isFeatured"
                    default-classes="text-lg font-semibold text-white"
                    default-featured-classes="text-lg font-semibold text-white">
                    {{ $plan['name'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="__SLUG__" prefix="card_desc" tag="p" :featured="$isFeatured"
                    default-classes="mt-1 text-sm text-zinc-400"
                    default-featured-classes="mt-1 text-sm text-white/70">
                    {{ $plan['desc'] }}
                </x-dl.wrapper>
                <x-dl.group slug="__SLUG__" prefix="card_price"
                    default-classes="mt-6 text-4xl font-bold text-white">
                    {{ $plan['price'] }}<x-dl.wrapper slug="__SLUG__" prefix="card_price_period" tag="span"
                        default-classes="text-base font-normal text-zinc-400">/mo</x-dl.wrapper>
                </x-dl.group>
                <x-dl.group slug="__SLUG__" prefix="card_features_list" tag="ul"
                    default-classes="mt-6 space-y-3">
                    @foreach (array_filter(array_map('trim', explode('|', $plan['features'] ?? ''))) as $feature)
                        <x-dl.wrapper slug="__SLUG__" prefix="card_feature_item" tag="li" :featured="$isFeatured"
                            default-classes="flex items-center gap-2 text-sm text-zinc-300"
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
                    default-classes="mt-8 block text-center px-4 py-3 rounded-lg font-semibold text-sm bg-white/10 text-white hover:bg-white/20 transition-colors"
                    default-featured-classes="mt-8 block text-center px-4 py-3 rounded-lg font-semibold text-sm bg-white text-primary hover:bg-zinc-100 transition-colors">
                    {{ $plan['cta'] ?? 'Get Started' }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
