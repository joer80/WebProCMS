<x-layouts::public title="Employer Services" description="Occupational health, workers' comp, and drug testing programs designed to keep your workforce healthy and your business protected.">
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Workplace health</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            Protect your team.<br>Protect your business.
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto mb-10">
            We partner with employers to deliver fast, compliant occupational health services — from pre-employment physicals to injury management and drug testing programs.
        </p>
        <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
            Set up an employer account
        </a>
    </section>

    {{-- Core services --}}
    <section class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Employer programs</span>
            <h2 class="text-3xl font-semibold leading-tight">Everything your workforce needs.</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            @foreach ([
                ['Workers\' Compensation', 'briefcase', 'Fast, accurate injury assessment and treatment that gets employees back to work safely. We coordinate directly with your insurance carrier and provide return-to-work documentation.', ['Injury evaluation & treatment', 'Work-status reports', 'Return-to-work coordination', 'Case management support']],
                ['Drug & Alcohol Testing', 'beaker', 'DOT and non-DOT compliant testing programs with rapid, reliable results. Pre-employment, random, reasonable-suspicion, and post-accident testing available.', ['Urine, oral fluid & hair testing', 'DOT-certified collection sites', 'MRO review & reporting', 'Random pool management']],
                ['Pre-Employment Physicals', 'heart', 'Customisable exams that meet OSHA, DOT, and industry-specific requirements. Available same-day at all locations.', ['Standard & DOT physicals', 'Fit-for-duty assessments', 'Audiometry & vision testing', 'Respiratory fit testing']],
                ['Occupational Health Programs', 'shield-check', 'Proactive wellness and compliance programs that reduce claims, lower costs, and keep your workforce healthy long-term.', ['OSHA compliance assistance', 'Annual wellness exams', 'Vaccination programs', 'Health risk assessments']],
            ] as [$title, $icon, $desc, $bullets])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <flux:icon :name="$icon" class="size-6 text-[#706f6c] dark:text-[#A1A09A]" />
                        <h3 class="font-semibold text-lg">{{ $title }}</h3>
                    </div>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-5">{{ $desc }}</p>
                    <ul class="space-y-2">
                        @foreach ($bullets as $bullet)
                            <li class="flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                <flux:icon name="check" class="size-4 text-primary dark:text-primary-surface shrink-0" />
                                {{ $bullet }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Why us --}}
    <section class="mb-24 grid sm:grid-cols-3 gap-6 text-center">
        @foreach ([
            ['clock', 'Fast turnaround', 'Most results returned within 24–48 hours, with stat reporting available for urgent needs.'],
            ['building-office', 'Multi-location', 'Send employees to any of our clinics — no pre-authorisation required with an employer account.'],
            ['document-chart-bar', 'Employer portal', 'Track results, manage testing pools, and download compliance reports from one dashboard.'],
        ] as [$icon, $title, $desc])
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6 flex flex-col items-center gap-3 text-center">
                <flux:icon :name="$icon" class="size-7 text-[#706f6c] dark:text-[#A1A09A]" />
                <p class="font-semibold">{{ $title }}</p>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] leading-normal">{{ $desc }}</p>
            </div>
        @endforeach
    </section>

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Ready to set up your employer account?</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            Account setup is free. Our team will work with you to build a program that fits your workforce and budget.
        </p>
        <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
            Contact our employer team
        </a>
    </section>
</x-layouts::public>
