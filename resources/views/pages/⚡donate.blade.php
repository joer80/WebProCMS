<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Your donation makes a direct difference. Every dollar supports our mission and the communities we serve.'])] #[Title('Donate')] class extends Component {
}; ?>

<div>
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Support our mission</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            Every gift<br>changes a life.
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto mb-10">
            Your generosity funds programs that provide education, meals, shelter, and opportunity to those who need it most. 100% of every dollar goes directly to our programs.
        </p>
    </section>

    {{-- Impact stats --}}
    <section class="mb-24 grid sm:grid-cols-3 gap-6 text-center">
        @foreach ([
            ['12,400+', 'Families served last year'],
            ['93%', 'Of donations go to programs'],
            ['18', 'Years serving our community'],
        ] as [$stat, $label])
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8">
                <p class="text-4xl font-semibold mb-2">{{ $stat }}</p>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $label }}</p>
            </div>
        @endforeach
    </section>

    {{-- Donation tiers --}}
    <section class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Choose your impact</span>
            <h2 class="text-3xl font-semibold leading-tight">How much would you like to give?</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach ([
                ['$25', 'Provides school supplies for one child for a full semester.'],
                ['$50', 'Feeds a family of four for two weeks through our food pantry.'],
                ['$100', 'Covers a month of after-school tutoring for one student.'],
                ['Custom', 'Give any amount that feels right. Every dollar counts.'],
            ] as [$amount, $impact])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 flex flex-col">
                    <p class="text-2xl font-semibold mb-3">{{ $amount }}</p>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-normal flex-1">{{ $impact }}</p>
                    <button class="mt-6 w-full px-4 py-2 text-sm font-medium border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm transition-colors">
                        Give {{ $amount }}
                    </button>
                </div>
            @endforeach
        </div>
        <p class="text-center text-xs text-[#706f6c] dark:text-[#A1A09A]">
            We are a registered 501(c)(3) nonprofit. Donations are tax-deductible to the full extent of the law.
        </p>
    </section>

    {{-- Where your money goes --}}
    <section class="mb-24 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Transparency</span>
            <h2 class="text-3xl font-semibold leading-tight mb-4">Every dollar is accounted for.</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
                We publish an annual report each year so donors can see exactly where their money goes. Our commitment to transparency has earned us a four-star Charity Navigator rating.
            </p>
        </div>
        <div class="space-y-3">
            @foreach ([
                ['Program services', '93%'],
                ['Administration', '5%'],
                ['Fundraising', '2%'],
            ] as [$label, $pct])
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-[#1b1b18] dark:text-[#EDEDEC]">{{ $label }}</span>
                        <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ $pct }}</span>
                    </div>
                    <div class="h-2 bg-[#f5f5f3] dark:bg-[#1D1D1B] rounded-full overflow-hidden">
                        <div class="h-full bg-primary dark:bg-primary-surface rounded-full" style="width: {{ $pct }}"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Ready to make a difference?</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            Your one-time or recurring gift fuels our work all year long. Join thousands of donors who are changing lives.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            <a href="{{ route('donate') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
                Donate now
            </a>
            <a href="{{ route('volunteer') }}" class="inline-block px-6 py-2.5 border border-[#3E3E3A] dark:border-[#19140035] hover:border-[#62605b] text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal transition-all">
                Volunteer instead
            </a>
        </div>
    </section>
</div>
