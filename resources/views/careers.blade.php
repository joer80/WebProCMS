<x-layouts::public title="Careers" description="Join a team dedicated to delivering exceptional care. Explore open positions and build a career you're proud of.">
    {{-- Hero --}}
    <section class="text-center py-16 lg:py-24">
        <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-4">Join our team</span>
        <h1 class="text-5xl lg:text-6xl font-semibold leading-tight mb-6">
            A career worth<br>showing up for.
        </h1>
        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] leading-normal max-w-xl mx-auto mb-10">
            We're growing and looking for talented people who are passionate about patient care, community health, and making a real difference every single day.
        </p>
        <a href="#openings" class="inline-block px-6 py-2.5 bg-primary dark:bg-primary-surface text-primary-foreground dark:text-primary rounded-sm text-sm font-medium leading-normal hover:bg-primary-hover dark:hover:bg-primary-foreground transition-all">
            View open positions
        </a>
    </section>

    {{-- Culture --}}
    <section class="mb-24 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Our culture</span>
            <h2 class="text-3xl font-semibold leading-tight mb-4">People-first, always.</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal mb-4">
                We believe that great patient care starts with great employee care. That means competitive pay, genuine flexibility, and a team that has your back.
            </p>
            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-normal">
                Our clinics are fast-paced, collaborative, and purpose-driven. Whether you're a clinician, administrator, or support staff, your work matters here.
            </p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach ([
                ['Competitive pay', 'Salaries benchmarked above market, with performance reviews twice a year.'],
                ['Full benefits', 'Medical, dental, vision, and 401(k) with employer match from day one.'],
                ['Flexible scheduling', 'Full-time, part-time, and PRN options across all departments.'],
                ['Growth paths', 'Tuition reimbursement, CME allowances, and clear promotion tracks.'],
            ] as [$title, $desc])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-5">
                    <p class="font-semibold text-sm mb-1">{{ $title }}</p>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-xs leading-normal">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Open positions --}}
    <section id="openings" class="mb-24">
        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold tracking-widest uppercase text-[#706f6c] dark:text-[#A1A09A] mb-3">Open positions</span>
            <h2 class="text-3xl font-semibold leading-tight">We're hiring.</h2>
        </div>
        <div class="space-y-4">
            @foreach ([
                ['Medical Assistant', 'Clinical', 'Austin, TX', 'Full-time'],
                ['Registered Nurse — Urgent Care', 'Clinical', 'Houston, TX', 'Full-time'],
                ['Phlebotomist / Lab Technician', 'Clinical', 'Multiple locations', 'Full-time & Part-time'],
                ['Front Desk Coordinator', 'Administration', 'Austin, TX', 'Full-time'],
                ['Occupational Health Specialist', 'Clinical', 'Dallas, TX', 'Full-time'],
                ['Billing & Coding Specialist', 'Operations', 'Remote', 'Full-time'],
            ] as [$title, $dept, $location, $type])
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div>
                            <h3 class="font-semibold mb-1">{{ $title }}</h3>
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $dept }}</span>
                                <span class="text-xs text-[#e3e3e0] dark:text-[#3E3E3A]">·</span>
                                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                    <flux:icon name="map-pin" class="inline size-3.5 -mt-0.5 mr-0.5" />{{ $location }}
                                </span>
                                <span class="text-xs text-[#e3e3e0] dark:text-[#3E3E3A]">·</span>
                                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $type }}</span>
                            </div>
                        </div>
                        <a href="{{ route('contact') }}" class="shrink-0 px-4 py-1.5 text-sm font-medium border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-primary dark:text-primary-surface rounded-sm transition-colors">
                            Apply
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mb-8 bg-primary dark:bg-primary-foreground rounded-lg p-12 lg:p-16 text-center">
        <h2 class="text-3xl font-semibold text-primary-foreground dark:text-primary leading-tight mb-4">Don't see the right fit?</h2>
        <p class="text-[#A1A09A] dark:text-[#706f6c] leading-normal mb-8 max-w-md mx-auto">
            We're always looking for great people. Send us your résumé and we'll reach out when a matching role opens up.
        </p>
        <a href="{{ route('contact') }}" class="inline-block px-6 py-2.5 bg-primary-foreground dark:bg-primary text-primary dark:text-primary-foreground rounded-sm text-sm font-medium leading-normal hover:bg-neutral-100 dark:hover:bg-primary-hover transition-all">
            Send a general application
        </a>
    </section>
</x-layouts::public>
