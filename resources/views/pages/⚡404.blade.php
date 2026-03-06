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
</div>
