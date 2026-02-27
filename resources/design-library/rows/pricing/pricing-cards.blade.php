{{--
@name Pricing - Cards
@description Three-tier pricing cards with features list and CTA buttons.
@sort 10
--}}
<section class="py-20 px-6 bg-zinc-50 dark:bg-zinc-950">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white">Simple, Transparent Pricing</h2>
            <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">No hidden fees. Cancel anytime.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach ([['name' => 'Starter', 'price' => '$9', 'desc' => 'Perfect for individuals', 'features' => ['5 projects', '10GB storage', 'Email support'], 'featured' => false], ['name' => 'Pro', 'price' => '$29', 'desc' => 'Great for small teams', 'features' => ['Unlimited projects', '100GB storage', 'Priority support', 'Analytics'], 'featured' => true], ['name' => 'Enterprise', 'price' => '$99', 'desc' => 'For large organizations', 'features' => ['Unlimited everything', 'Dedicated support', 'Custom integrations', 'SLA guarantee'], 'featured' => false]] as $plan)
                <div class="rounded-2xl p-8 {{ $plan['featured'] ? 'bg-primary text-white ring-2 ring-primary' : 'bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700' }}">
                    <h3 class="text-lg font-semibold {{ $plan['featured'] ? 'text-white' : 'text-zinc-900 dark:text-white' }}">{{ $plan['name'] }}</h3>
                    <p class="mt-1 text-sm {{ $plan['featured'] ? 'text-white/70' : 'text-zinc-500 dark:text-zinc-400' }}">{{ $plan['desc'] }}</p>
                    <div class="mt-6 text-4xl font-bold {{ $plan['featured'] ? 'text-white' : 'text-zinc-900 dark:text-white' }}">
                        {{ $plan['price'] }}<span class="text-base font-normal {{ $plan['featured'] ? 'text-white/70' : 'text-zinc-400' }}">/mo</span>
                    </div>
                    <ul class="mt-6 space-y-3">
                        @foreach ($plan['features'] as $feature)
                            <li class="flex items-center gap-2 text-sm {{ $plan['featured'] ? 'text-white/90' : 'text-zinc-600 dark:text-zinc-300' }}">
                                <svg class="size-4 shrink-0 {{ $plan['featured'] ? 'text-white' : 'text-primary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="#" class="mt-8 block text-center px-4 py-3 rounded-lg font-semibold text-sm transition-colors {{ $plan['featured'] ? 'bg-white text-primary hover:bg-zinc-100' : 'bg-primary text-white hover:bg-primary/90' }}">
                        Get Started
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
