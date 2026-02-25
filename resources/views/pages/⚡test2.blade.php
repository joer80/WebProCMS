<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test2')] class extends Component {
}; ?>
<div>{{-- ROW:start:hero-bIQYX5 --}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            <h1 class="text-5xl font-bold text-zinc-900 dark:text-white leading-tight">
                Build Something Amazing
            </h1>
            <p class="mt-6 text-lg text-zinc-500 dark:text-zinc-400">
                Describe your product or service here. Keep it concise and focused on the value you deliver to customers.
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="#" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors">
                    Start Free Trial
                </a>
                <a href="#" class="px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors">
                    Watch Demo →
                </a>
            </div>
        </div>
        <div class="rounded-2xl bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center">
            <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
        </div>
    </div>
</section>
{{-- ROW:end:hero-bIQYX5 --}}
</div>
