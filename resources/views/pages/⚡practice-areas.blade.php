<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['description' => 'Our experienced attorneys handle a wide range of legal matters. Explore our practice areas to find the right representation for your case.'])] #[Title('Practice Areas')] class extends Component {
}; ?>

<div>
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Legal expertise</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            Experienced attorneys.<br>Every practice area.
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto mb-10">
            Our team has decades of combined experience representing clients across a broad range of legal matters — from personal disputes to complex commercial litigation.
        </p>
        <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
            Schedule a consultation
        </a>
    </section>

    {{-- Practice area cards --}}
    <section class="mb-24">
        <div class="grid md:grid-cols-2 gap-6">
            @foreach ([
                ['Personal Injury', 'scale', 'We fight for maximum compensation for accident victims — including car crashes, slip and falls, workplace injuries, and medical malpractice cases.', ['Auto accidents', 'Slip & fall', 'Medical malpractice', 'Workers\' compensation']],
                ['Family Law', 'home', 'Compassionate representation for divorce, child custody, adoption, and other family legal matters. We protect what matters most.', ['Divorce & separation', 'Child custody', 'Adoption', 'Prenuptial agreements']],
                ['Criminal Defense', 'shield-check', 'Aggressive defense for misdemeanor and felony charges. We protect your rights at every stage of the criminal process.', ['DUI/DWI', 'Drug charges', 'Assault & battery', 'White collar crimes']],
                ['Corporate & Business', 'building-office', 'Legal counsel for businesses of all sizes — from formation and contracts to disputes and M&A transactions.', ['Business formation', 'Contract drafting', 'Commercial litigation', 'Mergers & acquisitions']],
                ['Estate Planning', 'document-text', 'Protect your legacy and ensure your wishes are honoured. We help you plan for the future with confidence.', ['Wills & trusts', 'Power of attorney', 'Probate', 'Asset protection']],
                ['Real Estate', 'home-modern', 'Transactional and litigation support for buyers, sellers, landlords, and tenants navigating complex property matters.', ['Residential closings', 'Commercial leases', 'Title disputes', 'Zoning issues']],
            ] as [$title, $icon, $desc, $subItems])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon :name="$icon" class="size-6 text-[#706f6c] dark:text-[#A1A09A]" />
                        <h3 class="font-semibold text-lg">{{ $title }}</h3>
                    </div>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-6">{{ $desc }}</p>
                    <ul class="grid grid-cols-2 gap-2">
                        @foreach ($subItems as $item)
                            <li class="flex items-center gap-1.5 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                <span class="size-1 rounded-full bg-[#706f6c] dark:bg-[#A1A09A] shrink-0"></span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Why us --}}
    <section class="mb-24 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Why our firm</span>
            <h2 class="text-3xl font-semibold leading-tight mb-4">Results-driven representation, client-first service.</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-6">
                We don't treat clients like case numbers. From your first consultation to final resolution, you'll have a dedicated attorney who knows your case and answers your calls.
            </p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach ([
                ['500+', 'Cases resolved'],
                ['25+', 'Years of experience'],
                ['98%', 'Client satisfaction'],
                ['$50M+', 'Recovered for clients'],
            ] as [$stat, $label])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 text-center">
                    <p class="text-3xl font-semibold mb-1">{{ $stat }}</p>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Have a legal question?</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            Schedule a free initial consultation with one of our attorneys today. We'll review your situation and explain your options clearly.
        </p>
        <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
            Book a free consultation
        </a>
    </section>
</div>
