<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Simple, transparent pricing for individuals, agencies, and enterprises. Start free, scale as you grow.'])] #[Title('Pricing — WebProCMS')] class extends Component {
}; ?>

<div>
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Simple pricing</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            Start free.<br>Scale when you're ready.
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto">
            No hidden fees. No per-seat surprises. Pay for what you need and upgrade any time.
        </p>
    </section>

    {{-- Pricing cards --}}
    <section class="mb-24">
        <div class="grid md:grid-cols-3 gap-6">
            {{-- Starter --}}
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8 flex flex-col">
                <p class="text-xs font-semibold uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A] mb-3">Starter</p>
                <div class="mb-6">
                    <span class="text-4xl font-semibold">Free</span>
                    <span class="text-[#706f6c] dark:text-[#A1A09A] text-sm ml-1">forever</span>
                </div>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-8 leading-normal">
                    Perfect for side projects and personal sites. Get up and running in minutes.
                </p>
                <ul class="space-y-3 mb-10 flex-1">
                    @foreach (['1 website', '5 pages', '10 blog posts', 'Basic SEO fields', 'Media library (1 GB)', 'Community support'] as $feature)
                        <li class="flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            <flux:icon name="check" class="size-4 text-primary dark:text-primary-surface shrink-0" />
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-block text-center px-6 py-2.5 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm text-sm font-medium leading-normal transition-all">
                        Get started free
                    </a>
                @endif
            </div>

            {{-- Pro --}}
            <div class="bg-primary dark:bg-primary-surface rounded-lg p-8 flex flex-col relative">
                <span class="absolute top-4 right-4 text-xs font-semibold px-2.5 py-0.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-full">Most popular</span>
                <p class="text-xs font-semibold uppercase tracking-wider text-primary-foreground/70 dark:text-primary/70 mb-3">Pro</p>
                <div class="mb-6">
                    <span class="text-4xl font-semibold text-primary-foreground dark:text-primary">$29</span>
                    <span class="text-primary-foreground/70 dark:text-primary/70 text-sm ml-1">/ month</span>
                </div>
                <p class="text-sm text-primary-foreground/80 dark:text-primary/80 mb-8 leading-normal">
                    For freelancers and small agencies managing client sites professionally.
                </p>
                <ul class="space-y-3 mb-10 flex-1">
                    @foreach (['Up to 10 websites', 'Unlimited pages & posts', 'Full SEO toolkit', 'Media library (20 GB)', 'Custom shortcodes', 'Location management', 'Priority email support'] as $feature)
                        <li class="flex items-center gap-2 text-sm text-primary-foreground/90 dark:text-primary/90">
                            <flux:icon name="check" class="size-4 text-primary-foreground dark:text-primary shrink-0" />
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-block text-center px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
                        Start free trial
                    </a>
                @endif
            </div>

            {{-- Enterprise --}}
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8 flex flex-col">
                <p class="text-xs font-semibold uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A] mb-3">Enterprise</p>
                <div class="mb-6">
                    <span class="text-4xl font-semibold">Custom</span>
                </div>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-8 leading-normal">
                    For large agencies and organisations with complex, multi-site requirements.
                </p>
                <ul class="space-y-3 mb-10 flex-1">
                    @foreach (['Unlimited websites', 'Unlimited everything', 'Dedicated support', 'SLA guarantee', 'Custom integrations', 'Onboarding & training', 'Self-hosted option'] as $feature)
                        <li class="flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            <flux:icon name="check" class="size-4 text-primary dark:text-primary-surface shrink-0" />
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('contact') }}" class="inline-block text-center px-6 py-2.5 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm text-sm font-medium leading-normal transition-all">
                    Talk to us
                </a>
            </div>
        </div>
    </section>

    {{-- Feature comparison note --}}
    <section class="mb-24 text-center">
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm mb-2">All plans include SSL, automatic backups, and uptime monitoring.</p>
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">
            Questions about which plan is right for you?
            <a href="{{ route('contact') }}" class="text-primary dark:text-primary-surface hover:underline">Get in touch</a>.
        </p>
    </section>

    {{-- FAQ --}}
    <section class="mb-8">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Common questions</span>
            <h2 class="text-3xl font-semibold leading-tight">Pricing FAQ.</h2>
        </div>
        <div class="max-w-2xl mx-auto space-y-4">
            @foreach ([
                ['Can I switch plans?', 'Yes. Upgrade or downgrade at any time. Changes take effect at the start of your next billing cycle.'],
                ['Is there a free trial on Pro?', 'Pro includes a 14-day free trial with no credit card required. Full access, no limits.'],
                ['What happens if I exceed my plan limits?', 'We\'ll notify you before any disruption. You can upgrade instantly from the dashboard.'],
                ['Do you offer annual billing?', 'Yes — pay annually and save 20% compared to monthly billing.'],
            ] as [$q, $a])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                    <h3 class="font-semibold mb-2">{{ $q }}</h3>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-normal">{{ $a }}</p>
                </div>
            @endforeach
        </div>
    </section>
</div>
