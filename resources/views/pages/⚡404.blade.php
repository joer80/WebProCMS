<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['status' => 'unlisted'])] #[Title('404')] class extends Component {
}; ?>
<div>{{-- ROW:start:not-found-simple:Xzy1bv --}}
<x-dl.section slug="not-found-simple:Xzy1bv"
    default-section-classes="min-h-screen bg-white dark:bg-zinc-900 flex items-center justify-center px-6"
    default-container-classes="text-center">
        <x-dl.wrapper slug="not-found-simple:Xzy1bv" prefix="error_code"
            default-classes="text-8xl font-black text-zinc-200 dark:text-zinc-700">
            404
        </x-dl.wrapper>
        <x-dl.heading slug="not-found-simple:Xzy1bv" prefix="headline" default="Page Not Found"
            default-tag="h1"
            default-classes="font-heading mt-4 text-3xl font-bold text-zinc-900 dark:text-white" />
        <x-dl.subheadline slug="not-found-simple:Xzy1bv" prefix="subheadline" default="Sorry, we couldn't find the page you're looking for. It may have been moved or deleted."
            default-classes="mt-4 text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto" />
        <x-dl.buttons slug="not-found-simple:Xzy1bv"
            default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
            default-primary-label="Go Home"
            default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
            default-secondary-label="Contact Support"
            default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" />
</x-dl.section>
{{-- ROW:end:not-found-simple:Xzy1bv --}}

{{-- ROW:start:cta-banner:pMHQ2m --}}
<x-dl.section slug="cta-banner:pMHQ2m"
    default-section-classes="bg-primary py-section-banner px-6 text-center"
    default-container-classes="max-w-3xl mx-auto">
        <x-dl.heading slug="cta-banner:pMHQ2m" prefix="headline" default="Ready to Get Started?"
            default-tag="h2"
            default-classes="font-heading text-4xl font-bold text-white" />
        <x-dl.subheadline slug="cta-banner:pMHQ2m" prefix="subheadline" default="Join thousands of satisfied customers today."
            default-classes="mt-4 text-lg text-white/80" />
        <x-dl.buttons slug="cta-banner:pMHQ2m"
            default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
            default-primary-label="Start Free Trial"
            default-primary-classes="px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors"
            default-secondary-label="Talk to Sales"
            default-secondary-classes="px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors" />
</x-dl.section>
{{-- ROW:end:cta-banner:pMHQ2m --}}
</div>
