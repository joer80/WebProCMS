<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>
<div>{{-- ROW:start:hero-E7nVCw --}}
<section class="py-24 px-6 bg-white dark:bg-zinc-900 text-center">
    <div class="max-w-3xl mx-auto">
        <span class="inline-block px-3 py-1 text-xs font-semibold tracking-widest uppercase bg-primary/10 text-primary rounded-full mb-6">Welcome</span>
        <h1 class="text-5xl sm:text-6xl font-bold text-zinc-900 dark:text-white leading-tight">
            Your Headline Goes Here
        </h1>
        <p class="mt-6 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed">
            A compelling subheadline that explains what you do and why it matters to your audience.
        </p>
        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a href="#" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                Get Started
            </a>
            <a href="#" class="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                Learn More
            </a>
        </div>
    </div>
</section>
{{-- ROW:end:hero-E7nVCw --}}

{{-- ROW:start:legacy-ORBGTi --}}
<div>
    <p>Test page — use the Design Library editor to insert rows here.</p>
</div>
{{-- ROW:end:legacy-ORBGTi --}}
</div>
