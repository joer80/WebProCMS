<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Explore WebProCMS services: visual content editor, page builder, media library, SEO tools, content scheduling, and team & role management.'])] #[Title('Services — WebProCMS')] class extends Component {
}; ?>

<div>
    {{-- Hero --}}
    <section class="mb-16 text-center">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">What we offer</span>
        <h1 class="text-4xl font-semibold leading-tight mb-4">Everything you need to manage your web.</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-lg leading-normal max-w-2xl mx-auto">
            From content creation to multi-site publishing, WebProCMS gives you the building blocks to build, manage, and grow any web project.
        </p>
    </section>

    {{-- Core services --}}
    <section class="mb-24">
        <div class="grid md:grid-cols-2 gap-6">
            @foreach ([
                [
                    'icon' => 'document-text',
                    'title' => 'Visual Content Editor',
                    'description' => 'Write and edit with a powerful block-based editor. Rich text, images, embeds, and custom blocks — all in one clean interface. No developer needed.',
                    'features' => ['Block-based editing', 'Rich text formatting', 'Media embeds & custom blocks', 'Version history'],
                    'route' => 'services.content-editor',
                ],
                [
                    'icon' => 'squares-2x2',
                    'title' => 'Page Builder',
                    'description' => 'Build beautiful pages without writing code. Drag-and-drop blocks, adjust layouts, and preview changes in real time before publishing.',
                    'features' => ['Drag-and-drop blocks', 'Responsive layouts', 'Live preview', 'Reusable component library'],
                    'route' => null,
                ],
                [
                    'icon' => 'photo',
                    'title' => 'Media Library',
                    'description' => 'Upload, organise, and optimise your images, documents, and files. Find anything in seconds with full-text search and folder organisation.',
                    'features' => ['Smart upload & tagging', 'Folder organisation', 'Image optimisation', 'CDN-ready delivery'],
                    'route' => null,
                ],
                [
                    'icon' => 'magnifying-glass',
                    'title' => 'SEO & Meta Management',
                    'description' => 'Control every SEO detail — meta titles, descriptions, canonical URLs, Open Graph tags, and structured data — all from one place, per page.',
                    'features' => ['Per-page meta control', 'Open Graph & Twitter Cards', 'Canonical URL management', 'Sitemap generation'],
                    'route' => null,
                ],
                [
                    'icon' => 'calendar',
                    'title' => 'Content Scheduling',
                    'description' => 'Write now, publish later. Schedule posts and pages to go live at exactly the right moment. Manage your entire content calendar from one dashboard.',
                    'features' => ['Scheduled publishing', 'Content calendar view', 'Draft management', 'Auto-expiry controls'],
                    'route' => null,
                ],
                [
                    'icon' => 'users',
                    'title' => 'Team & Role Management',
                    'description' => 'Invite collaborators, assign roles, and keep your team working efficiently. Fine-grained permissions let you control who can do what, site by site.',
                    'features' => ['Custom roles & permissions', 'Invitation system', 'Activity & audit logs', 'SSO support'],
                    'route' => null,
                ],
            ] as $service)
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-md bg-[#f5f5f3] dark:bg-[#1D1D1B] flex items-center justify-center shrink-0">
                            <flux:icon :name="$service['icon']" class="text-[#706f6c] dark:text-[#A1A09A]" />
                        </div>
                        <h3 class="font-semibold">{{ $service['title'] }}</h3>
                    </div>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm leading-normal mb-4">{{ $service['description'] }}</p>
                    <ul class="space-y-1.5 mb-4">
                        @foreach ($service['features'] as $feature)
                            <li class="flex items-center gap-2 text-sm text-primary dark:text-primary-surface">
                                <flux:icon name="check-circle" variant="micro" class="text-green-500 dark:text-green-400 shrink-0" />
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                    @if ($service['route'])
                        <a href="{{ route($service['route']) }}" class="inline-flex items-center gap-1 text-sm font-medium text-primary dark:text-primary-surface hover:underline">
                            Learn more
                            <flux:icon name="arrow-right" variant="micro" />
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    {{-- Pricing --}}
    <section class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Pricing</span>
            <h2 class="text-3xl font-semibold leading-tight">Simple, transparent pricing.</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] mt-3 leading-normal max-w-lg mx-auto">
                Start for free and upgrade as your team grows. No hidden fees, no surprise charges.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-6 items-start">
            @foreach ([
                [
                    'name' => 'Starter',
                    'price' => 'Free',
                    'period' => 'forever',
                    'description' => 'For individuals and small projects.',
                    'features' => ['1 website', '50 pages', '500 MB media storage', 'Visual content editor', 'Community support'],
                    'cta' => 'Get started',
                    'variant' => 'default',
                ],
                [
                    'name' => 'Pro',
                    'price' => '$29',
                    'period' => '/ month',
                    'description' => 'For growing teams and agencies.',
                    'features' => ['Up to 5 websites', 'Unlimited pages', '10 GB media storage', 'API & headless access', 'Content scheduling', 'Priority support'],
                    'cta' => 'Start free trial',
                    'variant' => 'primary',
                ],
                [
                    'name' => 'Enterprise',
                    'price' => 'Custom',
                    'period' => '',
                    'description' => 'For large teams with advanced needs.',
                    'features' => ['Unlimited websites', 'SSO & audit logs', 'Custom integrations', 'Dedicated support', 'SLA guarantee', 'Custom roles & permissions'],
                    'cta' => 'Talk to us',
                    'variant' => 'default',
                ],
            ] as $plan)
                @php $isPrimary = $plan['variant'] === 'primary'; @endphp
                <div @class([
                    'rounded-lg p-6',
                    'bg-primary dark:bg-primary-foreground shadow-[inset_0px_0px_0px_1px_rgba(255,255,255,0.1)] dark:shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]' => $isPrimary,
                    'bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]' => ! $isPrimary,
                ])>
                    <p @class([
                        'text-xs font-semibold tracking-widest uppercase mb-1',
                        'text-[#A1A09A] dark:text-[#706f6c]' => $isPrimary,
                        'text-[#706f6c] dark:text-[#A1A09A]' => ! $isPrimary,
                    ])>{{ $plan['name'] }}</p>
                    <div class="flex items-end gap-1 mb-1">
                        <span @class([
                            'text-3xl font-semibold',
                            'text-primary-foreground dark:text-primary' => $isPrimary,
                            'text-primary dark:text-primary-surface' => ! $isPrimary,
                        ])>{{ $plan['price'] }}</span>
                        @if ($plan['period'])
                            <span @class([
                                'text-sm mb-1',
                                'text-[#A1A09A] dark:text-[#706f6c]' => $isPrimary,
                                'text-[#706f6c] dark:text-[#A1A09A]' => ! $isPrimary,
                            ])>{{ $plan['period'] }}</span>
                        @endif
                    </div>
                    <p @class([
                        'text-sm leading-normal mb-5',
                        'text-[#A1A09A] dark:text-[#706f6c]' => $isPrimary,
                        'text-[#706f6c] dark:text-[#A1A09A]' => ! $isPrimary,
                    ])>{{ $plan['description'] }}</p>
                    <ul class="space-y-2 mb-6">
                        @foreach ($plan['features'] as $feature)
                            <li class="flex items-center gap-2 text-sm">
                                <flux:icon name="check-circle" variant="micro" @class([
                                    'shrink-0',
                                    'text-green-400 dark:text-green-500' => $isPrimary,
                                    'text-green-500 dark:text-green-400' => ! $isPrimary,
                                ]) />
                                <span @class([
                                    'text-primary-foreground dark:text-primary' => $isPrimary,
                                    'text-primary dark:text-primary-surface' => ! $isPrimary,
                                ])>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @if ($plan['name'] === 'Enterprise')
                        <a href="{{ route('contact') }}" @class([
                            'block text-center px-4 py-2.5 rounded-sm text-sm font-medium leading-normal transition-all',
                            'bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground hover:bg-neutral-100 dark:hover:bg-primary-hover' => $isPrimary,
                            'border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface' => ! $isPrimary,
                        ])>{{ $plan['cta'] }}</a>
                    @elseif (Route::has('register'))
                        <a href="{{ route('register') }}" @class([
                            'block text-center px-4 py-2.5 rounded-sm text-sm font-medium leading-normal transition-all',
                            'bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground hover:bg-neutral-100 dark:hover:bg-primary-hover' => $isPrimary,
                            'border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface' => ! $isPrimary,
                        ])>{{ $plan['cta'] }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Ready to manage your web better?</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            Sign up for free and launch your first site in under a minute. No credit card required.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
                    Get started free
                </a>
            @endif
            <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 border border-[#3E3E3A] dark:border-[#19140035] hover:border-[#62605b] text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal transition-all">
                Talk to us
            </a>
        </div>
    </section>
</div>
