<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test 2')] class extends Component {
}; ?>
<div>{{-- ROW:start:hero-centered:SNvunK --}}
<x-dl.section slug="hero-centered:SNvunK"
    default-section-classes="py-section-hero px-6 bg-white dark:bg-zinc-900 text-center"
    default-container-classes="max-w-3xl mx-auto">
        <x-dl.subheadline slug="hero-centered:SNvunK" prefix="badge" tag="span" default="Welcome"
            default-classes="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6" />
        <x-dl.heading slug="hero-centered:SNvunK" prefix="headline" default="Your Headline Goes Here"
            default-tag="h1"
            default-classes="font-heading text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight" />
        <x-dl.subheadline slug="hero-centered:SNvunK" prefix="subheadline" default="A compelling subheadline that explains what you do and why it matters to your audience."
            default-classes="mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed" />
        <x-dl.buttons slug="hero-centered:SNvunK"
            default-wrapper-classes="mt-10 flex flex-wrap items-center justify-center gap-4"
            default-primary-label="Get Started"
            default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="Learn More"
            default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
</x-dl.section>
{{-- ROW:end:hero-centered:SNvunK --}}

{{-- ROW:start:features-grid:L1ylAH --}}
<x-dl.section slug="features-grid:L1ylAH"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.wrapper slug="features-grid:L1ylAH" prefix="header_wrapper" default-classes="text-center mb-16">
        <x-dl.heading slug="features-grid:L1ylAH" prefix="headline" default="Everything You Need"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="features-grid:L1ylAH" prefix="subheadline" default="Powerful features designed to help you succeed."
            default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    </x-dl.wrapper>
    <x-dl.grid slug="features-grid:L1ylAH" prefix="features"
        default-grid-classes="grid md:grid-cols-3 gap-8"
        default-items='[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Detailed Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Easy to Customize","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]'>
        @dlItems('features-grid:L1ylAH', 'features', $features, '[{"icon":"bolt","title":"Lightning Fast","desc":"Optimized for speed at every level of the stack."},{"icon":"shield-check","title":"Secure by Default","desc":"Enterprise-grade security built into every feature."},{"icon":"chart-bar","title":"Detailed Analytics","desc":"Gain insight into every aspect of your business."},{"icon":"adjustments-horizontal","title":"Easy to Customize","desc":"Tailor the platform to your exact requirements."},{"icon":"globe-alt","title":"Global Scale","desc":"Built to handle millions of users worldwide."},{"icon":"chat-bubble-left-right","title":"24/7 Support","desc":"Our team is always here when you need us."}]')
        @foreach ($features as $feature)
            <x-dl.card slug="features-grid:L1ylAH" prefix="feature_card"
                default-classes="p-6 rounded-card border border-zinc-200 dark:border-zinc-700 hover:border-primary/40 transition-colors">
                <x-dl.icon slug="features-grid:L1ylAH" prefix="icon" name="{{ $feature['icon'] }}"
                    default-wrapper-classes="mb-4 text-primary"
                    default-classes="size-8" />
                <x-dl.wrapper slug="features-grid:L1ylAH" prefix="feature_title" tag="h3"
                    default-classes="text-lg font-semibold text-zinc-900 dark:text-white mb-2">
                    {{ $feature['title'] }}
                </x-dl.wrapper>
                <x-dl.wrapper slug="features-grid:L1ylAH" prefix="feature_desc" tag="p"
                    default-classes="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">
                    {{ $feature['desc'] }}
                </x-dl.wrapper>
            </x-dl.card>
        @endforeach
    </x-dl.grid>
</x-dl.section>
{{-- ROW:end:features-grid:L1ylAH --}}

{{-- ROW:start:cta-banner:iJaYuJ --}}
<x-dl.section slug="cta-banner:iJaYuJ"
    default-section-classes="bg-primary py-section-banner px-6 text-center"
    default-container-classes="max-w-3xl mx-auto">
        <x-dl.heading slug="cta-banner:iJaYuJ" prefix="headline" default="Ready to Get Started?"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-white" />
        <x-dl.subheadline slug="cta-banner:iJaYuJ" prefix="subheadline" default="Join thousands of satisfied customers today."
            default-classes="mt-4 text-lg text-white/80" />
        <x-dl.buttons slug="cta-banner:iJaYuJ"
            default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
            default-primary-label="Start Free Trial"
            default-primary-classes="px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors"
            default-secondary-label="Talk to Sales"
            default-secondary-classes="px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors" />
</x-dl.section>
{{-- ROW:end:cta-banner:iJaYuJ --}}
</div>
