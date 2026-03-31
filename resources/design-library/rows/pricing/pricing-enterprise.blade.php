{{--
@name Pricing - Enterprise
@description Self-serve pricing cards plus an enterprise contact block.
@sort 80
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-zinc-50 dark:bg-zinc-950"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Start Small, Think Big"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white text-center mb-4" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Self-serve plans for individuals and teams, plus custom pricing for large organizations."
        default-classes="text-center text-lg text-zinc-500 dark:text-zinc-400 mb-12" />
    <x-dl.grid slug="__SLUG__" prefix="plans"
        default-grid-classes="grid md:grid-cols-2 gap-8 mb-8"
        default-items='[{"name":"Starter","price":"$9","desc":"For individuals and freelancers","features":"5 projects|10GB storage|Email support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Pro","price":"$29","desc":"For growing teams","features":"Unlimited projects|100GB storage|Priority support|Analytics","cta":"Start Free Trial","cta_url":"#","toggle_featured":"1"}]'>
        @dlItems('__SLUG__', 'plans', $plans, '[{"name":"Starter","price":"$9","desc":"For individuals and freelancers","features":"5 projects|10GB storage|Email support","cta":"Get Started","cta_url":"#","toggle_featured":""},{"name":"Pro","price":"$29","desc":"For growing teams","features":"Unlimited projects|100GB storage|Priority support|Analytics","cta":"Start Free Trial","cta_url":"#","toggle_featured":"1"}]')
        @foreach ($plans as $plan)
            @php $isFeatured = !empty($plan['toggle_featured']); @endphp
            <x-dl.card slug="__SLUG__" prefix="card" :featured="$isFeatured"
                data-editor-item-index="{{ $loop->index }}"
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
                    {{ $plan['price'] }}<x-dl.wrapper slug="__SLUG__" prefix="card_price_period" tag="span" :featured="$isFeatured"
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
    <x-dl.wrapper slug="__SLUG__" prefix="enterprise_block"
        default-classes="rounded-card p-8 bg-zinc-900 dark:bg-zinc-800 text-white flex flex-col md:flex-row items-center justify-between gap-6">
        <x-dl.group slug="__SLUG__" prefix="enterprise_text"
            default-classes="">
            <x-dl.wrapper slug="__SLUG__" prefix="enterprise_title" tag="h3"
                default-classes="text-xl font-bold text-white">
                Enterprise
            </x-dl.wrapper>
            <x-dl.wrapper slug="__SLUG__" prefix="enterprise_desc" tag="p"
                default-classes="mt-1 text-zinc-400 text-sm max-w-md">
                Custom contracts, SLAs, dedicated infrastructure, and a dedicated customer success manager.
            </x-dl.wrapper>
        </x-dl.group>
        <x-dl.wrapper slug="__SLUG__" prefix="enterprise_cta" tag="a"
            href="#"
            default-classes="shrink-0 px-6 py-3 rounded-lg font-semibold text-sm bg-white text-zinc-900 hover:bg-zinc-100 transition-colors">
            Contact Sales
        </x-dl.wrapper>
    </x-dl.wrapper>
</x-dl.section>
