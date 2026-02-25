<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>
<div>{{-- ROW:start:hero-dd317W --}}
<section class="py-20 px-6 bg-white dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
            <h1 class="text-5xl font-bold text-zinc-900 dark:text-white leading-tight">
                {{ content('hero-dd317W', 'headline', 'Build Something Amazing') }}
            </h1>
            <p class="mt-6 text-lg text-zinc-500 dark:text-zinc-400">
                {{ content('hero-dd317W', 'subheadline', 'Describe your product or service here. Keep it concise and focused on the value you deliver to customers.') }}
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                <a
                    href="{{ content('hero-dd317W', 'primary_cta_url', '#') }}"
                    @if(content('hero-dd317W', 'primary_cta_new_tab', '', 'toggle')) target="_blank" rel="noopener noreferrer" @endif
                    class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
                >
                    {{ content('hero-dd317W', 'primary_cta', 'Start Free Trial') }}
                </a>
                @if(content('hero-dd317W', 'show_secondary_cta', '1', 'toggle'))
                <a
                    href="{{ content('hero-dd317W', 'secondary_cta_url', '#') }}"
                    @if(content('hero-dd317W', 'secondary_cta_new_tab', '', 'toggle')) target="_blank" rel="noopener noreferrer" @endif
                    class="px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors"
                >
                    {{ content('hero-dd317W', 'secondary_cta', 'Watch Demo →') }}
                </a>
                @endif
            </div>
        </div>
        <div class="rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center">
            @php $heroImage = content('hero-dd317W', 'image', '', 'image'); @endphp
            @if ($heroImage)
                <img src="{{ $heroImage }}" alt="{{ content('hero-dd317W', 'image_alt', '') }}" class="w-full h-full object-cover">
            @else
                <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image / Video</span>
            @endif
        </div>
    </div>
</section>
{{-- ROW:end:hero-dd317W --}}
</div>
